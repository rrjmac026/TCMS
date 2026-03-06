<?php

namespace App\Helpers;

class SubscriptionHelper
{
    protected static array $plans = ['basic', 'standard', 'premium'];

    protected static array $featurePlans = [
        'trainees'           => 'basic',
        'courses'            => 'basic',
        'enrollments'        => 'basic',
        'attendances'        => 'basic',
        'trainers'           => 'standard',
        'assessments'        => 'standard',
        'training-schedules' => 'standard',
        'users'              => 'standard',
        'certificates'       => 'premium',
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
                'trainees' => 100,
                'trainers' => 0,
                'users'    => 1,
                'courses'  => 20,
            ],
            'standard' => [
                'trainees' => 500,
                'trainers' => null,
                'users'    => 5,
                'courses'  => null,
            ],
            'premium' => [
                'trainees' => null,
                'trainers' => null,
                'users'    => null,
                'courses'  => null,
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
        if ($limit === 0)    return false;  // not available
        return $currentCount < $limit;
    }
}