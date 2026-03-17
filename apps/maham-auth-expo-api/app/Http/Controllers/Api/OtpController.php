<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\OtpProviderManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * إدارة مزودي OTP — الرصيد، الإحصائيات، الإعدادات
 * Admin only endpoints
 */
class OtpController extends Controller
{
    public function __construct(
        protected OtpProviderManager $otpProvider
    ) {}

    /**
     * الحصول على رصيد المزود النشط
     * GET /v1/admin/otp/balance
     */
    public function balance(): JsonResponse
    {
        $result = $this->otpProvider->getBalance();

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 400);
        }

        return response()->json([
            'success' => true,
            'data' => $result['data'],
        ]);
    }

    /**
     * الحصول على رصيد كل المزودين
     * GET /v1/admin/otp/balances
     */
    public function allBalances(): JsonResponse
    {
        $balances = $this->otpProvider->getAllBalances();

        return response()->json([
            'success' => true,
            'data' => $balances,
        ]);
    }

    /**
     * حالة كل المزودين
     * GET /v1/admin/otp/providers
     */
    public function providers(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'active_provider' => $this->otpProvider->getActiveProviderName(),
                'test_mode' => $this->otpProvider->isTestMode(),
                'default_channel' => config('otp.default_channel', 'sms'),
                'max_attempts_per_hour' => config('otp.max_attempts_per_hour', 5),
                'enable_provider_fallback' => config('otp.enable_provider_fallback', false),
                'providers' => $this->otpProvider->getProvidersStatus(),
            ],
        ]);
    }

    /**
     * إحصائيات OTP
     * GET /v1/admin/otp/stats
     */
    public function stats(): JsonResponse
    {
        // جلب الإحصائيات من الـ audit logs
        $today = now()->startOfDay();
        $thisWeek = now()->startOfWeek();
        $thisMonth = now()->startOfMonth();

        $stats = [
            'today' => [
                'otp_sent' => \App\Models\AuditLog::where('action', 'phone_otp_sent')
                    ->where('created_at', '>=', $today)->count()
                    + \App\Models\AuditLog::where('action', 'login_otp')
                    ->where('created_at', '>=', $today)->count(),
                'otp_logins' => \App\Models\AuditLog::where('action', 'login_otp')
                    ->where('created_at', '>=', $today)->count(),
                'otp_registrations' => \App\Models\AuditLog::where('action', 'register_otp')
                    ->where('created_at', '>=', $today)->count(),
            ],
            'this_week' => [
                'otp_sent' => \App\Models\AuditLog::where('action', 'phone_otp_sent')
                    ->where('created_at', '>=', $thisWeek)->count()
                    + \App\Models\AuditLog::where('action', 'login_otp')
                    ->where('created_at', '>=', $thisWeek)->count(),
                'otp_logins' => \App\Models\AuditLog::where('action', 'login_otp')
                    ->where('created_at', '>=', $thisWeek)->count(),
                'otp_registrations' => \App\Models\AuditLog::where('action', 'register_otp')
                    ->where('created_at', '>=', $thisWeek)->count(),
            ],
            'this_month' => [
                'otp_sent' => \App\Models\AuditLog::where('action', 'phone_otp_sent')
                    ->where('created_at', '>=', $thisMonth)->count()
                    + \App\Models\AuditLog::where('action', 'login_otp')
                    ->where('created_at', '>=', $thisMonth)->count(),
                'otp_logins' => \App\Models\AuditLog::where('action', 'login_otp')
                    ->where('created_at', '>=', $thisMonth)->count(),
                'otp_registrations' => \App\Models\AuditLog::where('action', 'register_otp')
                    ->where('created_at', '>=', $thisMonth)->count(),
            ],
            'active_provider' => $this->otpProvider->getActiveProviderName(),
            'test_mode' => $this->otpProvider->isTestMode(),
        ];

        // إضافة الرصيد
        $balance = $this->otpProvider->getBalance();
        if ($balance['success']) {
            $stats['balance'] = $balance['data'];
        }

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * اختبار إرسال OTP (للأدمن فقط)
     * POST /v1/admin/otp/test-send
     */
    public function testSend(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'channel' => 'nullable|string|in:sms,whatsapp',
            'provider' => 'nullable|string|in:authentica,twilio',
        ]);

        // إذا في وضع الاختبار ما نرسل فعلياً
        if ($this->otpProvider->isTestMode()) {
            return response()->json([
                'success' => true,
                'message' => 'وضع الاختبار مفعل — لم يتم إرسال فعلي',
                'data' => [
                    'test_mode' => true,
                    'provider' => $this->otpProvider->getActiveProviderName(),
                ],
            ]);
        }

        $result = $this->otpProvider->sendOtp(
            $request->phone,
            $request->input('channel', 'sms')
        );

        return response()->json($result);
    }
}
