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
}