<?php

namespace App\Http\Middleware;

use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * فحص دور المستخدم
     * 
     * الاستخدام في Routes:
     * - middleware('role:admin') - يجب أن يكون admin
     * - middleware('role:admin,super-admin') - يجب أن يكون أحدهما
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // ═══════════════════════════════════════════════════════════════
        // 1. فحص تسجيل الدخول
        // ═══════════════════════════════════════════════════════════════
        
        if (!$user) {
            return ApiResponse::error(
                message: __('messages.auth.unauthenticated'),
                errorCode: ApiErrorCode::AUTHENTICATION_REQUIRED
            );
        }

        // ═══════════════════════════════════════════════════════════════
        // 2. فحص حالة المستخدم
        // ═══════════════════════════════════════════════════════════════
        
        if (!$user->isActive()) {
            $errorCode = match($user->status->value) {
                'suspended' => ApiErrorCode::USER_SUSPENDED,
                'blocked' => ApiErrorCode::USER_BLOCKED,
                'inactive' => ApiErrorCode::USER_DISABLED,
                default => ApiErrorCode::USER_DISABLED,
            };

            return ApiResponse::error(
                message: __("messages.auth.account_{$user->status->value}"),
                errorCode: $errorCode
            );
        }

        // ═══════════════════════════════════════════════════════════════
        // 3. فحص الدور
        // ═══════════════════════════════════════════════════════════════
        
        if (!$user->hasRole($roles)) {
            return ApiResponse::error(
                message: __('messages.permissions.denied'),
                errorCode: ApiErrorCode::PERMISSION_DENIED,
                httpCode: 403,
                errors: [
                    'required_roles' => $roles,
                    'user_roles' => $user->roleNames,
                ]
            );
        }

        return $next($request);
    }
}
