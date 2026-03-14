<?php

/*
|--------------------------------------------------------------------------
| Twilio Verify Configuration
|--------------------------------------------------------------------------
|
| Secret keys (account_sid, auth_token, verify_service_sid) are from .env only.
| Non-secret settings can be controlled from the dashboard (via expo-api sync).
| Dashboard settings take priority over .env values.
|
*/

// Load dashboard settings from bootstrap cache (synced from expo-api)
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
    'account_sid' => env('TWILIO_ACCOUNT_SID', ''),
    'auth_token' => env('TWILIO_AUTH_TOKEN', ''),
    'verify_service_sid' => env('TWILIO_VERIFY_SERVICE_SID', ''),

    'base_url' => 'https://verify.twilio.com/v2',

    // ── Non-secret settings (dashboard → .env → default) ──
    'enabled' => $dashboardSettings['sms_enabled'] ?? env('TWILIO_ENABLED', true),
    'default_channel' => $dashboardSettings['sms_default_channel'] ?? env('TWILIO_DEFAULT_CHANNEL', 'sms'),
    'code_length' => $dashboardSettings['sms_code_length'] ?? env('TWILIO_CODE_LENGTH', 6),
    'max_attempts_per_hour' => $dashboardSettings['sms_max_attempts_per_hour'] ?? env('TWILIO_MAX_ATTEMPTS', 5),

    // ── Test Mode (in test mode, any OTP code is accepted without calling Twilio) ──
    // Dashboard setting takes priority → then OTP_TEST_MODE env → then TWILIO_TEST_MODE env
    'test_mode' => $dashboardSettings['sms_test_mode'] ?? env('OTP_TEST_MODE', env('TWILIO_TEST_MODE', false)),

    // ── Auth Mode (phone_and_otp | phone_or_email_and_pass) ──
    'auth_mode' => $dashboardSettings['auth_mode'] ?? env('AUTH_MODE', 'phone_and_otp'),
];
