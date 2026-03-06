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
        'trainees'          => 'basic',
        'courses'           => 'basic',
        'enrollments'       => 'basic',
        'attendances'       => 'basic',

        // Standard+
        'trainers'          => 'standard',
        'assessments'       => 'standard',
        'training-schedules'=> 'standard',
        'users'             => 'standard',

        // Premium only
        'certificates'      => 'premium',
    ];

    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $tenant = tenancy()->tenant;

        // No active tenant (shouldn't happen on tenant routes)
        if (! $tenant) {
            abort(403, 'No active tenant.');
        }

        // Tenant must be approved and not expired
        if (! $tenant->isSubscribed()) {
            return redirect()->route('login')
                ->withErrors(['subscription' => 'Your subscription has expired or is inactive. Please contact support.']);
        }

        $requiredPlan = $this->featurePlans[$feature] ?? 'basic';
        $currentPlan  = $tenant->subscription;

        if (! $this->hasAccess($currentPlan, $requiredPlan)) {
            abort(403, "Your current plan ({$currentPlan}) does not include access to this feature. Upgrade to {$requiredPlan} or higher.");
        }

        // Basic plan: cap trainees at 100
        if ($feature === 'trainees' && $currentPlan === 'basic') {
            $request->attributes->set('trainee_limit', 100);
        }

        // Standard plan: cap trainees at 500
        if ($feature === 'trainees' && $currentPlan === 'standard') {
            $request->attributes->set('trainee_limit', 500);
        }

        return $next($request);
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