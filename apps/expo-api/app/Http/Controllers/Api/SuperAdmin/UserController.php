<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessProfileResource;
use App\Models\BusinessProfile;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * SuperAdmin User Controller
 *
 * Manages business profiles (the user representation in expo-api).
 * Actual user accounts are managed via the Auth Service.
 */
class UserController extends Controller
{
    use SafeOrderBy;

    /**
     * List all business profiles
     */
    public function index(Request $request): JsonResponse
    {
        $query = BusinessProfile::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by business type
        if ($request->has('business_type')) {
            $query->where('business_type', $request->input('business_type'));
        }

        // Search
        if ($search = $this->sanitizeSearch($request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                    ->orWhere('company_name_ar', 'like', "%{$search}%")
                    ->orWhere('contact_email', 'like', "%{$search}%")
                    ->orWhere('contact_phone', 'like', "%{$search}%")
                    ->orWhere('commercial_registration', 'like', "%{$search}%");
            });
        }

        $this->applySafeOrder($query, $request, [
            'company_name', 'company_name_ar', 'status', 'business_type', 'created_at',
        ], 'created_at', 'desc');

        $profiles = $query->paginate($request->input('per_page', 15));

        return ApiResponse::success($profiles);
    }

    /**
     * Show a single business profile
     */
    public function show(BusinessProfile $profile): JsonResponse
    {
        return ApiResponse::success(
            new BusinessProfileResource($profile)
        );
    }

    /**
     * Approve a business profile
     */
    public function approve(Request $request, BusinessProfile $profile): JsonResponse
    {
        if ($profile->status === 'approved') {
            return ApiResponse::error(
                message: __('messages.profile.already_approved'),
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                httpCode: 422
            );
        }

        $profile->update([
            'status' => 'approved',
            'reviewed_by' => $request->input('auth_user_id'),
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        return ApiResponse::success(
            data: new BusinessProfileResource($profile),
            message: __('messages.profile.approved')
        );
    }

    /**
     * Reject a business profile
     */
    public function reject(Request $request, BusinessProfile $profile): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        if ($profile->status === 'rejected') {
            return ApiResponse::error(
                message: __('messages.profile.already_rejected'),
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                httpCode: 422
            );
        }

        $profile->update([
            'status' => 'rejected',
            'reviewed_by' => $request->input('auth_user_id'),
            'reviewed_at' => now(),
            'rejection_reason' => $validated['reason'],
        ]);

        return ApiResponse::success(
            data: new BusinessProfileResource($profile),
            message: __('messages.profile.rejected')
        );
    }

    /**
     * Suspend a business profile
     */
    public function suspend(Request $request, BusinessProfile $profile): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $profile->update([
            'status' => 'suspended',
            'reviewed_by' => $request->input('auth_user_id'),
            'reviewed_at' => now(),
            'rejection_reason' => $validated['reason'],
        ]);

        return ApiResponse::success(
            data: new BusinessProfileResource($profile),
            message: __('messages.profile.suspended')
        );
    }
}
