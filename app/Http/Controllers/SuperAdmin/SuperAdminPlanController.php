<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Tenant;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\DiscountUsage;
use App\Models\TenantSubscription;
use Illuminate\Support\Facades\DB;

class SuperAdminPlanController extends Controller
{
    public function index()
    {
        $plans     = config('plans');
        $discounts = Discount::latest()->get();

        return view('superadmin.plans.index', compact('plans', 'discounts'));
    }

    // ── Discounts ─────────────────────────────────────────────────────────────

    public function storeDiscount(Request $request)
    {
        $data = $request->validate([
            'code'        => ['required', 'string', 'max:50', 'unique:discounts,code'],
            'label'       => ['required', 'string', 'max:150'],
            'type'        => ['required', 'in:percentage,fixed'],
            'value'       => ['required', 'numeric', 'min:0.01'],
            'plan_slug'   => ['nullable', 'in:basic,standard,premium'],
            'valid_from'  => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'is_active'   => ['boolean'],
        ]);

        if ($data['type'] === 'percentage' && $data['value'] > 100) {
            return back()->withInput()->withErrors(['value' => 'Percentage cannot exceed 100.']);
        }

        $data['code']      = strtoupper($data['code']);
        $data['is_active'] = $request->boolean('is_active', true);

        Discount::create($data);

        return back()->with('success', 'Discount "' . $data['code'] . '" created.');
    }

    public function updateDiscount(Request $request, Discount $discount)
    {
        $data = $request->validate([
            'code'        => ['required', 'string', 'max:50', Rule::unique('discounts', 'code')->ignore($discount->id)],
            'label'       => ['required', 'string', 'max:150'],
            'type'        => ['required', 'in:percentage,fixed'],
            'value'       => ['required', 'numeric', 'min:0.01'],
            'plan_slug'   => ['nullable', 'in:basic,standard,premium'],
            'valid_from'  => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'is_active'   => ['boolean'],
        ]);

        if ($data['type'] === 'percentage' && $data['value'] > 100) {
            return back()->withInput()->withErrors(['value' => 'Percentage cannot exceed 100.']);
        }

        $data['code']      = strtoupper($data['code']);
        $data['is_active'] = $request->boolean('is_active', true);

        $discount->update($data);

        return back()->with('success', 'Discount "' . $discount->code . '" updated.');
    }

    public function destroyDiscount(Discount $discount)
    {
        $code = $discount->code;
        $discount->delete();

        return back()->with('success', 'Discount "' . $code . '" deleted.');
    }

    // ── Apply discount + set plan to a tenant ────────────────────────────────

    public function applyToTenant(Request $request)
    {
        $data = $request->validate([
            'tenant_id'     => ['required', 'exists:tenants,id'],
            'plan_slug'     => ['required', 'in:basic,standard,premium'],
            'discount_code' => ['nullable', 'string'],
        ]);

        $tenant    = Tenant::findOrFail($data['tenant_id']);
        $plan      = config('plans.' . $data['plan_slug']);
        $planModel = SubscriptionPlan::where('slug', $data['plan_slug'])->first();
        $price     = $planModel ? (float) $planModel->price : (float) $plan['price'];
        $discount  = null;

        if (! empty($data['discount_code'])) {
            $discount = Discount::where('code', strtoupper($data['discount_code']))->first();

            if (! $discount || ! $discount->isValidFor($data['plan_slug'])) {
                return back()->withErrors(['discount_code' => 'Invalid or inapplicable discount code.']);
            }

            $price = $discount->applyTo($price);
        }

        $expiresAt = $planModel?->getExpiresAt() ?? now()->addDays($plan['duration_days']);

        DB::transaction(function () use ($tenant, $data, $planModel, $discount, $price, $expiresAt, $plan) {
            $basePrice       = $planModel ? (float) $planModel->price : (float) $plan['price'];
            $discountUsageId = null;

            if ($discount) {
                $usage = DiscountUsage::create([
                    'discount_id'     => $discount->id,
                    'tenant_id'       => $tenant->id,
                    'action'          => 'approve',
                    'plan_slug'       => $data['plan_slug'],
                    'original_price'  => $basePrice,
                    'discount_amount' => $discount->discountAmount($basePrice),
                    'final_price'     => $price,
                    'applied_by'      => auth()->id(),
                ]);
                $discountUsageId = $usage->id;
            }

            TenantSubscription::create([
                'tenant_id'         => $tenant->id,
                'plan_slug'         => $data['plan_slug'],
                'discount_usage_id' => $discountUsageId,
                'amount_paid'       => $price,
                'action'            => 'approve',
                'starts_at'         => now(),
                'expires_at'        => $expiresAt,
                'applied_by'        => auth()->id(),
            ]);

            $tenant->subscription = $data['plan_slug'];
            $tenant->expires_at   = $expiresAt;
            $tenant->status       = 'approved';
            $tenant->is_active    = true;
            $tenant->save();
        });

        $msg = "Plan set to {$plan['name']} for {$tenant->name}.";
        if ($discount) {
            $msg .= " Discount {$discount->code} applied — final price ₱" . number_format($price, 2) . '.';
        }

        return back()->with('success', $msg);
    }

    // ── AJAX discount code check ─────────────────────────────────────────────

    public function validateCode(Request $request)
    {
        $request->validate([
            'code'      => ['required', 'string'],
            'plan_slug' => ['required', 'in:basic,standard,premium'],
        ]);

        $discount = Discount::where('code', strtoupper($request->code))->first();

        if (!$discount || !$discount->isValidFor($request->plan_slug)) {
            return response()->json(['valid' => false, 'message' => 'Invalid or inapplicable code.']);
        }

        $base  = (float) config('plans.' . $request->plan_slug . '.price');
        $saved = $discount->discountAmount($base);
        $final = $discount->applyTo($base);

        return response()->json([
            'valid'           => true,
            'formatted_value' => $discount->formatted_value,
            'original_price'  => $base,
            'discount_amount' => $saved,
            'final_price'     => $final,
        ]);
    }
}