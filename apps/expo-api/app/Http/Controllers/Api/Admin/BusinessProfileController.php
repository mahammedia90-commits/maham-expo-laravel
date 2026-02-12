<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewRequestRequest;
use App\Http\Resources\BusinessProfileResource;
use App\Models\BusinessProfile;
use App\Models\Notification;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessProfileController extends Controller
{
    use SafeOrderBy;
    /**
     * Get all business profiles (admin)
     */
    public function index(Request $request): JsonResponse
    {
        $query = BusinessProfile::query();

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter by business type
        if ($businessType = $request->input('business_type')) {
            $query->where('business_type', $businessType);
        }

        // Search (sanitized to prevent wildcard injection)
        if ($search = $this->sanitizeSearch($request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                    ->orWhere('company_name_ar', 'like', "%{$search}%")
                    ->orWhere('commercial_registration_number', 'like', "%{$search}%")
                    ->orWhere('contact_phone', 'like', "%{$search}%");
            });
        }

        // Sorting (safe - whitelisted columns only)
        $this->applySafeOrder($query, $request, [
            'created_at', 'company_name', 'status', 'business_type', 'reviewed_at',
        ]);

        $profiles = $query->paginate(15);

        return ApiResponse::paginated(
            $profiles->through(fn($item) => new BusinessProfileResource($item))
        );
    }

    /**
     * Get single business profile
     */
    public function show(BusinessProfile $profile): JsonResponse
    {
        return ApiResponse::success(
            new BusinessProfileResource($profile)
        );
    }

    /**
     * Approve business profile
     */
    public function approve(ReviewRequestRequest $request, BusinessProfile $profile): JsonResponse
    {
        if ($profile->status->value !== 'pending') {
            return ApiResponse::error(
                __('messages.profile.not_pending'),
                'profile_not_pending'
            );
        }

        $adminId = $request->input('auth_user_id');
        $profile->approve($adminId);

        // Send notification to user
        Notification::send(
            userId: $profile->user_id,
            title: 'Profile Verified',
            titleAr: 'تم توثيق الحساب',
            type: 'profile_approved',
            body: 'Your business profile has been verified successfully.',
            bodyAr: 'تم توثيق ملفك التجاري بنجاح',
            data: [
                'profile_id' => $profile->id,
            ]
        );

        return ApiResponse::success(
            new BusinessProfileResource($profile),
            __('messages.profile.approved')
        );
    }

    /**
     * Reject business profile
     */
    public function reject(ReviewRequestRequest $request, BusinessProfile $profile): JsonResponse
    {
        if ($profile->status->value !== 'pending') {
            return ApiResponse::error(
                __('messages.profile.not_pending'),
                'profile_not_pending'
            );
        }

        $adminId = $request->input('auth_user_id');
        $profile->reject($adminId, $request->reason);

        // Send notification to user
        Notification::send(
            userId: $profile->user_id,
            title: 'Profile Rejected',
            titleAr: 'تم رفض توثيق الحساب',
            type: 'profile_rejected',
            body: 'Your business profile verification has been rejected.',
            bodyAr: 'تم رفض طلب توثيق ملفك التجاري',
            data: [
                'profile_id' => $profile->id,
                'reason' => $request->reason,
            ]
        );

        return ApiResponse::success(
            new BusinessProfileResource($profile),
            __('messages.profile.rejected')
        );
    }
}
