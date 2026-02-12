<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Auth Service Configuration
    |--------------------------------------------------------------------------
    */

    'service_name' => env('SERVICE_NAME', 'auth-service'),
    'service_version' => env('SERVICE_VERSION', '1.0.0'),

    /*
    |--------------------------------------------------------------------------
    | Service Token for Inter-Service Communication
    |--------------------------------------------------------------------------
    */
    'service_token' => env('SERVICE_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | JWT Settings
    |--------------------------------------------------------------------------
    */
    'jwt' => [
        'ttl' => env('JWT_TTL', 60), // minutes
        'refresh_ttl' => env('JWT_REFRESH_TTL', 20160), // 14 days
    ],

    /*
    |--------------------------------------------------------------------------
    | Trusted Services
    |--------------------------------------------------------------------------
    | قائمة الخدمات الموثوقة التي يمكنها الاتصال بـ Auth Service
    */
    'trusted_services' => [
        'ips' => explode(',', env('TRUSTED_SERVICE_IPS', '127.0.0.1')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'rate_limit' => [
        'login_attempts' => 5,
        'login_decay_minutes' => 1,
        'api_per_minute' => env('RATE_LIMIT_PER_MINUTE', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Policy
    |--------------------------------------------------------------------------
    */
    'password' => [
        'min_length' => 8,
        'require_uppercase' => true,
        'require_lowercase' => true,
        'require_numbers' => true,
        'require_special' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Roles
    |--------------------------------------------------------------------------
    */
    'default_roles' => [
        'super-admin' => [
            'description' => 'Full system access',
            'is_system' => true,
        ],
        'admin' => [
            'description' => 'Administrative access',
            'is_system' => true,
        ],
        'user' => [
            'description' => 'Regular user access',
            'is_system' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Permissions
    |--------------------------------------------------------------------------
    */
    'default_permissions' => [
        // Users
        'users.view',
        'users.create',
        'users.update',
        'users.delete',

        // Roles
        'roles.view',
        'roles.create',
        'roles.update',
        'roles.delete',

        // Permissions
        'permissions.view',
        'permissions.create',
        'permissions.update',
        'permissions.delete',

        // Services
        'services.view',
        'services.create',
        'services.update',
        'services.delete',
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Settings
    |--------------------------------------------------------------------------
    */
    'audit' => [
        'enabled' => true,
        'events' => [
            'login',
            'logout',
            'password_change',
            'role_change',
            'permission_change',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'enabled' => true,
        'prefix' => 'auth_',
        'ttl' => [
            'permissions' => 3600, // 1 hour
            'roles' => 3600,
            'user_permissions' => 1800, // 30 minutes
        ],
    ],
];
