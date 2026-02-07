<?php

namespace App\Http\Middleware;

use App\Services\AuthServiceClient;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function __construct(
        protected AuthServiceClient $authClient
    ) {}

    /**
     * Handle an incoming request.
     *
     * @param  string  ...$roles  Roles to check (comma-separated or multiple arguments)
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $userRoles = $request->input('auth_user_roles', []);

        // Super admin bypasses all role checks
        if ($this->authClient->hasRole($userRoles, 'super-admin')) {
            return $next($request);
        }

        // Check if user has any of the required roles
        if (!$this->authClient->hasRole($userRoles, $roles)) {
            return ApiResponse::error(
                message: __('messages.auth.role_required'),
                errorCode: ApiErrorCode::ROLE_REQUIRED,
                httpCode: 403,
                errors: ['required_roles' => $roles]
            );
        }

        return $next($request);
    }
}
