<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Plan hierarchy — higher index = higher plan
     */
    protected array $plans = ['basic', 'standard', 'premium'];

    /**
     * Features and the minimum plan required to access them
     */
    protected array $featurePlans = [
        // Basic (all plans)
        'trainees'           => 'basic',
        'courses'            => 'basic',
        'enrollments'        => 'basic',
        'attendances'        => 'basic',
        

        // Standard+
        'trainers'           => 'standard',
        'assessments'        => 'standard',
        'training-schedules' => 'standard',
        'users'              => 'standard',
        'reports'            => 'standard',

        // Premium only
        'certificates'       => 'premium',
        'custom-reports' => 'premium',
    ];

    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $tenant = tenancy()->tenant;

        if (! $tenant) {
            abort(403, 'No active tenant.');
        }

        if (! $tenant->isSubscribed()) {
            return redirect()->route('login')
                ->withErrors(['subscription' => 'Your subscription has expired or is inactive.']);
        }

        $requiredPlan = $this->featurePlans[$feature] ?? 'basic';
        $currentPlan  = $tenant->subscription;

        if (! $this->hasAccess($currentPlan, $requiredPlan)) {
            abort(403, "Your current plan ({$currentPlan}) does not include access to this feature. Upgrade to {$requiredPlan} or higher.");
        }

        // Pass plan limits via request attributes so controllers can read them
        $limits = $this->getPlanLimits($currentPlan);
        $request->attributes->set('plan_limits', $limits);

        return $next($request);
    }

    /**
     * Returns the quantity limits for each plan.
     * null = unlimited | 0 = not available on this plan
     */
    protected function getPlanLimits(string $plan): array
    {
        return match($plan) {
            'basic' => [
                'trainees'        => 100,
                'trainers'        => 0,
                'users'           => 1,
                'courses'         => 20,
                'exports_monthly' => 0,      // no exports on basic
            ],
            'standard' => [
                'trainees'        => 500,
                'trainers'        => null,
                'users'           => 5,
                'courses'         => null,
                'exports_monthly' => 3000,   // CSV only, 3,000 records/month
            ],
            'premium' => [
                'trainees'        => null,
                'trainers'        => null,
                'users'           => null,
                'courses'         => null,
                'exports_monthly' => null,   // unlimited (CSV, Excel, PDF)
            ],
            default => [
                'trainees'        => 100,
                'trainers'        => 0,
                'users'           => 1,
                'courses'         => 20,
                'exports_monthly' => 0,
            ],
        };
    }

    protected function hasAccess(string $currentPlan, string $requiredPlan): bool
    {
        $currentIndex  = array_search($currentPlan, $this->plans);
        $requiredIndex = array_search($requiredPlan, $this->plans);

        return $currentIndex !== false
            && $requiredIndex !== false
            && $currentIndex >= $requiredIndex;
    }
}