<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\DiscountUsage;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SuperAdminPlanController extends Controller
{
    // =========================================================================
    // PLAN MANAGEMENT
    // =========================================================================

    public function index()
    {
        $plans    = SubscriptionPlan::orderBy('sort_order')->get();
        $discounts = Discount::withCount('usages')
            ->orderByDesc('created_at')
            ->get();

        $tenants = Tenant::where('status', 'approved')->orderBy('name')->get();

        // Usage stats per discount
        $totalSaved = DiscountUsage::sum('discount_amount');
        $totalUsages = DiscountUsage::count();

        return view('superadmin.plans.index', compact(
            'plans',
            'discounts',
            'tenants',
            'totalSaved',
            'totalUsages',
        ));
    }

    // ── Update a plan's editable fields ──────────────────────────────────────

    public function updatePlan(Request $request, SubscriptionPlan $plan)
    {
        $validated = $request->validate([
            'name'                   => ['required', 'string', 'max:100'],
            'description'            => ['nullable', 'string', 'max:500'],
            'price'                  => ['required', 'numeric', 'min:0'],
            'duration_days'          => ['required', 'integer', 'min:1'],
            'max_trainees'           => ['nullable', 'integer', 'min:0'],
            'max_trainers'           => ['nullable', 'integer', 'min:0'],
            'max_users'              => ['nullable', 'integer', 'min:1'],
            'max_courses'            => ['nullable', 'integer', 'min:0'],
            'max_exports_monthly'    => ['nullable', 'integer', 'min:0'],
            'allowed_export_formats' => ['nullable', 'array'],
            'allowed_export_formats.*' => ['in:csv,excel,pdf'],
            'has_assessments'        => ['boolean'],
            'has_certificates'       => ['boolean'],
            'has_custom_reports'     => ['boolean'],
            'has_branding'           => ['boolean'],
            'has_trainers'           => ['boolean'],
            'is_active'              => ['boolean'],
        ]);

        // Null-ify empty strings for nullable integer fields
        foreach (['max_trainees', 'max_trainers', 'max_users', 'max_courses', 'max_exports_monthly'] as $field) {
            if ($request->input($field) === '' || $request->input($field) === null) {
                $validated[$field] = null;
            }
        }

        // Booleans from checkboxes
        $boolFields = ['has_assessments', 'has_certificates', 'has_custom_reports', 'has_branding', 'has_trainers', 'is_active'];
        foreach ($boolFields as $f) {
            $validated[$f] = $request->boolean($f);
        }

        $validated['allowed_export_formats'] = $request->input('allowed_export_formats', []);

        $plan->update($validated);

        return redirect()->route('superadmin.plans.index')
            ->with('success', "Plan \"{$plan->name}\" updated successfully.");
    }

    // =========================================================================
    // DISCOUNT MANAGEMENT
    // =========================================================================

    public function storeDiscount(Request $request)
    {
        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:150'],
            'code'                => ['required', 'string', 'max:50', 'uppercase', 'alpha_dash', 'unique:discounts,code'],
            'type'                => ['required', 'in:percentage,fixed'],
            'value'               => ['required', 'numeric', 'min:0.01'],
            'applicable_plans'    => ['nullable', 'array'],
            'applicable_plans.*'  => ['in:basic,standard,premium'],
            'applicable_actions'  => ['nullable', 'array'],
            'applicable_actions.*'=> ['in:approve,upgrade_superadmin,upgrade_admin,renewal'],
            'tenant_id'           => ['nullable', 'exists:tenants,id'],
            'valid_from'          => ['nullable', 'date'],
            'valid_until'         => ['nullable', 'date', 'after_or_equal:valid_from'],
            'max_uses'            => ['nullable', 'integer', 'min:1'],
            'minimum_price'       => ['nullable', 'numeric', 'min:0'],
            'is_active'           => ['boolean'],
        ]);

        // Extra validation: percentage can't exceed 100
        if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
            return back()->withInput()
                ->withErrors(['value' => 'Percentage discount cannot exceed 100%.']);
        }

        $validated['is_active']   = $request->boolean('is_active', true);
        $validated['created_by']  = Auth::id();
        $validated['code']        = strtoupper($validated['code']);

        // Empty array → null (means "applies to all")
        if (empty($validated['applicable_plans']))   $validated['applicable_plans']   = null;
        if (empty($validated['applicable_actions'])) $validated['applicable_actions'] = null;

        Discount::create($validated);

        return redirect()->route('superadmin.plans.index')
            ->with('success', 'Discount code "' . $validated['code'] . '" created successfully.');
    }

    public function updateDiscount(Request $request, Discount $discount)
    {
        $validated = $request->validate([
            'name'                => ['required', 'string', 'max:150'],
            'code'                => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('discounts', 'code')->ignore($discount->id)],
            'type'                => ['required', 'in:percentage,fixed'],
            'value'               => ['required', 'numeric', 'min:0.01'],
            'applicable_plans'    => ['nullable', 'array'],
            'applicable_plans.*'  => ['in:basic,standard,premium'],
            'applicable_actions'  => ['nullable', 'array'],
            'applicable_actions.*'=> ['in:approve,upgrade_superadmin,upgrade_admin,renewal'],
            'tenant_id'           => ['nullable', 'exists:tenants,id'],
            'valid_from'          => ['nullable', 'date'],
            'valid_until'         => ['nullable', 'date', 'after_or_equal:valid_from'],
            'max_uses'            => ['nullable', 'integer', 'min:1'],
            'minimum_price'       => ['nullable', 'numeric', 'min:0'],
            'is_active'           => ['boolean'],
        ]);

        if ($validated['type'] === 'percentage' && $validated['value'] > 100) {
            return back()->withInput()
                ->withErrors(['value' => 'Percentage discount cannot exceed 100%.']);
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['code']      = strtoupper($validated['code']);

        if (empty($validated['applicable_plans']))   $validated['applicable_plans']   = null;
        if (empty($validated['applicable_actions'])) $validated['applicable_actions'] = null;

        $discount->update($validated);

        return redirect()->route('superadmin.plans.index')
            ->with('success', 'Discount "' . $discount->code . '" updated successfully.');
    }

    public function destroyDiscount(Discount $discount)
    {
        $discount->delete(); // soft delete

        return redirect()->route('superadmin.plans.index')
            ->with('success', 'Discount "' . $discount->code . '" deleted.');
    }

    // ── Apply a discount directly to a specific tenant (SuperAdmin action) ───

    public function applyDiscount(Request $request)
    {
        $validated = $request->validate([
            'discount_code' => ['required', 'string'],
            'tenant_id'     => ['required', 'exists:tenants,id'],
            'plan_slug'     => ['required', 'in:basic,standard,premium'],
            'action'        => ['required', 'in:approve,upgrade_superadmin,renewal'],
        ]);

        $discount = Discount::where('code', strtoupper($validated['discount_code']))->first();

        if (! $discount) {
            return back()->withErrors(['discount_code' => 'Discount code not found.']);
        }

        $plan = SubscriptionPlan::where('slug', $validated['plan_slug'])->first();

        if (! $plan) {
            return back()->withErrors(['plan_slug' => 'Plan not found.']);
        }

        if (! $discount->isValidFor($validated['plan_slug'], $validated['action'], $validated['tenant_id'], (float) $plan->price)) {
            return back()->withErrors(['discount_code' => 'This discount code is not valid for the selected plan, action, or tenant.']);
        }

        $tenant          = Tenant::findOrFail($validated['tenant_id']);
        $originalPrice   = (float) $plan->price;
        $discountAmount  = $discount->discountAmount($originalPrice);
        $finalPrice      = $discount->applyTo($originalPrice);

        // Record usage
        DiscountUsage::create([
            'discount_id'     => $discount->id,
            'tenant_id'       => $tenant->id,
            'action'          => $validated['action'],
            'plan_slug'       => $validated['plan_slug'],
            'original_price'  => $originalPrice,
            'discount_amount' => $discountAmount,
            'final_price'     => $finalPrice,
            'applied_by'      => Auth::id(),
        ]);

        // Increment usage counter
        $discount->increment('uses_count');

        // Apply the plan to the tenant if action is approve or upgrade
        if (in_array($validated['action'], ['approve', 'upgrade_superadmin', 'renewal'])) {
            $tenant->subscription = $validated['plan_slug'];
            $tenant->expires_at   = $plan->getExpiresAt();
            if ($validated['action'] === 'approve') {
                $tenant->status    = 'approved';
                $tenant->is_active = true;
            }
            $tenant->save();
        }

        $saved = number_format($discountAmount, 2);

        return redirect()->route('superadmin.plans.index')
            ->with('success', "Discount applied to {$tenant->name}. They saved ₱{$saved} on the " . ucfirst($validated['plan_slug']) . " plan.");
    }

    // ── Validate a discount code via AJAX ─────────────────────────────────────

    public function validateCode(Request $request)
    {
        $request->validate([
            'code'      => ['required', 'string'],
            'plan_slug' => ['required', 'in:basic,standard,premium'],
        ]);

        $discount = Discount::valid()
            ->where('code', strtoupper($request->code))
            ->first();

        if (! $discount) {
            return response()->json(['valid' => false, 'message' => 'Invalid or expired discount code.']);
        }

        $plan = SubscriptionPlan::where('slug', $request->plan_slug)->first();
        $basePrice = $plan ? (float) $plan->price : 0;

        return response()->json([
            'valid'           => true,
            'name'            => $discount->name,
            'formatted_value' => $discount->formatted_value,
            'type'            => $discount->type,
            'value'           => $discount->value,
            'original_price'  => $basePrice,
            'discount_amount' => $discount->discountAmount($basePrice),
            'final_price'     => $discount->applyTo($basePrice),
            'message'         => "Code valid! Saves {$discount->formatted_value}" . ($discount->type === 'fixed' ? '' : ' off') . '.',
        ]);
    }
}