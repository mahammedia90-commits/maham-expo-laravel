<?php

namespace App\Services;

use App\Contracts\OtpProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Authentica OTP Provider
 * https://authenticasa.docs.apiary.io/
 *
 * API Base: https://api.authentica.sa/api/v2
 * Auth: X-Authorization header with API Key
 */
class AuthenticaService implements OtpProviderInterface
{
    protected string $apiKey;
    protected string $baseUrl;
    protected string $defaultChannel;
    protected int $maxAttempts;
    protected bool $fallbackEnabled;
    protected ?int $templateId;

    public function __construct()
    {
        $this->apiKey = config('otp.providers.authentica.api_key', '');
        $this->baseUrl = rtrim(config('otp.providers.authentica.base_url', 'https://api.authentica.sa/api/v2'), '/');
        $this->defaultChannel = config('otp.providers.authentica.default_channel', 'sms');
        $this->maxAttempts = config('otp.max_attempts_per_hour', 5);
        $this->fallbackEnabled = config('otp.providers.authentica.fallback_enabled', true);
        $this->templateId = config('otp.providers.authentica.template_id');
    }

    public function getName(): string
    {
        return 'authentica';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * إرسال OTP عبر Authentica
     * POST https://api.authentica.sa/api/v2/send-otp
     */
    public function sendOtp(string $phoneNumber, string $channel = 'sms', array $options = []): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'خدمة Authentica غير مهيأة',
            ];
        }

        $channel = $channel ?: $this->defaultChannel;

        // Rate limiting
        $cacheKey = "otp_attempts_{$phoneNumber}";
        $attempts = (int) Cache::get($cacheKey, 0);

        if ($attempts >= $this->maxAttempts) {
            return [
                'success' => false,
                'message' => 'تم تجاوز الحد الأقصى لمحاولات الإرسال. يرجى المحاولة لاحقاً',
            ];
        }

        try {
            $body = [
                'method' => $channel,
                'phone' => $phoneNumber,
            ];

            // Template ID
            if ($this->templateId) {
                $body['template_id'] = $this->templateId;
            }

            // Fallback — if primary is SMS, fallback to WhatsApp and vice versa
            if ($this->fallbackEnabled) {
                $body['fallback_phone'] = $phoneNumber;
            }

            // Custom OTP if provided
            if (!empty($options['otp'])) {
                $body['otp'] = $options['otp'];
            }

            $response = Http::withHeaders([
                'X-Authorization' => $this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/send-otp", $body);

            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? false)) {
                // Increment rate limit
                Cache::put($cacheKey, $attempts + 1, now()->addHour());

                Log::info('Authentica OTP sent', [
                    'phone' => $this->maskPhone($phoneNumber),
                    'channel' => $channel,
                    'provider' => 'authentica',
                ]);

                return [
                    'success' => true,
                    'message' => $channel === 'whatsapp'
                        ? 'تم إرسال رمز التحقق عبر واتساب'
                        : 'تم إرسال رمز التحقق عبر رسالة نصية',
                    'channel' => $channel,
                    'provider' => 'authentica',
                ];
            }

            Log::error('Authentica send OTP failed', [
                'phone' => $this->maskPhone($phoneNumber),
                'channel' => $channel,
                'status' => $response->status(),
                'response' => $data,
            ]);

            return [
                'success' => false,
                'message' => $data['message'] ?? 'فشل إرسال رمز التحقق',
            ];
        } catch (\Exception $e) {
            Log::error('Authentica send OTP exception', [
                'phone' => $this->maskPhone($phoneNumber),
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'خطأ في الاتصال بخدمة الرسائل',
            ];
        }
    }

    /**
     * التحقق من OTP عبر Authentica
     * POST https://api.authentica.sa/api/v2/verify-otp
     */
    public function verifyOtp(string $phoneNumber, string $code): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'خدمة Authentica غير مهيأة',
                'valid' => false,
            ];
        }

        try {
            $response = Http::withHeaders([
                'X-Authorization' => $this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/verify-otp", [
                'phone' => $phoneNumber,
                'otp' => $code,
            ]);

            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? false)) {
                // Clear rate limit on success
                Cache::forget("otp_attempts_{$phoneNumber}");

                Log::info('Authentica OTP verified', [
                    'phone' => $this->maskPhone($phoneNumber),
                    'provider' => 'authentica',
                ]);

                return [
                    'success' => true,
                    'message' => 'تم التحقق بنجاح',
                    'valid' => true,
                ];
            }

            Log::warning('Authentica OTP verification failed', [
                'phone' => $this->maskPhone($phoneNumber),
                'status' => $response->status(),
                'response' => $data,
            ]);

            return [
                'success' => false,
                'message' => $data['message'] ?? 'رمز التحقق غير صحيح',
                'valid' => false,
            ];
        } catch (\Exception $e) {
            Log::error('Authentica verify OTP exception', [
                'phone' => $this->maskPhone($phoneNumber),
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'خطأ في الاتصال بخدمة التحقق',
                'valid' => false,
            ];
        }
    }

    /**
     * الحصول على رصيد الحساب
     * GET https://api.authentica.sa/api/v2/balance
     */
    public function getBalance(): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'خدمة Authentica غير مهيأة',
            ];
        }

        try {
            $response = Http::withHeaders([
                'X-Authorization' => $this->apiKey,
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/balance");

            $data = $response->json();

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => [
                        'provider' => 'authentica',
                        'balance' => $data['balance'] ?? $data['data']['balance'] ?? $data,
                        'raw' => $data,
                    ],
                ];
            }

            return [
                'success' => false,
                'message' => $data['message'] ?? 'فشل جلب الرصيد',
            ];
        } catch (\Exception $e) {
            Log::error('Authentica get balance exception', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'خطأ في الاتصال بالخدمة',
            ];
        }
    }

    /**
     * إرسال رسالة SMS مخصصة (ليست OTP)
     * POST https://api.authentica.sa/api/v2/send-sms
     */
    public function sendSms(string $phoneNumber, string $message, ?string $senderName = null): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'خدمة Authentica غير مهيأة'];
        }

        try {
            $body = [
                'phone' => $phoneNumber,
                'message' => $message,
            ];

            if ($senderName) {
                $body['sender_name'] = $senderName;
            }

            $response = Http::withHeaders([
                'X-Authorization' => $this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/send-sms", $body);

            $data = $response->json();

            if ($response->successful() && ($data['success'] ?? false)) {
                return ['success' => true, 'message' => 'تم إرسال الرسالة بنجاح'];
            }

            return ['success' => false, 'message' => $data['message'] ?? 'فشل إرسال الرسالة'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'خطأ في الاتصال بالخدمة'];
        }
    }

    protected function maskPhone(string $phone): string
    {
        if (strlen($phone) <= 6) {
            return '***' . substr($phone, -2);
        }
        return substr($phone, 0, 4) . '****' . substr($phone, -2);
    }
}
