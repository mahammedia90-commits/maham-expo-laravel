<?php

namespace App\Http\Middleware;

use App\Services\AuthServiceClient;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function __construct(
        protected AuthServiceClient $authClient
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $userRoles = $request->input('auth_user_roles', []);
        $userPermissions = $request->input('auth_user_permissions', []);

        // Super admin bypasses all permission checks
        if ($this->authClient->hasRole($userRoles, 'super-admin') 
            || in_array('super_admin', $userRoles) 
            || in_array('admin', $userRoles)) {
            return $next($request);
        }

        // Wildcard permission bypass
        if (in_array('*', $userPermissions)) {
            return $next($request);
        }

        // Check if user has the required permission
        if (!in_array($permission, $userPermissions)) {
            return ApiResponse::error(
                message: __('messages.auth.permission_denied'),
                errorCode: ApiErrorCode::PERMISSION_DENIED,
                httpCode: 403,
                errors: ['required_permission' => $permission]
            );
        }

        return $next($request);
    }
}
