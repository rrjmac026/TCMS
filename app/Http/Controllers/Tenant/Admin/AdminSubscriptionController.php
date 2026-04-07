<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\Discount;
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
        $autoDiscounts = [];
        foreach ($plans as $plan) {
            $discount = Discount::where('is_active', true)
                ->where('is_automatic', true)
                ->where(function ($q) use ($plan) {
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
                ->orderByDesc('value')
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
        $basePrice = (float) $planModel->price;
        $price     = $basePrice;
        $discount  = null;

        // ── Resolve discount (code-based first, then automatic) ───────────
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

        if (! $discount) {
            $autoDiscount = Discount::on('mysql')
                ->where('is_active', true)
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
                $price    = $autoDiscount->applyTo($basePrice);
            }
        }

        $expiresAt       = $planModel->getExpiresAt();
        $appliedBy       = auth()->id();   // tenant admin user ID
        $discountUsageId = null;

        // ── Write discount usage to central DB ────────────────────────────
        if ($discount) {
            $usage = DiscountUsage::on('mysql')->create([
                'discount_id'     => $discount->id,
                'tenant_id'       => $tenant->id,
                'action'          => 'tenant_upgrade',
                'plan_slug'       => $data['subscription'],
                'original_price'  => $basePrice,
                'discount_amount' => $discount->discountAmount($basePrice),
                'final_price'     => $price,
                'applied_by'      => $appliedBy,
            ]);
            $discountUsageId = $usage->id;
        }

        // ── Write subscription history to central DB ──────────────────────
        TenantSubscription::on('mysql')->create([
            'tenant_id'         => $tenant->id,
            'plan_slug'         => $data['subscription'],
            'discount_usage_id' => $discountUsageId,
            'amount_paid'       => $price,
            'action'            => 'tenant_upgrade',
            'starts_at'         => now(),
            'expires_at'        => $expiresAt,
            'applied_by'        => $appliedBy,
        ]);

        // ── Update the tenant record (central DB) ─────────────────────────
        // Use DB::connection('mysql') to avoid the tenant-context transaction
        // swallowing central DB writes.
        DB::connection('mysql')->table('tenants')
            ->where('id', $tenant->id)
            ->update([
                'subscription' => $data['subscription'],
                'expires_at'   => $expiresAt,
                'updated_at'   => now(),
            ]);

        // Keep the in-memory tenant model in sync so any subsequent
        // code in this request sees the new values.
        $tenant->subscription = $data['subscription'];
        $tenant->expires_at   = $expiresAt;

        return response()->json(['success' => true]);
    }
}