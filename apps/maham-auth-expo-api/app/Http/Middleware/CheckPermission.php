<?php

namespace App\Http\Middleware;

use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (!$user) {
            return ApiResponse::error(
                message: __('messages.auth.unauthenticated'),
                errorCode: ApiErrorCode::AUTHENTICATION_REQUIRED
            );
        }

        if ($user->hasRole('super-admin')) { 
            return $next($request);
        }

        if (!$user->hasPermissionTo($permission)) {
            return ApiResponse::error(
                message: __('messages.permissions.denied'),
                errorCode: ApiErrorCode::PERMISSION_DENIED,
                httpCode: 403,
                errors: ['required_permission' => $permission]
            );
        }

        return $next($request);
    }
}
