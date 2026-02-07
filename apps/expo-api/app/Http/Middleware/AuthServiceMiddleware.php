<?php

namespace App\Http\Middleware;

use App\Services\AuthServiceClient;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
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

        // Add user data to request
        $request->merge([
            'auth_user' => $userData['user'],
            'auth_user_id' => $userData['user']['id'],
            'auth_user_roles' => $userData['user']['roles'] ?? [],
            'auth_user_permissions' => $userData['user']['permissions'] ?? [],
        ]);

        return $next($request);
    }
}
