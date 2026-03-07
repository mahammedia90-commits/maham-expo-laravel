<?php

namespace App\Sdk;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Auth Service Middleware
 * 
 * استخدم هذا الـ Middleware في المشاريع الأخرى للتحقق من المستخدمين
 * 
 * Usage in Kernel.php:
 * 'auth.service' => \App\Http\Middleware\AuthServiceMiddleware::class,
 * 
 * Usage in routes:
 * Route::middleware('auth.service')->group(function () {
 *     // Protected routes
 * });
 * 
 * Route::middleware('auth.service:orders.view')->group(function () {
 *     // Routes requiring specific permission
 * });
 */
class AuthServiceMiddleware
{
    protected AuthServiceClient $authClient;

    public function __construct()
    {
        $this->authClient = new AuthServiceClient();
    }

    public function handle(Request $request, Closure $next, ?string $permission = null): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Authorization token required',
            ], 401);
        }

        // التحقق من التوكن
        $result = $this->authClient->verifyToken($token);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Invalid token',
            ], 401);
        }

        // حفظ بيانات المستخدم في الـ Request
        $request->merge(['auth_user' => $result['data']['user']]);

        // التحقق من الصلاحية إذا محددة
        if ($permission) {
            $userId = $result['data']['user']['id'];
            
            if (!$this->authClient->checkPermission($userId, $permission)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Permission denied',
                    'required_permission' => $permission,
                ], 403);
            }
        }

        return $next($request);
    }
}
