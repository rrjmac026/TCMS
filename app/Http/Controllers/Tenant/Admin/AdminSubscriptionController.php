<?php
// In AdminSubscriptionController — update the index() method to resolve
// automatic discounts per plan and pass them to the view.
// The rest of your upgrade() / validateCode() methods stay the same.

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\Discount;
use App\Helpers\SubscriptionHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DiscountUsage;
use App\Models\TenantSubscription;

class AdminSubscriptionController extends Controller
{
    // ── Upgrade page ──────────────────────────────────────────────────────

    public function index()
    {
        $tenant      = tenancy()->tenant;
        $currentPlan = $tenant->subscription ?? 'basic';
        $plans       = SubscriptionPlan::active()->orderBy('sort_order')->get();
        $planSlugs   = $plans->pluck('slug')->toArray();

        // Resolve the best active *automatic* discount for each plan
        // (keyed by plan slug so the Blade views can do $autoDiscounts[$plan->slug])
        $autoDiscounts = [];
        foreach ($plans as $plan) {
            $discount = Discount::where('is_active', true)
                ->where('is_automatic', true)
                ->where(function ($q) use ($plan) {
                    // null plan_slugs  = applies to all plans
                    $q->whereNull('plan_slugs')
                      ->orWhereJsonContains('plan_slugs', $plan->slug);
                })
                ->where(function ($q) {
                    $q->whereNull('valid_from')
                      ->orWhereDate('valid_from', '<=', today());
                })
                ->where(function ($q) {
                    $q->whereNull('valid_until')
                      ->orWhereDate('valid_until', '>=', today());
                })
                ->orderByDesc('value') // pick the highest-value one
                ->first();

            $autoDiscounts[$plan->slug] = $discount;
        }

        return view('tenants.admin.subscription.upgrade', compact(
            'plans',
            'currentPlan',
            'planSlugs',
            'autoDiscounts',
        ));
    }

    // ── AJAX: validate a promo code ───────────────────────────────────────

    public function validateCode(Request $request)
    {
        $request->validate([
            'code'      => ['required', 'string'],
            'plan_slug' => ['required', 'in:basic,standard,premium'],
        ]);

        $tenantId = optional(tenancy()->tenant)->id;

        $discount = Discount::findValidCode(
            $request->code,
            $request->plan_slug,
            $tenantId
        );

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

    // ── AJAX: upgrade plan ────────────────────────────────────────────────

    public function upgrade(Request $request)
    {
        $data = $request->validate([
            'subscription'  => ['required', 'in:basic,standard,premium'],
            'discount_code' => ['nullable', 'string'],
        ]);

        $tenant    = tenancy()->tenant;
        $planModel = SubscriptionPlan::where('slug', $data['subscription'])->firstOrFail();
        $price     = (float) $planModel->price;
        $discount  = null;

        // Resolve discount — only affects recorded price, not the plan change
        if (! empty($data['discount_code'])) {
            $discount = Discount::findValidCode(
                $data['discount_code'],
                $data['subscription'],
                $tenant->id
            );
            if ($discount) {
                $price = $discount->applyTo($price);
            }
        }

        // Also apply an automatic discount if no code was used
        if (! $discount) {
            $autoDiscount = Discount::where('is_active', true)
                ->where('is_automatic', true)
                ->where(function ($q) use ($data) {
                    $q->whereNull('plan_slugs')
                      ->orWhereJsonContains('plan_slugs', $data['subscription']);
                })
                ->where(function ($q) {
                    $q->whereNull('valid_from')->orWhereDate('valid_from', '<=', today());
                })
                ->where(function ($q) {
                    $q->whereNull('valid_until')->orWhereDate('valid_until', '>=', today());
                })
                ->orderByDesc('value')
                ->first();

            if ($autoDiscount) {
                $discount = $autoDiscount;
                $price    = $autoDiscount->applyTo((float) $planModel->price);
            }
        }

        $expiresAt = $planModel->getExpiresAt();

        DB::transaction(function () use ($tenant, $data, $planModel, $discount, $price, $expiresAt) {
            $basePrice       = (float) $planModel->price;
            $discountUsageId = null;

            if ($discount) {
                $usage = DiscountUsage::create([
                    'discount_id'     => $discount->id,
                    'tenant_id'       => $tenant->id,
                    'action'          => 'tenant_upgrade',
                    'plan_slug'       => $data['subscription'],
                    'original_price'  => $basePrice,
                    'discount_amount' => $discount->discountAmount($basePrice),
                    'final_price'     => $price,
                    'applied_by'      => auth()->id(),
                ]);
                $discountUsageId = $usage->id;
            }

            TenantSubscription::create([
                'tenant_id'         => $tenant->id,
                'plan_slug'         => $data['subscription'],
                'discount_usage_id' => $discountUsageId,
                'amount_paid'       => $price,
                'action'            => 'tenant_upgrade',
                'starts_at'         => now(),
                'expires_at'        => $expiresAt,
                'applied_by'        => auth()->id(),
            ]);

            // Persist the plan change on the tenant record
            $tenant->subscription = $data['subscription'];
            $tenant->expires_at   = $expiresAt;
            $tenant->save();
        });

        return response()->json(['success' => true]);
    }
}