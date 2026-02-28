<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OneSignal App ID
    |--------------------------------------------------------------------------
    |
    | Your OneSignal App ID from the dashboard settings
    |
    */
    'app_id' => env('ONESIGNAL_APP_ID'),

    /*
    |--------------------------------------------------------------------------
    | OneSignal REST API Key
    |--------------------------------------------------------------------------
    |
    | Your OneSignal REST API Key (required for most operations)
    |
    */
    'rest_api_key' => env('ONESIGNAL_REST_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | OneSignal Organization API Key
    |--------------------------------------------------------------------------
    |
    | Your OneSignal Organization API Key (optional - only needed for
    | organization-level operations like creating new apps)
    |
    */
    'organization_api_key' => env('ONESIGNAL_ORGANIZATION_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | OneSignal API URL
    |--------------------------------------------------------------------------
    |
    | The base URL for OneSignal API
    |
    */
    'api_url' => env('ONESIGNAL_API_URL', 'https://api.onesignal.com'),

    /*
    |--------------------------------------------------------------------------
    | Default Segment
    |--------------------------------------------------------------------------
    |
    | The default segment to use when sending notifications
    |
    */
    'default_segment' => env('ONESIGNAL_DEFAULT_SEGMENT', 'All'),

    /*
    |--------------------------------------------------------------------------
    | Enable/Disable OneSignal
    |--------------------------------------------------------------------------
    |
    | Toggle OneSignal functionality (useful for testing/development)
    |
    */
    'enabled' => env('ONESIGNAL_ENABLED', true),
];
