<?php

namespace App\Helpers;

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
        'custom-reports' => 'premium'
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
        $limits = [
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
                'exports_monthly' => 3000,   // 3,000 records/month, CSV only
            ],
            'premium' => [
                'trainees'        => null,
                'trainers'        => null,
                'users'           => null,
                'courses'         => null,
                'exports_monthly' => null,   // unlimited exports (CSV, Excel, PDF)
            ],
        ];

        return $limits[$plan][$resource] ?? null;
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
        return match($plan) {
            'standard' => ['csv'],
            'premium'  => ['csv', 'excel', 'pdf'],
            default    => [],
        };
    }

    /**
     * Returns true if the plan can export at all.
     */
    public static function canExport(string $plan): bool
    {
        return in_array($plan, ['standard', 'premium']);
    }
}