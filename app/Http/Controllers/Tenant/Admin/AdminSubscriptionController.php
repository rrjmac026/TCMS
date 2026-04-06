<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\DiscountUsage;
use App\Models\SubscriptionPlan;
use App\Models\TenantSubscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * AdminSubscriptionController
 *
 * Handles the tenant/admin subscription upgrade page.
 *
 * KEY RULES enforced here:
 *  - A discount (automatic OR code-based) ONLY affects the price that is displayed
 *    and recorded. It NEVER changes the tenant's plan by itself.
 *  - The tenant's plan only changes when they explicitly confirm an upgrade via
 *    the confirmUpgrade() / upgrade() action.
 *  - Automatic discounts are surfaced to the tenant without any code entry.
 *  - Code-based discounts require the tenant to type the promo code manually.
 */
class AdminSubscriptionController extends Controller
{
    /**
     * Show the subscription upgrade page.
     *
     * For each upgradeable plan we resolve the best active automatic discount
     * (if any) so the blade can show the discounted price directly on the card.
     */
    public function index()
    {
        $tenant      = tenancy()->tenant;
        $currentPlan = $tenant->subscription ?? 'basic';
        $planSlugs   = ['basic', 'standard', 'premium'];

        // Load all active plans ordered by sort_order
        $plans = SubscriptionPlan::active()->get();

        // For each plan, find the best active automatic discount (may be null)
        $autoDiscounts = [];
        foreach ($plans as $plan) {
            $autoDiscounts[$plan->slug] = Discount::bestAutomaticFor($plan->slug);
        }

        return view('tenants.admin.subscription.upgrade', compact(
            'plans',
            'currentPlan',
            'planSlugs',
            'autoDiscounts',
        ));
    }

    /**
     * AJAX: validate a manually entered promo code.
     *
     * Returns pricing info only. NEVER changes the plan.
     */
    public function validateCode(Request $request): JsonResponse
    {
        $request->validate([
            'code'      => ['required', 'string'],
            'plan_slug' => ['required', 'in:basic,standard,premium'],
        ]);

        $discount = Discount::findValidCode($request->code, $request->plan_slug);

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

    /**
     * AJAX: resolve price for a plan, optionally applying an automatic discount.
     *
     * Called when the tenant opens the upgrade modal so we can show the
     * auto-discounted price immediately (before any code is entered).
     *
     * Returns pricing info only. NEVER changes the plan.
     */
    public function resolvePrice(Request $request): JsonResponse
    {
        $request->validate([
            'plan_slug' => ['required', 'in:basic,standard,premium'],
        ]);

        $planModel = SubscriptionPlan::where('slug', $request->plan_slug)->firstOrFail();
        $base      = (float) $planModel->price;

        $autoDiscount = Discount::bestAutomaticFor($request->plan_slug);
        $final        = $autoDiscount ? $autoDiscount->applyTo($base) : $base;
        $saved        = $base - $final;

        return response()->json([
            'base_price'       => $base,
            'final_price'      => $final,
            'discount_amount'  => $saved,
            'has_auto_discount'=> $autoDiscount !== null,
            'auto_label'       => $autoDiscount?->label,
            'auto_value'       => $autoDiscount?->formatted_value,
        ]);
    }

    /**
     * Confirm upgrade — the ONLY action that changes the tenant's plan.
     *
     * Discount logic:
     *  1. If a valid promo code is supplied → use it (code-based discount).
     *  2. Else if an automatic discount exists for the plan → apply it.
     *  3. Otherwise → full price.
     *
     * The plan slug supplied must be strictly higher than the current plan.
     */
    public function upgrade(Request $request): JsonResponse
    {
        $tenant      = tenancy()->tenant;
        $currentPlan = $tenant->subscription ?? 'basic';
        $planSlugs   = ['basic', 'standard', 'premium'];

        $request->validate([
            'subscription'  => ['required', 'in:basic,standard,premium'],
            'discount_code' => ['nullable', 'string'],
        ]);

        $newPlanSlug = $request->subscription;

        // Enforce upgrade-only (no downgrades)
        if (array_search($newPlanSlug, $planSlugs) <= array_search($currentPlan, $planSlugs)) {
            return response()->json(['success' => false, 'message' => 'You can only upgrade to a higher plan.'], 422);
        }

        $planModel = SubscriptionPlan::where('slug', $newPlanSlug)->firstOrFail();
        $base      = (float) $planModel->price;
        $price     = $base;
        $discount  = null;

        // 1. Try promo code first
        if (! empty($request->discount_code)) {
            $codeDiscount = Discount::findValidCode($request->discount_code, $newPlanSlug);
            if ($codeDiscount) {
                $discount = $codeDiscount;
                $price    = $discount->applyTo($base);
            }
        }

        // 2. Fall back to automatic discount if no code was used
        if (! $discount) {
            $autoDiscount = Discount::bestAutomaticFor($newPlanSlug);
            if ($autoDiscount) {
                $discount = $autoDiscount;
                $price    = $discount->applyTo($base);
            }
        }

        $expiresAt = $planModel->getExpiresAt();

        DB::transaction(function () use ($tenant, $newPlanSlug, $planModel, $discount, $base, $price, $expiresAt) {
            $discountUsageId = null;

            if ($discount) {
                $usage = DiscountUsage::create([
                    'discount_id'     => $discount->id,
                    'tenant_id'       => $tenant->id,
                    'action'          => $discount->is_automatic ? 'auto_discount' : 'promo_code',
                    'plan_slug'       => $newPlanSlug,
                    'original_price'  => $base,
                    'discount_amount' => $discount->discountAmount($base),
                    'final_price'     => $price,
                    'applied_by'      => auth()->id(),
                ]);
                $discountUsageId = $usage->id;
            }

            TenantSubscription::create([
                'tenant_id'         => $tenant->id,
                'plan_slug'         => $newPlanSlug,
                'discount_usage_id' => $discountUsageId,
                'amount_paid'       => $price,
                'action'            => 'tenant_upgrade',
                'starts_at'         => now(),
                'expires_at'        => $expiresAt,
                'applied_by'        => auth()->id(),
            ]);

            // ← This is the ONLY place where the tenant's plan slug is changed
            //   from the tenant/admin side. Discount codes alone never reach here.
            $tenant->subscription = $newPlanSlug;
            $tenant->expires_at   = $expiresAt;
            $tenant->status       = 'approved';
            $tenant->is_active    = true;
            $tenant->save();
        });

        return response()->json([
            'success'  => true,
            'plan'     => $planModel->name,
            'expires'  => $expiresAt->format('M d, Y'),
        ]);
    }
}