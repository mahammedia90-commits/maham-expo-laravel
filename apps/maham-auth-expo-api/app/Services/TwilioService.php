<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TwilioService
{
    protected string $accountSid;
    protected string $authToken;
    protected string $verifyServiceSid;
    protected string $baseUrl;
    protected string $defaultChannel;
    protected int $maxAttempts;

    public function __construct()
    {
        $this->accountSid = config('twilio.account_sid');
        $this->authToken = config('twilio.auth_token');
        $this->verifyServiceSid = config('twilio.verify_service_sid');
        $this->baseUrl = rtrim(config('twilio.base_url'), '/');
        $this->defaultChannel = config('twilio.default_channel', 'sms');
        $this->maxAttempts = config('twilio.max_attempts_per_hour', 5);
    }

    /**
     * Send OTP verification code via SMS or WhatsApp
     *
     * @param string $phoneNumber Phone number in E.164 format (+966XXXXXXXXX)
     * @param string $channel 'sms' or 'whatsapp'
     * @return array
     */
    public function sendOtp(string $phoneNumber, string $channel = null): array
    {
        // Check if SMS/OTP is enabled from dashboard
        if (!config('twilio.enabled', true)) {
            return [
                'success' => false,
                'message' => 'خدمة الرسائل النصية معطلة حالياً',
            ];
        }

        $channel = $channel ?? $this->defaultChannel;

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
            $url = "{$this->baseUrl}/Services/{$this->verifyServiceSid}/Verifications";

            $response = Http::withBasicAuth($this->accountSid, $this->authToken)
                ->asForm()
                ->post($url, [
                    'To' => $phoneNumber,
                    'Channel' => $channel,
                ]);

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'pending') {
                // Increment rate limit counter
                Cache::put($cacheKey, $attempts + 1, now()->addHour());

                Log::info('OTP sent successfully', [
                    'phone' => $this->maskPhone($phoneNumber),
                    'channel' => $channel,
                ]);

                return [
                    'success' => true,
                    'message' => $channel === 'whatsapp'
                        ? 'تم إرسال رمز التحقق عبر واتساب'
                        : 'تم إرسال رمز التحقق عبر رسالة نصية',
                    'channel' => $channel,
                ];
            }

            Log::error('Twilio send OTP failed', [
                'phone' => $this->maskPhone($phoneNumber),
                'channel' => $channel,
                'response' => $data,
            ]);

            return [
                'success' => false,
                'message' => $data['message'] ?? 'فشل إرسال رمز التحقق',
            ];
        } catch (\Exception $e) {
            Log::error('Twilio send OTP exception', [
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
     * Verify the OTP code
     *
     * @param string $phoneNumber Phone number in E.164 format
     * @param string $code The OTP code to verify
     * @return array
     */
    public function verifyOtp(string $phoneNumber, string $code): array
    {
        try {
            $url = "{$this->baseUrl}/Services/{$this->verifyServiceSid}/VerificationCheck";

            $response = Http::withBasicAuth($this->accountSid, $this->authToken)
                ->asForm()
                ->post($url, [
                    'To' => $phoneNumber,
                    'Code' => $code,
                ]);

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'approved') {
                // Clear rate limit on success
                Cache::forget("otp_attempts_{$phoneNumber}");

                Log::info('OTP verified successfully', [
                    'phone' => $this->maskPhone($phoneNumber),
                ]);

                return [
                    'success' => true,
                    'message' => 'تم التحقق بنجاح',
                    'valid' => true,
                ];
            }

            if ($response->successful() && ($data['status'] ?? '') === 'pending') {
                return [
                    'success' => false,
                    'message' => 'رمز التحقق غير صحيح',
                    'valid' => false,
                ];
            }

            Log::warning('OTP verification failed', [
                'phone' => $this->maskPhone($phoneNumber),
                'response' => $data,
            ]);

            return [
                'success' => false,
                'message' => $data['message'] ?? 'فشل التحقق من الرمز',
                'valid' => false,
            ];
        } catch (\Exception $e) {
            Log::error('Twilio verify OTP exception', [
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
     * Mask phone number for logging
     */
    protected function maskPhone(string $phone): string
    {
        if (strlen($phone) <= 6) {
            return '***' . substr($phone, -2);
        }

        return substr($phone, 0, 4) . '****' . substr($phone, -2);
    }
}
