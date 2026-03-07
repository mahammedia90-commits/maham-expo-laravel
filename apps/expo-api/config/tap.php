<?php

/*
|--------------------------------------------------------------------------
| Tap Payment Gateway Configuration
|--------------------------------------------------------------------------
|
| Secret keys (tap.secret_key, tap.public_key) are loaded from .env only.
| Non-secret settings can be controlled from the dashboard (Settings API).
| Dashboard settings take priority over .env values.
|
*/

// Load dashboard settings from bootstrap cache (written by SettingController)
$dashboardSettings = [];
$settingsCache = base_path('bootstrap/cache/system_settings.php');
if (file_exists($settingsCache)) {
    try {
        $dashboardSettings = include $settingsCache;
    } catch (\Throwable $e) {
        $dashboardSettings = [];
    }
}

return [
    // ── Secret Keys (from .env only — never expose in dashboard) ──
    'secret_key' => env('TAP_SECRET_KEY', ''),
    'public_key' => env('TAP_PUBLIC_KEY', ''),

    // ── Non-secret settings (dashboard → .env → default) ──
    'enabled' => $dashboardSettings['payment_enabled'] ?? env('TAP_ENABLED', true),
    'currency' => $dashboardSettings['payment_default_currency'] ?? env('TAP_CURRENCY', 'SAR'),
    'test_mode' => ($dashboardSettings['payment_gateway_mode'] ?? env('TAP_GATEWAY_MODE', 'test')) === 'test',
    'three_d_secure' => $dashboardSettings['payment_3d_secure'] ?? env('TAP_3D_SECURE', true),

    'base_url' => env('TAP_BASE_URL', 'https://api.tap.company/v2'),

    // Redirect URL after payment (frontend URL)
    'redirect_url' => env('TAP_REDIRECT_URL', ''),

    // Webhook URL for server-to-server notifications
    'webhook_url' => env('TAP_WEBHOOK_URL', ''),
];
