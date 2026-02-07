<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Expo API Service Configuration
    |--------------------------------------------------------------------------
    */

    'service_name' => env('SERVICE_NAME', 'expo-api'),
    'service_version' => env('SERVICE_VERSION', '1.0.0'),

    /*
    |--------------------------------------------------------------------------
    | Auth Service Configuration
    |--------------------------------------------------------------------------
    */
    'auth_service' => [
        'url' => env('AUTH_SERVICE_URL', 'http://localhost:8001'),
        'token' => env('AUTH_SERVICE_TOKEN'),
        'timeout' => env('AUTH_SERVICE_TIMEOUT', 5),
        'cache_ttl' => env('AUTH_SERVICE_CACHE_TTL', 300), // 5 minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | User Roles
    |--------------------------------------------------------------------------
    */
    'roles' => [
        'investor' => 'مستثمر',
        'merchant' => 'تاجر',
        'admin' => 'مشرف',
        'super-admin' => 'مدير النظام',
    ],

    /*
    |--------------------------------------------------------------------------
    | Request Number Prefixes
    |--------------------------------------------------------------------------
    */
    'request_prefixes' => [
        'visit' => 'VR',
        'rental' => 'RR',
    ],

    /*
    |--------------------------------------------------------------------------
    | File Upload Settings
    |--------------------------------------------------------------------------
    */
    'uploads' => [
        'max_size' => 5120, // 5MB in KB
        'allowed_images' => ['jpg', 'jpeg', 'png', 'webp'],
        'allowed_documents' => ['pdf', 'jpg', 'jpeg', 'png'],
        'paths' => [
            'events' => 'uploads/events',
            'spaces' => 'uploads/spaces',
            'profiles' => 'uploads/profiles',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination Settings
    |--------------------------------------------------------------------------
    */
    'pagination' => [
        'default_per_page' => 15,
        'max_per_page' => 50,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    */
    'cache' => [
        'enabled' => env('CACHE_ENABLED', true),
        'prefix' => 'expo_',
        'ttl' => [
            'categories' => 3600, // 1 hour
            'cities' => 3600,
            'events' => 1800, // 30 minutes
            'spaces' => 1800,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Visit Request Settings
    |--------------------------------------------------------------------------
    */
    'visit_request' => [
        'max_visitors' => 10,
        'min_advance_days' => 1, // minimum days before visit
        'cancellation_allowed_before_hours' => 24,
    ],

    /*
    |--------------------------------------------------------------------------
    | Rental Request Settings
    |--------------------------------------------------------------------------
    */
    'rental_request' => [
        'min_rental_days' => 1,
        'requires_verified_profile' => true,
        'cancellation_allowed_before_hours' => 48,
    ],
];
