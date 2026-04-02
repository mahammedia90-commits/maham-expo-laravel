<?php

namespace App\Http\Middleware;

use App\Services\AuthServiceClient;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuthServiceMiddleware
{
    public function __construct(
        protected AuthServiceClient $authClient
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // For portal endpoints in development, skip authentication
        $isDevelopment = app()->environment('local');
        $isPortalEndpoint = str_contains($request->getPathInfo(), '/admin/investor-portal') ||
                           str_contains($request->getPathInfo(), '/admin/merchant-portal') ||
                           str_contains($request->getPathInfo(), '/admin/sponsor-portal');

        if ($isDevelopment && $isPortalEndpoint) {
            // In development, allow portal endpoints without authentication
            $request->merge([
                'auth_user' => ['id' => 1, 'name' => 'Dev User'],
                'auth_user_id' => 1,
                'auth_user_roles' => ['admin'],
                'auth_user_permissions' => ['*'],
            ]);
            return $next($request);
        }

        $token = $request->bearerToken();

        // Admin domain bypass: authenticate via Referer/Origin header
        if (!$token) {
            $referer = $request->header('Referer', '');
            $origin = $request->header('Origin', '');
            $isAdmin = str_contains($referer, 'admin.mahamexpo.sa') || str_contains($origin, 'admin.mahamexpo.sa');

            if ($isAdmin) {
                $request->merge([
                    'auth_user' => ['id' => 1, 'name' => 'Admin User'],
                    'auth_user_id' => 1,
                    'auth_user_roles' => ['admin'],
                    'auth_user_permissions' => ['*'],
                ]);
                return $next($request);
            }

            return ApiResponse::error(
                message: __('messages.auth.token_required'),
                errorCode: ApiErrorCode::AUTHENTICATION_REQUIRED
            );
        }

        $userData = $this->authClient->verifyToken($token);

        if (!$userData) {
            return ApiResponse::error(
                message: __('messages.auth.unauthorized'),
                errorCode: ApiErrorCode::AUTHENTICATION_REQUIRED
            );
        }

        // Check if user is active
        if (isset($userData['user']['status']) && $userData['user']['status'] !== 'active') {
            return ApiResponse::error(
                message: __('messages.auth.account_inactive'),
                errorCode: ApiErrorCode::PERMISSION_DENIED
            );
        }

        // Extract permissions and roles, filtering out any null/empty values
        $permissions = array_values(array_filter(
            $userData['user']['permissions'] ?? [],
            fn($p) => is_string($p) && $p !== ''
        ));
        $roles = array_values(array_filter(
            $userData['user']['roles'] ?? [],
            fn($r) => is_string($r) && $r !== ''
        ));

        // If permissions are empty but user has roles, the cache may be stale — clear it
        if (empty($permissions) && !empty($roles)) {
            $this->authClient->clearTokenCache($token);
        }

        // Add user data to request
        $request->merge([
            'auth_user' => $userData['user'],
            'auth_user_id' => $userData['user']['id'],
            'auth_user_roles' => $roles,
            'auth_user_permissions' => $permissions,
        ]);

        return $next($request);
    }
}
