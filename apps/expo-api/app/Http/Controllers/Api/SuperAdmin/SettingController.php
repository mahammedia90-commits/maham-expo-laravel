<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * SuperAdmin Setting Controller
 *
 * Manages system-level settings stored in cache with JSON file fallback.
 */
class SettingController extends Controller
{
    private const CACHE_KEY = 'system_settings';

    private array $defaults = [
        'site_name' => 'Maham Expo',
        'site_name_ar' => '',
        'contact_email' => '',
        'contact_phone' => '',
        'support_email' => '',
        'maintenance_mode' => false,
        'allow_registration' => true,
        'auto_approve_profiles' => false,
        'max_visit_requests_per_day' => 10,
        'max_rental_requests_per_merchant' => 5,
        'default_currency' => 'SAR',
        'timezone' => 'Asia/Riyadh',

        // ── Visit Fee ──
        'visit_fee' => 0,                         // رسوم الزيارة

        // ── CORS Settings ──
        'cors_allowed_origins' => '*',           // * أو روابط مفصولة بفاصلة
        'cors_supports_credentials' => false,
        'cors_max_age' => 86400,

        // ── Payment Gateway Settings (غير سرية) ──
        'payment_enabled' => true,
        'payment_gateway_mode' => 'test',        // test أو live
        'payment_default_currency' => 'SAR',
        'payment_3d_secure' => true,

        // ── SMS/OTP Settings (غير سرية) ──
        'sms_enabled' => true,
        'sms_default_channel' => 'sms',          // sms أو whatsapp
        'sms_max_attempts_per_hour' => 5,
        'sms_code_length' => 6,
    ];

    /**
     * Get all settings
     */
    public function index(): JsonResponse
    {
        return ApiResponse::success($this->getSettings());
    }

    /**
     * Get a single setting by key
     */
    public function show(string $key): JsonResponse
    {
        $settings = $this->getSettings();

        if (!array_key_exists($key, $settings)) {
            return ApiResponse::notFound(__('messages.setting.not_found'));
        }

        return ApiResponse::success([
            'key' => $key,
            'value' => $settings[$key],
        ]);
    }

    /**
     * Update settings (batch update)
     */
    public function update(Request $request): JsonResponse
    {
        // Accept both flat format and wrapped {settings: {...}} format
        $input = $request->has('settings') ? $request->input('settings') : $request->except(['auth_user_id', 'auth_user_role']);

        if (!is_array($input) || empty($input)) {
            return ApiResponse::error(__('messages.validation_failed'), 'validation_failed', 422);
        }

        // Only allow known setting keys
        $allowedKeys = array_keys($this->defaults);
        $input = array_intersect_key($input, array_flip($allowedKeys));

        $rules = [
            'site_name' => 'sometimes|nullable|string|max:255',
            'site_name_ar' => 'sometimes|nullable|string|max:255',
            'contact_email' => 'sometimes|nullable|email|max:255',
            'contact_phone' => 'sometimes|nullable|string|max:20',
            'support_email' => 'sometimes|nullable|email|max:255',
            'maintenance_mode' => 'sometimes|boolean',
            'allow_registration' => 'sometimes|boolean',
            'auto_approve_profiles' => 'sometimes|boolean',
            'max_visit_requests_per_day' => 'sometimes|integer|min:1|max:100',
            'max_rental_requests_per_merchant' => 'sometimes|integer|min:1|max:50',
            'default_currency' => 'sometimes|string|max:10',
            'timezone' => 'sometimes|string|max:50',
            'visit_fee' => 'sometimes|numeric|min:0|max:9999999',
            'cors_allowed_origins' => 'sometimes|string|max:2000',
            'cors_supports_credentials' => 'sometimes|boolean',
            'cors_max_age' => 'sometimes|integer|min:0|max:604800',
            'payment_enabled' => 'sometimes|boolean',
            'payment_gateway_mode' => 'sometimes|string|in:test,live',
            'payment_default_currency' => 'sometimes|string|max:10',
            'payment_3d_secure' => 'sometimes|boolean',
            'sms_enabled' => 'sometimes|boolean',
            'sms_default_channel' => 'sometimes|string|in:sms,whatsapp',
            'sms_max_attempts_per_hour' => 'sometimes|integer|min:1|max:20',
            'sms_code_length' => 'sometimes|integer|min:4|max:8',
        ];

        $validated = validator($input, $rules)->validate();

        $currentSettings = $this->getSettings();
        $updatedSettings = array_merge($currentSettings, $validated);

        Cache::forever(self::CACHE_KEY, $updatedSettings);

        // Persist to JSON file as backup
        $settingsPath = storage_path('app/settings.json');
        file_put_contents($settingsPath, json_encode($updatedSettings, JSON_PRETTY_PRINT));

        // Sync all service settings to bootstrap cache (for config:cache compatibility)
        $this->syncBootstrapCache($updatedSettings);

        // Rebuild config cache so changes take effect immediately
        try {
            Artisan::call('config:cache');
        } catch (\Throwable $e) {
            // Ignore — cache will be rebuilt on next request
        }

        // Push SMS settings to auth-service (non-blocking)
        $this->syncAuthServiceSettings($updatedSettings);

        return ApiResponse::success(
            data: $updatedSettings,
            message: __('messages.setting.updated')
        );
    }

    /**
     * Get settings from cache or file fallback
     */
    private function getSettings(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $settingsPath = storage_path('app/settings.json');

            if (file_exists($settingsPath)) {
                $saved = json_decode(file_get_contents($settingsPath), true);
                return array_merge($this->defaults, $saved ?? []);
            }

            return $this->defaults;
        });
    }

    /**
     * Save service settings to bootstrap cache file
     * so config/cors.php, config/tap.php can read them during config:cache
     */
    private function syncBootstrapCache(array $settings): void
    {
        $syncKeys = [
            // CORS
            'cors_allowed_origins', 'cors_supports_credentials', 'cors_max_age',
            // Payment
            'payment_enabled', 'payment_gateway_mode', 'payment_default_currency', 'payment_3d_secure',
            // SMS
            'sms_enabled', 'sms_default_channel', 'sms_max_attempts_per_hour', 'sms_code_length',
        ];

        $cached = [];
        foreach ($syncKeys as $key) {
            if (isset($settings[$key])) {
                $cached[$key] = $settings[$key];
            }
        }

        $cachePath = base_path('bootstrap/cache/system_settings.php');
        $content = '<?php return ' . var_export($cached, true) . ';' . PHP_EOL;
        file_put_contents($cachePath, $content);
    }

    /**
     * Push SMS/OTP settings to auth-service (internal Docker network)
     */
    private function syncAuthServiceSettings(array $settings): void
    {
        $smsKeys = ['sms_enabled', 'sms_default_channel', 'sms_max_attempts_per_hour', 'sms_code_length'];
        $smsSettings = [];

        foreach ($smsKeys as $key) {
            if (isset($settings[$key])) {
                $smsSettings[$key] = $settings[$key];
            }
        }

        if (empty($smsSettings)) {
            return;
        }

        try {
            $authUrl = rtrim(config('expo-api.auth_service.url'), '/');
            Http::timeout(5)->post($authUrl . '/api/v1/service/sync-settings', [
                'settings' => $smsSettings,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to sync settings to auth-service', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
