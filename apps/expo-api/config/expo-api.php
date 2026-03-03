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
        'sponsor' => 'راعي',
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
        'sponsor_contract' => 'SC',
        'sponsor_payment' => 'SP',
        'rental_contract' => 'RC',
        'invoice' => 'INV',
        'ticket' => 'TK',
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
            'sponsors' => 'uploads/sponsors',
            'sponsor_assets' => 'uploads/sponsor-assets',
            'banners' => 'uploads/banners',
            'tickets' => 'uploads/tickets',
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
            'sponsors' => 1800,
            'sponsor_packages' => 3600,
            'pages' => 7200, // 2 hours
            'faqs' => 3600,
            'banners' => 1800,
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

    /*
    |--------------------------------------------------------------------------
    | Sponsor Settings
    |--------------------------------------------------------------------------
    */
    'sponsor' => [
        'max_assets_per_sponsor' => 20,
        'max_asset_size' => 10240, // 10MB in KB
        'allowed_asset_types' => ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'mp4'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Rating Settings
    |--------------------------------------------------------------------------
    */
    'rating' => [
        'min_rating' => 1,
        'max_rating' => 5,
        'auto_approve' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Support Ticket Settings
    |--------------------------------------------------------------------------
    */
    'support' => [
        'max_attachments' => 5,
        'max_attachment_size' => 5120, // 5MB
        'auto_close_after_days' => 30,
    ],

    /*
    |--------------------------------------------------------------------------
    | Invoice Settings
    |--------------------------------------------------------------------------
    */
    'invoice' => [
        'tax_rate' => 15, // 15% VAT
        'currency' => 'SAR',
        'company_name' => 'Maham Expo',
        'company_name_ar' => 'معرض مهام',
    ],
];
