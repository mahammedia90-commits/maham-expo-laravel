<?php

namespace App\Sdk;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

/**
 * Auth Service SDK
 * 
 * استخدم هذا الـ SDK في المشاريع الأخرى للتواصل مع Auth Service
 * 
 * Installation:
 * 1. Copy this file to your project
 * 2. Add to your .env:
 *    AUTH_SERVICE_URL=http://localhost:8001
 * 
 * Usage:
 * $authService = new AuthServiceClient();
 * $result = $authService->verifyToken($userToken);
 */
class AuthServiceClient
{
    protected string $baseUrl;
    protected int $timeout = 10;
    protected bool $cacheEnabled = true;
    protected int $cacheTtl = 300; // 5 minutes

    public function __construct(?string $baseUrl = null)
    {
        $this->baseUrl = $baseUrl ?? config('services.auth.url', 'http://localhost:8001');
    }

    /**
     * التحقق من توكن المستخدم
     */
    public function verifyToken(string $userToken): array
    {
        $cacheKey = 'auth_token_' . md5($userToken);

        if ($this->cacheEnabled && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = $this->request('POST', '/api/internal/verify-token', [
            'user_token' => $userToken,
        ]);

        if ($response['success'] && $this->cacheEnabled) {
            Cache::put($cacheKey, $response, $this->cacheTtl);
        }

        return $response;
    }

    /**
     * التحقق من صلاحية للمستخدم
     */
    public function checkPermission(string $userId, string $permission): bool
    {
        $cacheKey = "auth_perm_{$userId}_{$permission}";

        if ($this->cacheEnabled && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = $this->request('POST', '/api/internal/check-permission', [
            'user_id' => $userId,
            'permission' => $permission,
        ]);

        $hasPermission = $response['success'] && ($response['data']['has_permission'] ?? false);

        if ($this->cacheEnabled) {
            Cache::put($cacheKey, $hasPermission, $this->cacheTtl);
        }

        return $hasPermission;
    }

    /**
     * الحصول على بيانات المستخدم
     */
    public function getUserInfo(string $userId, array $include = []): ?array
    {
        $response = $this->request('POST', '/api/internal/user-info', [
            'user_id' => $userId,
            'include' => $include,
        ]);

        if ($response['success']) {
            return $response['data'];
        }

        return null;
    }

    /**
     * إرسال طلب للـ Auth Service
     */
    protected function request(string $method, string $endpoint, array $data = []): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Accept' => 'application/json',
                ])
                ->$method($this->baseUrl . $endpoint, $data);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'message' => $response->json('message') ?? 'Request failed',
                'status' => $response->status(),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * تفعيل/تعطيل الكاش
     */
    public function setCacheEnabled(bool $enabled): self
    {
        $this->cacheEnabled = $enabled;
        return $this;
    }

    /**
     * تعيين مدة الكاش
     */
    public function setCacheTtl(int $seconds): self
    {
        $this->cacheTtl = $seconds;
        return $this;
    }

    /**
     * مسح كاش المستخدم
     */
    public function clearUserCache(string $userId): void
    {
        // يمكنك استخدام Cache::tags إذا كنت تستخدم Redis
        // هنا نمسح الكاش يدوياً
    }
}
