<?php

namespace App\Http\Middleware;

use App\Models\Service;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use App\Services\Cache\CacheService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServiceTokenMiddleware
{
    public function __construct(
        private CacheService $cacheService
    ) {}

    /**
     * التحقق من توكن الخدمة
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-Service-Token');

        // ═══════════════════════════════════════════════════════════════
        // 1. فحص وجود التوكن
        // ═══════════════════════════════════════════════════════════════
        
        if (!$token) {
            return ApiResponse::error(
                message: __('messages.services.token_required'),
                errorCode: ApiErrorCode::SERVICE_TOKEN_REQUIRED
            );
        }

        // ═══════════════════════════════════════════════════════════════
        // 2. فحص التوكن الأساسي من الإعدادات (للتطوير)
        // ═══════════════════════════════════════════════════════════════
        
        $configToken = config('auth-service.service_token');
        if ($token === $configToken) {
            $request->merge([
                '_service' => null,
                '_service_name' => 'config_token',
            ]);
            return $next($request);
        }

        // ═══════════════════════════════════════════════════════════════
        // 3. فحص الكاش
        // ═══════════════════════════════════════════════════════════════
        
        $cachedService = $this->cacheService->getService($token);
        if ($cachedService) {
            // فحص IP
            if (!$this->isIpAllowed($cachedService, $request->ip())) {
                return ApiResponse::error(
                    message: __('messages.services.ip_not_allowed'),
                    errorCode: ApiErrorCode::SERVICE_IP_NOT_ALLOWED
                );
            }

            $request->merge([
                '_service' => $cachedService,
                '_service_name' => $cachedService['name'],
            ]);
            return $next($request);
        }

        // ═══════════════════════════════════════════════════════════════
        // 4. فحص قاعدة البيانات
        // ═══════════════════════════════════════════════════════════════
        
        $service = Service::findByToken($token);

        if (!$service) {
            return ApiResponse::error(
                message: __('messages.services.token_invalid'),
                errorCode: ApiErrorCode::SERVICE_TOKEN_INVALID
            );
        }

        // فحص الحالة
        if (!$service->isActive()) {
            $errorCode = $service->status === 'suspended' 
                ? ApiErrorCode::SERVICE_SUSPENDED 
                : ApiErrorCode::SERVICE_DISABLED;
            
            return ApiResponse::error(
                message: __("messages.services.{$service->status}"),
                errorCode: $errorCode
            );
        }

        // فحص IP
        if (!$service->isIpAllowed($request->ip())) {
            return ApiResponse::error(
                message: __('messages.services.ip_not_allowed'),
                errorCode: ApiErrorCode::SERVICE_IP_NOT_ALLOWED
            );
        }

        // كاش الخدمة
        $this->cacheService->cacheService($token, [
            'id' => $service->id,
            'name' => $service->name,
            'allowed_ips' => $service->allowed_ips,
            'allowed_permissions' => $service->allowed_permissions,
        ]);

        // تسجيل الاستخدام
        $service->recordUsage();

        // إضافة معلومات الخدمة للطلب
        $request->merge([
            '_service' => $service,
            '_service_name' => $service->name,
        ]);

        return $next($request);
    }

    /**
     * فحص IP من البيانات المخزنة في الكاش
     */
    private function isIpAllowed(array $serviceData, ?string $ip): bool
    {
        if (empty($serviceData['allowed_ips'])) {
            return true;
        }

        return in_array($ip, $serviceData['allowed_ips']);
    }
}
