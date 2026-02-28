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
        $token = $request->bearerToken();

        if (!$token) {
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
