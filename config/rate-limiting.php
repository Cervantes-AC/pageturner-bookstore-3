<?php

return [
    'tiers' => [
        'public' => [
            'limit' => 30,
            'decay' => 60,
            'description' => 'General browsing, book search',
            'users' => 'Visitors',
        ],
        'standard' => [
            'limit' => 60,
            'decay' => 60,
            'description' => 'Authenticated API access',
            'users' => 'Regular customers',
        ],
        'premium' => [
            'limit' => 300,
            'decay' => 60,
            'description' => 'High-volume API access',
            'users' => 'Premium/VIP customers',
        ],
        'admin' => [
            'limit' => 1000,
            'decay' => 60,
            'description' => 'Administrative operations',
            'users' => 'Administrators',
        ],
        'auth' => [
            'limit' => 10,
            'decay' => 60,
            'description' => 'Login, registration, password reset',
            'users' => 'All (strict)',
        ],
    ],
];
