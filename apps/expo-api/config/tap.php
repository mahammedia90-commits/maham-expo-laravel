<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tap Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Configure your Tap payment gateway credentials and settings.
    | Get your keys from: https://dashboard.tap.company
    |
    */

    'secret_key' => env('TAP_SECRET_KEY', ''),
    'public_key' => env('TAP_PUBLIC_KEY', ''),
    'currency' => env('TAP_CURRENCY', 'SAR'),

    'base_url' => env('TAP_BASE_URL', 'https://api.tap.company/v2'),

    // Redirect URL after payment (frontend URL)
    'redirect_url' => env('TAP_REDIRECT_URL', ''),

    // Webhook URL for server-to-server notifications
    'webhook_url' => env('TAP_WEBHOOK_URL', ''),

    // Enable 3D Secure
    'three_d_secure' => env('TAP_3D_SECURE', true),

    // Test mode
    'test_mode' => env('TAP_TEST_MODE', true),
];
