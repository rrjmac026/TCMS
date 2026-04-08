<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\DiscountUsage;
use App\Models\RenewalRequest;
use App\Models\SubscriptionPlan;
use App\Models\TenantSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSubscriptionController extends Controller
{
    // ── Upgrade page ──────────────────────────────────────────────────────

    public function index()
    {
        $tenant      = tenancy()->tenant;
        $currentPlan = $tenant->subscription ?? 'basic';
        $today = today();
        $plans = SubscriptionPlan::active()
            ->where(fn($q) => $q->whereNull('available_from')
                                ->orWhereDate('available_from', '<=', $today))
            ->where(fn($q) => $q->whereNull('available_until')
                                ->orWhereDate('available_until', '>=', $today))
            ->orderBy('sort_order')
            ->get();
        $planSlugs   = $plans->pluck('slug')->toArray();

        // Best active automatic discount for each plan
        $autoDiscounts = [];
        foreach ($plans as $plan) {
            $autoDiscounts[$plan->slug] = Discount::on('mysql')
                ->where('is_active', true)
                ->where('is_automatic', true)
                ->where(fn($q) => $q->whereNull('plan_slugs')
                                    ->orWhereJsonContains('plan_slugs', $plan->slug))
                ->where(fn($q) => $q->whereNull('valid_from')
                                    ->orWhereDate('valid_from', '<=', today()))
                ->where(fn($q) => $q->whereNull('valid_until')
                                    ->orWhereDate('valid_until', '>=', today()))
                ->orderByDesc('value')
                ->first();
        }

        // Any pending renewal request — shown to the tenant so they know
        $pendingRenewal = RenewalRequest::on('mysql')
            ->where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        return view('tenants.admin.subscription.upgrade', compact(
            'plans',
            'currentPlan',
            'planSlugs',
            'autoDiscounts',
            'pendingRenewal',
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
        $discount = Discount::findValidCode($request->code, $request->plan_slug, $tenantId);

        if (! $discount) {
            return response()->json(['valid' => false, 'message' => 'Invalid or inapplicable promo code.']);
        }

        $plan  = SubscriptionPlan::where('slug', $request->plan_slug)->firstOrFail();
        $base  = (float) $plan->price;

        return response()->json([
            'valid'           => true,
            'formatted_value' => $discount->formatted_value,
            'original_price'  => $base,
            'discount_amount' => $discount->discountAmount($base),
            'final_price'     => $discount->applyTo($base),
        ]);
    }

    // ── AJAX: upgrade plan ────────────────────────────────────────────────

    /**
     * Upgrade the tenant to a higher plan immediately.
     *
     * BILLING RULES enforced here:
     *
     * 1. duration_days is now tenant-chosen. If omitted, the plan's standard
     *    duration is used. expires_at = now + duration_days.
     *    Remaining days on the old plan are NOT carried over.
     *
     * 2. Any pending RENEWAL REQUEST for the OLD plan is automatically cancelled
     *    because it is now stale — the tenant has already moved to a different plan.
     *
     * 3. Central-DB models (TenantSubscription, DiscountUsage, Tenant) are written
     *    with explicit .on('mysql') to avoid the tenant-context connection interfering.
     */
    public function upgrade(Request $request)
    {
        $data = $request->validate([
            'subscription'  => ['required', 'in:basic,standard,premium'],
            'duration_days' => ['nullable', 'integer', 'min:1'],
            'discount_code' => ['nullable', 'string'],
        ]);

        $tenant    = tenancy()->tenant;
        $planModel = SubscriptionPlan::where('slug', $data['subscription'])->firstOrFail();
        $basePrice = (float) $planModel->price;

        // Enforce upgrade-only (no downgrades via this endpoint)
        $planOrder     = ['basic' => 0, 'standard' => 1, 'premium' => 2];
        $currentRank   = $planOrder[$tenant->subscription] ?? 0;
        $requestedRank = $planOrder[$data['subscription']] ?? 0;

        if ($requestedRank <= $currentRank) {
            return response()->json([
                'success' => false,
                'message' => 'You can only upgrade to a higher plan.',
            ], 422);
        }

        // ── Resolve duration ──────────────────────────────────────────────
        // Use the tenant-chosen duration, or fall back to the plan's standard duration.
        $durationDays = (int) ($data['duration_days'] ?? $planModel->duration_days);
        if ($durationDays < 1) {
            $durationDays = $planModel->duration_days;
        }

        // Pro-rate the price based on the chosen duration vs the standard duration.
        // e.g. Standard is ₱1,499 for 180 days → 90 days = ₱749.50
        $proratedBase = $planModel->duration_days > 0
            ? round(($basePrice / $planModel->duration_days) * $durationDays, 2)
            : $basePrice;

        $price    = $proratedBase;
        $discount = null;

        // ── Resolve discount ──────────────────────────────────────────────
        if (! empty($data['discount_code'])) {
            $discount = Discount::findValidCode($data['discount_code'], $data['subscription'], $tenant->id);
            if ($discount) {
                $price = $discount->applyTo($price);
            }
        }

        if (! $discount) {
            $auto = Discount::on('mysql')
                ->where('is_active', true)
                ->where('is_automatic', true)
                ->where(fn($q) => $q->whereNull('plan_slugs')
                                    ->orWhereJsonContains('plan_slugs', $data['subscription']))
                ->where(fn($q) => $q->whereNull('valid_from')
                                    ->orWhereDate('valid_from', '<=', today()))
                ->where(fn($q) => $q->whereNull('valid_until')
                                    ->orWhereDate('valid_until', '>=', today()))
                ->orderByDesc('value')
                ->first();

            if ($auto) {
                $discount = $auto;
                $price    = $auto->applyTo($proratedBase);
            }
        }

        // New expiry: now + chosen duration.
        // Old remaining time is intentionally not carried over.
        $expiresAt = now()->addDays($durationDays);
        $appliedBy = auth()->id();

        // ── Cancel stale pending renewal requests ─────────────────────────
        RenewalRequest::on('mysql')
            ->where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->where(function ($q) use ($planOrder, $requestedRank) {
                $stalePlanSlugs = array_keys(
                    array_filter($planOrder, fn($rank) => $rank <= $requestedRank)
                );
                $q->whereIn('plan_slug', $stalePlanSlugs);
            })
            ->update([
                'status' => 'cancelled_by_upgrade',
                'notes'  => 'Automatically cancelled — tenant upgraded to a higher plan.',
            ]);

        // ── Write discount usage to central DB ────────────────────────────
        $discountUsageId = null;
        if ($discount) {
            $usage = DiscountUsage::on('mysql')->create([
                'discount_id'     => $discount->id,
                'tenant_id'       => $tenant->id,
                'action'          => 'tenant_upgrade',
                'plan_slug'       => $data['subscription'],
                'original_price'  => $proratedBase,
                'discount_amount' => $discount->discountAmount($proratedBase),
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

        // ── Update tenant record on central DB ────────────────────────────
        DB::connection('mysql')->table('tenants')
            ->where('id', $tenant->id)
            ->update([
                'subscription' => $data['subscription'],
                'expires_at'   => $expiresAt,
                'updated_at'   => now(),
            ]);

        // Keep in-memory model in sync for any code later in this request
        $tenant->subscription = $data['subscription'];
        $tenant->expires_at   = $expiresAt;

        return response()->json(['success' => true]);
    }
}