<?php

namespace App\Helpers;
use App\Models\SubscriptionPlan;

class SubscriptionHelper
{
    protected static array $plans = ['basic', 'standard', 'premium'];

    protected static array $featurePlans = [
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
        'branding' => 'premium',
    ];

    public static function canAccess(string $currentPlan, string $feature): bool
    {
        $required      = static::$featurePlans[$feature] ?? 'basic';
        $currentIndex  = array_search($currentPlan, static::$plans);
        $requiredIndex = array_search($required, static::$plans);

        return $currentIndex !== false && $currentIndex >= $requiredIndex;
    }

    public static function getLimit(string $plan, string $resource): ?int
    {
        $planModel = SubscriptionPlan::where('slug', $plan)->first();

        if (! $planModel) return 0;

        return match($resource) {
            'trainees'        => $planModel->max_trainees,
            'trainers'        => $planModel->max_trainers,
            'users'           => $planModel->max_users,
            'courses'         => $planModel->max_courses,
            'exports_monthly' => $planModel->max_exports_monthly,
            default           => null,
        };
    }

    /**
     * Returns true if adding one more of $resource is allowed.
     */
    public static function canAddMore(string $plan, string $resource, int $currentCount): bool
    {
        $limit = static::getLimit($plan, $resource);
        if ($limit === null) return true;   // unlimited
        if ($limit === 0)    return false;  // not available on this plan
        return $currentCount < $limit;
    }

    /**
     * Returns the allowed export formats for a given plan.
     * Basic  → none
     * Standard → ['csv'] with a 3,000 record/month cap
     * Premium  → ['csv', 'excel', 'pdf'] unlimited
     */
    public static function getAllowedExportFormats(string $plan): array
    {
        $planModel = SubscriptionPlan::where('slug', $plan)->first();
        return $planModel?->allowed_export_formats ?? [];
    }

    /**
     * Returns true if the plan can export at all.
     */
    public static function canExport(string $plan): bool
    {
        return in_array($plan, ['standard', 'premium']);
    }
}