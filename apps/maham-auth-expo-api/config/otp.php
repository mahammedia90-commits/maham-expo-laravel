<?php

/*
|--------------------------------------------------------------------------
| OTP Provider Configuration
|--------------------------------------------------------------------------
|
| نظام مرن لإدارة مزودي خدمة OTP
| يدعم: authentica, twilio
| يمكن تغيير المزود النشط من الإعدادات أو .env
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
    /*
    |--------------------------------------------------------------------------
    | Active OTP Provider
    |--------------------------------------------------------------------------
    | المزود النشط حالياً: authentica, twilio
    | Dashboard setting → .env → default
    */
    'active_provider' => $dashboardSettings['otp_active_provider'] ?? env('OTP_ACTIVE_PROVIDER', 'authentica'),

    /*
    |--------------------------------------------------------------------------
    | Test Mode
    |--------------------------------------------------------------------------
    | في وضع الاختبار: أي رمز OTP يُقبل بدون إرسال فعلي
    */
    'test_mode' => $dashboardSettings['otp_test_mode']
        ?? $dashboardSettings['sms_test_mode']
        ?? env('OTP_TEST_MODE', env('TWILIO_TEST_MODE', false)),

    /*
    |--------------------------------------------------------------------------
    | Default Channel
    |--------------------------------------------------------------------------
    | القناة الافتراضية: sms, whatsapp
    */
    'default_channel' => $dashboardSettings['otp_default_channel']
        ?? $dashboardSettings['sms_default_channel']
        ?? env('OTP_DEFAULT_CHANNEL', 'sms'),

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */
    'max_attempts_per_hour' => (int) ($dashboardSettings['otp_max_attempts']
        ?? $dashboardSettings['sms_max_attempts_per_hour']
        ?? env('OTP_MAX_ATTEMPTS', 5)),

    /*
    |--------------------------------------------------------------------------
    | Cooldown Between Sends (seconds)
    |--------------------------------------------------------------------------
    | الحد الأدنى بالثواني بين كل رسالة OTP وأخرى لنفس الرقم
    | مثال: 60 = دقيقة واحدة بين كل رسالة
    */
    'cooldown_seconds' => (int) ($dashboardSettings['otp_cooldown_seconds']
        ?? $dashboardSettings['sms_cooldown_seconds']
        ?? env('OTP_COOLDOWN_SECONDS', 60)),

    /*
    |--------------------------------------------------------------------------
    | Enable Provider Fallback
    |--------------------------------------------------------------------------
    | إذا فشل المزود الأساسي، جرب مزود ثاني تلقائياً
    */
    'enable_provider_fallback' => $dashboardSettings['otp_enable_fallback']
        ?? env('OTP_ENABLE_FALLBACK', false),

    /*
    |--------------------------------------------------------------------------
    | Providers Configuration
    |--------------------------------------------------------------------------
    */
    'providers' => [

        /*
        |----------------------------------------------------------------------
        | Authentica (authentica.sa)
        |----------------------------------------------------------------------
        | API Docs: https://authenticasa.docs.apiary.io/
        | Portal: https://portal.authentica.sa/
        */
        'authentica' => [
            'api_key' => env('AUTHENTICA_API_KEY', ''),
            'base_url' => env('AUTHENTICA_BASE_URL', 'https://api.authentica.sa/api/v2'),
            'default_channel' => env('AUTHENTICA_DEFAULT_CHANNEL', 'sms'),
            'template_id' => env('AUTHENTICA_TEMPLATE_ID', null),
            'fallback_enabled' => env('AUTHENTICA_FALLBACK_ENABLED', true),
        ],

        /*
        |----------------------------------------------------------------------
        | Twilio
        |----------------------------------------------------------------------
        */
        'twilio' => [
            'account_sid' => env('TWILIO_ACCOUNT_SID', ''),
            'auth_token' => env('TWILIO_AUTH_TOKEN', ''),
            'verify_service_sid' => env('TWILIO_VERIFY_SERVICE_SID', ''),
            'base_url' => env('TWILIO_BASE_URL', 'https://verify.twilio.com/v2'),
        ],
    ],
];
