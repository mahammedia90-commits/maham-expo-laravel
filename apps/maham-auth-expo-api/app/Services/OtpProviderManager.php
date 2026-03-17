<?php

namespace App\Services;

use App\Contracts\OtpProviderInterface;
use Illuminate\Support\Facades\Log;

/**
 * مدير مزودي OTP — يختار المزود النشط ويوفر fallback
 *
 * يدعم: authentica, twilio, test
 * يمكن تغيير المزود من الإعدادات أو .env
 */
class OtpProviderManager
{
    protected array $providers = [];
    protected string $activeProvider;
    protected bool $testMode;

    public function __construct(
        protected AuthenticaService $authenticaService,
        protected TwilioService $twilioService,
    ) {
        $this->activeProvider = config('otp.active_provider', 'authentica');
        $this->testMode = config('otp.test_mode', false);

        // تسجيل المزودين
        $this->providers = [
            'authentica' => $this->authenticaService,
            'twilio' => $this->twilioService,
        ];
    }

    /**
     * الحصول على المزود النشط
     */
    public function getActiveProvider(): OtpProviderInterface
    {
        $provider = $this->providers[$this->activeProvider] ?? null;

        if (!$provider || !$provider->isConfigured()) {
            // Fallback: جرب أي مزود مهيأ
            foreach ($this->providers as $name => $p) {
                if ($p->isConfigured()) {
                    Log::warning("OTP provider '{$this->activeProvider}' not configured, falling back to '{$name}'");
                    return $p;
                }
            }

            // لا يوجد مزود مهيأ
            throw new \RuntimeException('لا يوجد مزود OTP مهيأ. يرجى إعداد Authentica أو Twilio.');
        }

        return $provider;
    }

    /**
     * هل في وضع الاختبار؟
     */
    public function isTestMode(): bool
    {
        return $this->testMode;
    }

    /**
     * إرسال OTP — يختار المزود تلقائياً
     */
    public function sendOtp(string $phoneNumber, string $channel = 'sms', array $options = []): array
    {
        $provider = $this->getActiveProvider();
        $result = $provider->sendOtp($phoneNumber, $channel, $options);

        // إذا فشل والـ fallback مفعل، جرب المزود الثاني
        if (!$result['success'] && config('otp.enable_provider_fallback', false)) {
            $fallbackProvider = $this->getFallbackProvider($provider->getName());
            if ($fallbackProvider) {
                Log::info("OTP send failed on {$provider->getName()}, trying fallback {$fallbackProvider->getName()}");
                $result = $fallbackProvider->sendOtp($phoneNumber, $channel, $options);
                if ($result['success']) {
                    $result['fallback_used'] = true;
                }
            }
        }

        return $result;
    }

    /**
     * التحقق من OTP
     */
    public function verifyOtp(string $phoneNumber, string $code): array
    {
        return $this->getActiveProvider()->verifyOtp($phoneNumber, $code);
    }

    /**
     * الحصول على الرصيد من المزود النشط
     */
    public function getBalance(): array
    {
        return $this->getActiveProvider()->getBalance();
    }

    /**
     * الحصول على رصيد كل المزودين
     */
    public function getAllBalances(): array
    {
        $balances = [];
        foreach ($this->providers as $name => $provider) {
            if ($provider->isConfigured()) {
                $balances[$name] = $provider->getBalance();
            } else {
                $balances[$name] = [
                    'success' => false,
                    'message' => 'غير مهيأ',
                ];
            }
        }
        return $balances;
    }

    /**
     * معلومات حالة كل المزودين
     */
    public function getProvidersStatus(): array
    {
        $status = [];
        foreach ($this->providers as $name => $provider) {
            $status[$name] = [
                'name' => $name,
                'configured' => $provider->isConfigured(),
                'active' => $name === $this->activeProvider,
            ];
        }
        return $status;
    }

    /**
     * اسم المزود النشط
     */
    public function getActiveProviderName(): string
    {
        return $this->activeProvider;
    }

    /**
     * قائمة المزودين المتاحين
     */
    public function getAvailableProviders(): array
    {
        return array_keys($this->providers);
    }

    /**
     * الحصول على مزود بديل
     */
    protected function getFallbackProvider(string $currentProvider): ?OtpProviderInterface
    {
        foreach ($this->providers as $name => $provider) {
            if ($name !== $currentProvider && $provider->isConfigured()) {
                return $provider;
            }
        }
        return null;
    }
}
