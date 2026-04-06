<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\DiscountUsage;
use App\Models\SubscriptionPlan;
use App\Models\TenantSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSubscriptionController extends Controller
{
    public function index()
    {
        $dbPlans = collect();
        try {
            $dbPlans = SubscriptionPlan::active()->orderBy('sort_order')->get()->keyBy('slug');
        } catch (\Throwable) {}

        $tenant      = tenancy()->tenant;
        $currentPlan = $tenant->subscription ?? 'basic';
        $plans       = $dbPlans->values();

        return view('tenants.admin.subscription.upgrade', compact('dbPlans', 'currentPlan', 'plans'));
    }

    /**
     * AJAX — validate a discount code against a plan (called from the tenant upgrade page).
     */
    public function validateCode(Request $request)
    {
        $request->validate([
            'code'      => ['required', 'string'],
            'plan_slug' => ['required', 'in:basic,standard,premium'],
        ]);

        $discount = Discount::where('code', strtoupper($request->code))->first();

        if (! $discount || ! $discount->isValidFor($request->plan_slug)) {
            return response()->json(['valid' => false, 'message' => 'Invalid or inapplicable discount code.']);
        }

        $planModel = SubscriptionPlan::where('slug', $request->plan_slug)->first();
        $base      = $planModel ? (float) $planModel->price : 0;
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
     * Confirm upgrade — validates plan, optional discount, records everything, updates tenant.
     */
    public function upgrade(Request $request)
    {
        $request->validate([
            'subscription'  => ['required', 'in:basic,standard,premium'],
            'discount_code' => ['nullable', 'string'],
        ]);

        $tenant = tenancy()->tenant;
        $plans  = ['basic', 'standard', 'premium'];

        $currentIndex = array_search($tenant->subscription ?? 'basic', $plans);
        $newIndex     = array_search($request->subscription, $plans);

        if ($newIndex <= $currentIndex) {
            return response()->json([
                'success' => false,
                'message' => 'You can only upgrade to a higher plan.',
            ], 422);
        }

        $planModel = SubscriptionPlan::where('slug', $request->subscription)->first();

        if (! $planModel) {
            return response()->json(['success' => false, 'message' => 'Plan not found.'], 422);
        }

        $basePrice     = (float) $planModel->price;
        $finalPrice    = $basePrice;
        $discountModel = null;

        // Validate discount if provided
        if (! empty($request->discount_code)) {
            $discountModel = Discount::where('code', strtoupper($request->discount_code))->first();

            if (! $discountModel || ! $discountModel->isValidFor($request->subscription)) {
                return response()->json([
                    'success' => false,
                    'message' => 'The discount code is invalid or does not apply to this plan.',
                ], 422);
            }

            $finalPrice = $discountModel->applyTo($basePrice);
        }

        $expiresAt = $planModel->getExpiresAt() ?? match($request->subscription) {
            'standard' => now()->addMonths(6),
            'premium'  => now()->addYear(),
            default    => now()->addDays(30),
        };

        DB::transaction(function () use ($tenant, $planModel, $discountModel, $basePrice, $finalPrice, $expiresAt, $request) {
            // Record discount usage
            $discountUsageId = null;
            if ($discountModel) {
                $usage = DiscountUsage::create([
                    'discount_id'     => $discountModel->id,
                    'tenant_id'       => $tenant->id,
                    'action'          => 'upgrade_tenant',
                    'plan_slug'       => $planModel->slug,
                    'original_price'  => $basePrice,
                    'discount_amount' => $discountModel->discountAmount($basePrice),
                    'final_price'     => $finalPrice,
                    'applied_by'      => auth()->id(),
                ]);
                $discountUsageId = $usage->id;
            }

            // Record subscription history
            TenantSubscription::create([
                'tenant_id'        => $tenant->id,
                'plan_slug'        => $planModel->slug,
                'discount_usage_id'=> $discountUsageId,
                'amount_paid'      => $finalPrice,
                'action'           => 'upgrade_tenant',
                'starts_at'        => now(),
                'expires_at'       => $expiresAt,
                'applied_by'       => auth()->id(),
            ]);

            // Update tenant
            $tenant->subscription = $planModel->slug;
            $tenant->expires_at   = $expiresAt;
            $tenant->save();
        });

        return response()->json([
            'success' => true,
            'plan'    => $request->subscription,
        ]);
    }
}