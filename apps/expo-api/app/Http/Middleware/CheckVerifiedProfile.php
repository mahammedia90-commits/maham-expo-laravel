<?php

namespace App\Http\Middleware;

use App\Models\BusinessProfile;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckVerifiedProfile
{
    /**
     * Handle an incoming request.
     * Ensures user has a verified business profile for certain actions.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userId = $request->input('auth_user_id');

        if (!$userId) {
            return ApiResponse::error(
                message: __('messages.auth.token_required'),
                errorCode: ApiErrorCode::AUTHENTICATION_REQUIRED
            );
        }

        $profile = BusinessProfile::where('user_id', $userId)
            ->where('status', 'approved')
            ->first();

        if (!$profile) {
            // Check if profile exists but not approved
            $pendingProfile = BusinessProfile::where('user_id', $userId)->first();

            if ($pendingProfile) {
                $errorCode = match($pendingProfile->status) {
                    'pending' => ApiErrorCode::PROFILE_PENDING,
                    'rejected' => ApiErrorCode::PROFILE_REJECTED,
                    default => ApiErrorCode::PROFILE_NOT_VERIFIED,
                };

                return ApiResponse::error(
                    message: __('messages.profile.' . $pendingProfile->status),
                    errorCode: $errorCode
                );
            }

            return ApiResponse::error(
                message: __('messages.profile.required'),
                errorCode: ApiErrorCode::PROFILE_REQUIRED
            );
        }

        // Add profile to request for use in controllers
        $request->merge(['business_profile' => $profile]);

        return $next($request);
    }
}
