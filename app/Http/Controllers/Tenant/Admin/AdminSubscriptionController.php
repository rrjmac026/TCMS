<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

class AdminSubscriptionController extends Controller
{
    public function index()
    {
        // Load plans from the central DB so the view shows live prices/features
        $dbPlans = collect();
        try {
            $dbPlans = SubscriptionPlan::active()->get()->keyBy('slug');
        } catch (\Throwable) {
            // Plans table not yet migrated — view falls back to static copy
        }

        $tenant      = tenancy()->tenant;
        $currentPlan = $tenant->subscription ?? 'basic';

        return view('tenants.admin.subscription.upgrade', compact('dbPlans', 'currentPlan'));
    }

    public function upgrade(Request $request)
    {
        $request->validate([
            'subscription' => ['required', 'in:basic,standard,premium'],
        ]);

        $tenant = tenancy()->tenant;
        $plans  = ['basic', 'standard', 'premium'];

        $currentIndex = array_search($tenant->subscription, $plans);
        $newIndex     = array_search($request->subscription, $plans);

        if ($newIndex <= $currentIndex) {
            return response()->json([
                'success' => false,
                'message' => 'You can only upgrade to a higher plan.',
            ], 422);
        }

        // Read duration from SubscriptionPlan if available
        $expiresAt = null;
        try {
            $plan = SubscriptionPlan::where('slug', $request->subscription)->first();
            if ($plan) {
                $expiresAt = $plan->getExpiresAt();
            }
        } catch (\Throwable) {}

        // Fallback to hardcoded durations if plans table isn't ready
        if (! $expiresAt) {
            $expiresAt = match($request->subscription) {
                'standard' => now()->addMonths(6),
                'premium'  => now()->addYear(),
                default    => now()->addDays(30),
            };
        }

        $tenant->subscription = $request->subscription;
        $tenant->expires_at   = $expiresAt;
        $tenant->save();

        return response()->json([
            'success' => true,
            'plan'    => $request->subscription,
        ]);
    }
}