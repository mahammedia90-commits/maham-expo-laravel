<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBusinessProfileRequest;
use App\Http\Requests\UpdateBusinessProfileRequest;
use App\Http\Resources\BusinessProfileResource;
use App\Models\BusinessProfile;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BusinessProfileController extends Controller
{
    /**
     * Get current user's business profile
     */
    public function show(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        $profile = BusinessProfile::forUser($userId)->first();

        if (!$profile) {
            return ApiResponse::notFound(
                __('messages.profile.not_found'),
                'profile'
            );
        }

        return ApiResponse::success(
            new BusinessProfileResource($profile)
        );
    }

    /**
     * Create business profile
     */
    public function store(StoreBusinessProfileRequest $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        // Check if profile already exists
        if (BusinessProfile::forUser($userId)->exists()) {
            return ApiResponse::error(
                __('messages.profile.already_exists'),
                ApiErrorCode::PROFILE_ALREADY_EXISTS
            );
        }

        $data = $request->validated();
        $data['user_id'] = $userId;

        // Handle file uploads
        $data = $this->handleFileUploads($request, $data);

        $profile = BusinessProfile::create($data);

        return ApiResponse::created(
            new BusinessProfileResource($profile),
            __('messages.profile.created')
        );
    }

    /**
     * Update business profile
     */
    public function update(UpdateBusinessProfileRequest $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        $profile = BusinessProfile::forUser($userId)->first();

        if (!$profile) {
            return ApiResponse::notFound(
                __('messages.profile.not_found'),
                'profile'
            );
        }

        // Check if profile can be modified
        if (!$profile->canBeModified()) {
            return ApiResponse::error(
                __('messages.profile.cannot_be_modified'),
                ApiErrorCode::PROFILE_CANNOT_BE_MODIFIED
            );
        }

        $data = $request->validated();

        // Handle file uploads
        $data = $this->handleFileUploads($request, $data, $profile);

        // Reset status to pending if profile was rejected
        if ($profile->is_rejected) {
            $data['status'] = 'pending';
            $data['rejection_reason'] = null;
        }

        $profile->update($data);

        return ApiResponse::success(
            new BusinessProfileResource($profile->fresh()),
            __('messages.profile.updated')
        );
    }

    /**
     * Handle file uploads for profile
     */
    protected function handleFileUploads(Request $request, array $data, ?BusinessProfile $profile = null): array
    {
        $uploadPath = config('expo-api.uploads.paths.profiles');

        // Commercial Registration Image
        if ($request->hasFile('commercial_registration_image')) {
            if ($profile && $profile->commercial_registration_image) {
                Storage::disk('public')->delete($profile->commercial_registration_image);
            }
            $data['commercial_registration_image'] = $request->file('commercial_registration_image')
                ->store($uploadPath, 'public');
        }

        // National ID Image
        if ($request->hasFile('national_id_image')) {
            if ($profile && $profile->national_id_image) {
                Storage::disk('public')->delete($profile->national_id_image);
            }
            $data['national_id_image'] = $request->file('national_id_image')
                ->store($uploadPath, 'public');
        }

        // Company Logo
        if ($request->hasFile('company_logo')) {
            if ($profile && $profile->company_logo) {
                Storage::disk('public')->delete($profile->company_logo);
            }
            $data['company_logo'] = $request->file('company_logo')
                ->store($uploadPath, 'public');
        }

        return $data;
    }
}
