<?php

namespace App\Contracts;

/**
 * واجهة موحدة لمزودي خدمة OTP
 * كل مزود (Authentica, Twilio, etc.) يطبق هذه الواجهة
 */
interface OtpProviderInterface
{
    /**
     * اسم المزود
     */
    public function getName(): string;

    /**
     * إرسال رمز OTP
     *
     * @param string $phoneNumber رقم الهاتف بصيغة E.164 (+966XXXXXXXXX)
     * @param string $channel قناة الإرسال: sms, whatsapp
     * @param array $options خيارات إضافية (fallback, template_id, etc.)
     * @return array ['success' => bool, 'message' => string, ...]
     */
    public function sendOtp(string $phoneNumber, string $channel = 'sms', array $options = []): array;

    /**
     * التحقق من رمز OTP
     *
     * @param string $phoneNumber رقم الهاتف
     * @param string $code الرمز المدخل
     * @return array ['success' => bool, 'valid' => bool, 'message' => string]
     */
    public function verifyOtp(string $phoneNumber, string $code): array;

    /**
     * الحصول على رصيد الحساب
     *
     * @return array ['success' => bool, 'data' => [...]]
     */
    public function getBalance(): array;

    /**
     * هل المزود مفعل ومهيأ؟
     */
    public function isConfigured(): bool;
}
