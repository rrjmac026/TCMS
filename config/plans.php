<?php

return [
    'basic' => [
        'name'        => 'Basic',
        'price'       => 0,
        'duration_days' => 30,
        'features'    => [
            'Max trainees'  => 100,
            'Max courses'   => 20,
            'Max users'     => 1,
            'Trainers'      => false,
            'Assessments'   => false,
            'Certificates'  => false,
            'Reports'       => false,
        ],
    ],
    'standard' => [
        'name'        => 'Standard',
        'price'       => 1499,
        'duration_days' => 180,
        'features'    => [
            'Max trainees'  => 500,
            'Max courses'   => null, // unlimited
            'Max users'     => 5,
            'Trainers'      => true,
            'Assessments'   => true,
            'Certificates'  => false,
            'Reports'       => true,
        ],
    ],
    'premium' => [
        'name'        => 'Premium',
        'price'       => 3999,
        'duration_days' => 365,
        'features'    => [
            'Max trainees'  => null,
            'Max courses'   => null,
            'Max users'     => null,
            'Trainers'      => true,
            'Assessments'   => true,
            'Certificates'  => true,
            'Reports'       => true,
        ],
    ],
];