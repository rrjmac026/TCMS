<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\RenewalRequest;
use App\Models\SubscriptionPlan;
use App\Models\Tenant;
use Illuminate\Http\Request;

class AdminRenewalController extends Controller
{
    /**
     * Show the subscription-expired wall.
     */
    public function expired()
    {
        $tenant         = tenancy()->tenant;
        $plans          = SubscriptionPlan::active()->orderBy('sort_order')->get();
        $pendingRequest = RenewalRequest::where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        return view('tenants.admin.subscription.expired', compact(
            'tenant', 'plans', 'pendingRequest'
        ));
    }

    /**
     * Submit a renewal request.
     *
     * Accepts:
     *   plan_slug      – which plan to renew to
     *   duration_days  – how many days to add (tenant-chosen; overrides plan default)
     *   discount_code  – optional promo code
     */
    public function request(Request $request)
    {
        $data = $request->validate([
            'plan_slug'     => ['required', 'in:basic,standard,premium'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'discount_code' => ['nullable', 'string'],
        ]);

        $tenant    = tenancy()->tenant;
        $planModel = SubscriptionPlan::where('slug', $data['plan_slug'])->firstOrFail();

        // Block duplicate pending requests
        $alreadyPending = RenewalRequest::where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->exists();

        if ($alreadyPending) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a pending renewal request.',
            ]);
        }

        // Price calculation
        $durationDays = (int) $data['duration_days'];
        $basePrice    = $this->priceForDuration($planModel, $durationDays);
        $discount     = null;
        $finalPrice   = $basePrice;
        $discountAmt  = 0;

        if (!empty($data['discount_code'])) {
            $discount = Discount::findValidCode(
                $data['discount_code'],
                $data['plan_slug'],
                $tenant->id
            );
            if ($discount) {
                $discountAmt = $discount->discountAmount($basePrice);
                $finalPrice  = $discount->applyTo($basePrice);
            }
        }

        // Also apply automatic discount if no code used
        if (!$discount) {
            $autoDiscount = Discount::where('is_active', true)
                ->where('is_automatic', true)
                ->where(function ($q) use ($data) {
                    $q->whereNull('plan_slugs')
                      ->orWhereJsonContains('plan_slugs', $data['plan_slug']);
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
                $discountAmt = $autoDiscount->discountAmount($basePrice);
                $finalPrice  = $autoDiscount->applyTo($basePrice);
            }
        }

        RenewalRequest::create([
            'tenant_id'       => $tenant->id,
            'plan_slug'       => $data['plan_slug'],
            'duration_days'   => $durationDays,
            'discount_code'   => $data['discount_code'] ?? null,
            'original_price'  => $basePrice,
            'discount_amount' => $discountAmt,
            'final_price'     => $finalPrice,
            'status'          => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Renewal request submitted. Please wait for super admin approval.',
        ]);
    }

    /**
     * Cancel the tenant's own pending renewal request.
     */
    public function cancel(Request $request)
    {
        $tenant = tenancy()->tenant;

        RenewalRequest::where('tenant_id', $tenant->id)
            ->where('status', 'pending')
            ->delete();

        return redirect()->back()->with('success', 'Renewal request cancelled.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    /**
     * Pro-rate the plan price proportionally to the requested duration.
     *
     * e.g. Standard is ₱1,499 for 180 days.
     *      If tenant asks for 90 days → ₱749.50
     *      If tenant asks for 360 days → ₱2,998
     */
    private function priceForDuration(SubscriptionPlan $plan, int $days): float
    {
        if ($plan->duration_days <= 0 || $plan->price <= 0) {
            return (float) $plan->price;
        }

        return round(($plan->price / $plan->duration_days) * $days, 2);
    }
}