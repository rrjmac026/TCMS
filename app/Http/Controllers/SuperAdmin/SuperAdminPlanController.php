<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\Tenant;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

        $tenant   = Tenant::findOrFail($data['tenant_id']);
        $plan     = config('plans.' . $data['plan_slug']);
        $price    = (float) $plan['price'];
        $discount = null;

        // Validate and apply discount if provided
        if (!empty($data['discount_code'])) {
            $discount = Discount::where('code', strtoupper($data['discount_code']))->first();

            if (!$discount || !$discount->isValidFor($data['plan_slug'])) {
                return back()->withErrors(['discount_code' => 'Invalid or inapplicable discount code.']);
            }

            $price = $discount->applyTo($price);
        }

        // Set plan on tenant
        $tenant->subscription = $data['plan_slug'];
        $tenant->expires_at   = now()->addDays($plan['duration_days']);
        $tenant->status       = 'approved';
        $tenant->is_active    = true;
        $tenant->save();

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