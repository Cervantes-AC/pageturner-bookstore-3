<?php

return [
    'enabled' => env('AUDIT_ENABLED', true),

    'events' => [
        'created',
        'updated',
        'deleted',
        'login',
        'logout',
        'failed_login',
        'password_changed',
        '2fa_enabled',
        '2fa_disabled',
        'email_verified',
        'permission_changed',
        'role_assigned',
        'backup_run',
        'import_completed',
        'export_completed',
        'settings_changed',
    ],

    'sensitive_fields' => [
        'password',
        'password_confirmation',
        'current_password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'credit_card',
        'card_number',
        'cvv',
        'ssn',
    ],

    'retention' => [
        'online_days' => 365,
        'archive_years' => 5,
    ],
];
