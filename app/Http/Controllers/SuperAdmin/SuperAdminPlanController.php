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

/**
 * SuperAdminPlanController
 *
 * IMPORTANT — Two completely separate responsibilities:
 *
 * 1. PLAN ASSIGNMENT (applyToTenant)
 *    The superadmin can assign/change a tenant's plan (Basic → Standard → Premium).
 *    This is the ONLY action that changes a tenant's subscription plan.
 *    A discount code can optionally be attached to record the discounted price paid,
 *    but applying a discount code here does NOT by itself change any plan.
 *
 * 2. DISCOUNT MANAGEMENT (store/update/destroyDiscount)
 *    Discounts are purely pricing tools. Two subtypes:
 *    a) Automatic — shown on plan cards with no code required (date-range based).
 *    b) Code-based — tenant must type the code manually on the upgrade page.
 *       Code-based discounts can optionally be restricted to specific tenants.
 *    Neither type changes a tenant's plan on its own.
 */
class SuperAdminPlanController extends Controller
{
    // ── Index ─────────────────────────────────────────────────────────────────

    public function index()
    {
        $plans     = SubscriptionPlan::active()->get();
        $discounts = Discount::latest()->get();
        $tenants   = Tenant::orderBy('name')->get();

        return view('superadmin.plans.index', compact('plans', 'discounts', 'tenants'));
    }

    // ── Plan Assignment ───────────────────────────────────────────────────────

    /**
     * Assign a plan to a tenant (and optionally record a discounted price).
     *
     * KEY RULE: Only this method changes a tenant's subscription plan.
     * A discount code attached here only affects the amount_paid that is recorded.
     * The plan change happens regardless of whether a discount is used.
     */
    public function applyToTenant(Request $request)
    {
        $data = $request->validate([
            'tenant_id'     => ['required', 'exists:tenants,id'],
            'plan_slug'     => ['required', 'in:basic,standard,premium'],
            'discount_code' => ['nullable', 'string'],
        ]);

        $tenant    = Tenant::findOrFail($data['tenant_id']);
        $planModel = SubscriptionPlan::where('slug', $data['plan_slug'])->firstOrFail();
        $price     = (float) $planModel->price;
        $discount  = null;

        // Resolve discount if a code was provided — only affects recorded price
        if (! empty($data['discount_code'])) {
            $discount = Discount::findValidCode($data['discount_code'], $data['plan_slug'], $tenant->id);

            if (! $discount) {
                return back()->withErrors(['discount_code' => 'Invalid or inapplicable discount code.']);
            }

            $price = $discount->applyTo($price);
        }

        $expiresAt = $planModel->getExpiresAt();

        DB::transaction(function () use ($tenant, $data, $planModel, $discount, $price, $expiresAt) {
            $basePrice       = (float) $planModel->price;
            $discountUsageId = null;

            if ($discount) {
                $usage = DiscountUsage::create([
                    'discount_id'     => $discount->id,
                    'tenant_id'       => $tenant->id,
                    'action'          => 'superadmin_assign',
                    'plan_slug'       => $data['plan_slug'],
                    'original_price'  => $basePrice,
                    'discount_amount' => $discount->discountAmount($basePrice),
                    'final_price'     => $price,
                    'applied_by'      => auth()->id(),
                ]);
                $discountUsageId = $usage->id;
            }

            // Record the subscription history entry
            TenantSubscription::create([
                'tenant_id'         => $tenant->id,
                'plan_slug'         => $data['plan_slug'],
                'discount_usage_id' => $discountUsageId,
                'amount_paid'       => $price,
                'action'            => 'superadmin_assign',
                'starts_at'         => now(),
                'expires_at'        => $expiresAt,
                'applied_by'        => auth()->id(),
            ]);

            // This is the ONLY place a plan change is triggered by the superadmin
            $tenant->subscription = $data['plan_slug'];
            $tenant->expires_at   = $expiresAt;
            $tenant->status       = 'approved';
            $tenant->is_active    = true;
            $tenant->save();
        });

        $msg = "Plan set to {$planModel->name} for {$tenant->name}.";
        if ($discount) {
            $msg .= " Discount {$discount->code} applied — recorded price ₱" . number_format($price, 2) . '.';
        }

        return back()->with('success', $msg);
    }

    // ── Discount Management ───────────────────────────────────────────────────

    /**
     * Create a discount.
     *
     * is_automatic = true  → shown automatically on plan cards (no code entry needed)
     * is_automatic = false → tenant must enter the code manually on the upgrade page
     *
     * plan_slugs = null        → applies to all plans
     * plan_slugs = ['standard','premium'] → applies only to those plans
     *
     * tenant_ids = null        → promo code applies to any tenant (code-based only)
     * tenant_ids = ['uuid',…]  → promo code restricted to those tenants
     */
    public function storeDiscount(Request $request)
    {
        $data = $request->validate([
            'code'         => ['required_if:is_automatic,0', 'nullable', 'string', 'max:50', 'unique:discounts,code'],
            'label'        => ['required', 'string', 'max:150'],
            'type'         => ['required', 'in:percentage,fixed'],
            'value'        => ['required', 'numeric', 'min:0.01'],
            'plan_slugs'   => ['nullable', 'array'],
            'plan_slugs.*' => ['in:basic,standard,premium'],
            'tenant_ids'   => ['nullable', 'array'],
            'tenant_ids.*' => ['exists:tenants,id'],
            'valid_from'   => ['nullable', 'date'],
            'valid_until'  => ['nullable', 'date', 'after_or_equal:valid_from'],
            'is_active'    => ['boolean'],
            'is_automatic' => ['boolean'],
        ]);

        if ($data['type'] === 'percentage' && $data['value'] > 100) {
            return back()->withInput()->withErrors(['value' => 'Percentage cannot exceed 100.']);
        }

        $isAutomatic = $request->boolean('is_automatic', false);

        // Automatic discounts don't need a code — generate a placeholder so the
        // column stays non-null (it has a unique constraint)
        if ($isAutomatic) {
            $data['code']       = 'AUTO-' . strtoupper(uniqid());
            $data['tenant_ids'] = null; // tenant restriction is irrelevant for automatic discounts
        } else {
            $data['code'] = strtoupper($data['code'] ?? '');

            // Normalise tenant_ids: empty array → null (means "any tenant")
            $tenantIds          = $request->input('tenant_ids', []);
            $data['tenant_ids'] = (is_array($tenantIds) && count($tenantIds) > 0) ? $tenantIds : null;
        }

        // Normalise plan_slugs: empty array → null (means "all plans")
        $planSlugs = $request->input('plan_slugs', []);
        $data['plan_slugs']   = (is_array($planSlugs) && count($planSlugs) > 0) ? $planSlugs : null;
        $data['is_active']    = $request->boolean('is_active', true);
        $data['is_automatic'] = $isAutomatic;

        Discount::create($data);

        $typeLabel = $isAutomatic ? 'Automatic discount' : 'Promo code "' . $data['code'] . '"';
        return back()->with('success', $typeLabel . ' created successfully.');
    }

    public function updateDiscount(Request $request, Discount $discount)
    {
        $data = $request->validate([
            'code'         => [
                'nullable', 'string', 'max:50',
                Rule::unique('discounts', 'code')->ignore($discount->id),
            ],
            'label'        => ['required', 'string', 'max:150'],
            'type'         => ['required', 'in:percentage,fixed'],
            'value'        => ['required', 'numeric', 'min:0.01'],
            'plan_slugs'   => ['nullable', 'array'],
            'plan_slugs.*' => ['in:basic,standard,premium'],
            'tenant_ids'   => ['nullable', 'array'],
            'tenant_ids.*' => ['exists:tenants,id'],
            'valid_from'   => ['nullable', 'date'],
            'valid_until'  => ['nullable', 'date', 'after_or_equal:valid_from'],
            'is_active'    => ['boolean'],
            'is_automatic' => ['boolean'],
        ]);

        if ($data['type'] === 'percentage' && $data['value'] > 100) {
            return back()->withInput()->withErrors(['value' => 'Percentage cannot exceed 100.']);
        }

        $isAutomatic = $request->boolean('is_automatic', false);
        $data['is_active']    = $request->boolean('is_active', true);
        $data['is_automatic'] = $isAutomatic;

        // Normalise plan_slugs
        $planSlugs = $request->input('plan_slugs', []);
        $data['plan_slugs'] = (is_array($planSlugs) && count($planSlugs) > 0) ? $planSlugs : null;

        // Keep the auto-generated code for automatic discounts
        if ($isAutomatic) {
            unset($data['code']); // don't overwrite the AUTO-xxx code
            $data['tenant_ids'] = null; // tenant restriction is irrelevant for automatic discounts
        } else {
            $data['code'] = strtoupper($data['code'] ?? $discount->code);

            // Normalise tenant_ids
            $tenantIds          = $request->input('tenant_ids', []);
            $data['tenant_ids'] = (is_array($tenantIds) && count($tenantIds) > 0) ? $tenantIds : null;
        }

        $discount->update($data);

        return back()->with('success', 'Discount updated successfully.');
    }

    public function destroyDiscount(Discount $discount)
    {
        $label = $discount->is_automatic ? $discount->label : $discount->code;
        $discount->delete();

        return back()->with('success', 'Discount "' . $label . '" deleted.');
    }

    // ── AJAX: validate a code-based discount ─────────────────────────────────

    /**
     * Used by both superadmin Apply-to-Tenant form and tenant upgrade modal
     * to check a manually entered promo code.
     *
     * Returns pricing info only — never triggers a plan change.
     */
    public function validateCode(Request $request)
    {
        $request->validate([
            'code'      => ['required', 'string'],
            'plan_slug' => ['required', 'in:basic,standard,premium'],
        ]);

        // Pass tenant ID if available (from tenant context or explicit param)
        $tenantId = $request->input('tenant_id') ?? optional(tenancy()->tenant)->id;

        $discount = Discount::findValidCode($request->code, $request->plan_slug, $tenantId);

        if (! $discount) {
            return response()->json([
                'valid'   => false,
                'message' => 'Invalid or inapplicable promo code.',
            ]);
        }

        $planModel = SubscriptionPlan::where('slug', $request->plan_slug)->firstOrFail();
        $base      = (float) $planModel->price;
        $saved     = $discount->discountAmount($base);
        $final     = $discount->applyTo($base);

        return response()->json([
            'valid'           => true,
            'formatted_value' => $discount->formatted_value,
            'original_price'  => $base,
            'discount_amount' => $saved,
            'final_price'     => $final,
        ]);
    }
}