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
use Illuminate\Support\Facades\Validator;

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
     * Save KYC step data (step-based wizard)
     */
    public function saveKycStep(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');
        $step = (int) $request->input('step', 1);

        if ($step < 1 || $step > 5) {
            return ApiResponse::error('Invalid step', ApiErrorCode::VALIDATION_FAILED);
        }

        // Validate step-specific data
        $rules = $this->getKycStepRules($step);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors());
        }

        // Get or create profile
        $profile = BusinessProfile::forUser($userId)->first();

        if (!$profile) {
            $profile = BusinessProfile::create([
                'user_id' => $userId,
                'company_name' => $request->input('company_name', ''),
                'contact_phone' => $request->input('contact_phone', ''),
                'business_type' => 'merchant',
                'status' => 'pending',
                'kyc_step' => 1,
            ]);
        }

        // Don't allow changes if approved (must be pending/rejected)
        if ($profile->is_approved && $step < 5) {
            return ApiResponse::error(
                __('messages.profile.cannot_be_modified'),
                ApiErrorCode::PROFILE_CANNOT_BE_MODIFIED
            );
        }

        $data = $validator->validated();

        // Step-specific processing
        switch ($step) {
            case 1: // Personal Data
                $profile->update([
                    'full_name' => $data['full_name'] ?? $profile->full_name,
                    'national_id_number' => $data['national_id_number'] ?? $profile->national_id_number,
                    'contact_phone' => $data['contact_phone'] ?? $profile->contact_phone,
                    'contact_email' => $data['contact_email'] ?? $profile->contact_email,
                    'date_of_birth' => $data['date_of_birth'] ?? $profile->date_of_birth,
                    'nationality' => $data['nationality'] ?? $profile->nationality,
                    'city' => $data['city'] ?? $profile->city,
                    'company_address' => $data['address'] ?? $profile->company_address,
                    'kyc_step' => max($profile->kyc_step, 2),
                ]);
                break;

            case 2: // Company Data
                $profile->update([
                    'company_name' => $data['company_name'] ?? $profile->company_name,
                    'commercial_registration_number' => $data['commercial_registration_number'] ?? $profile->commercial_registration_number,
                    'business_activity_type' => $data['business_activity_type'] ?? $profile->business_activity_type,
                    'establishment_year' => $data['establishment_year'] ?? $profile->establishment_year,
                    'vat_number' => $data['vat_number'] ?? $profile->vat_number,
                    'national_address' => $data['national_address'] ?? $profile->national_address,
                    'employee_count' => $data['employee_count'] ?? $profile->employee_count,
                    'website' => $data['website'] ?? $profile->website,
                    'kyc_step' => max($profile->kyc_step, 3),
                ]);
                break;

            case 3: // Bank Account
                $profile->update([
                    'bank_name' => $data['bank_name'] ?? $profile->bank_name,
                    'iban' => $data['iban'] ?? $profile->iban,
                    'account_holder_name' => $data['account_holder_name'] ?? $profile->account_holder_name,
                    'account_number' => $data['account_number'] ?? $profile->account_number,
                    'kyc_step' => max($profile->kyc_step, 4),
                ]);
                break;

            case 4: // Document Uploads
                $fileData = $this->handleKycDocumentUploads($request, $profile);
                $fileData['kyc_step'] = max($profile->kyc_step, 5);
                $profile->update($fileData);
                break;

            case 5: // Legal Declaration & Submit
                if (!$request->boolean('legal_declaration_accepted')) {
                    return ApiResponse::error(
                        'يجب الموافقة على الإقرار القانوني',
                        ApiErrorCode::VALIDATION_FAILED
                    );
                }
                $profile->update([
                    'legal_declaration_accepted' => true,
                    'legal_declaration_accepted_at' => now(),
                    'kyc_submitted_at' => now(),
                    'status' => 'pending',
                    'rejection_reason' => null,
                ]);
                break;
        }

        return ApiResponse::success(
            new BusinessProfileResource($profile->fresh()),
            'تم حفظ البيانات بنجاح'
        );
    }

    /**
     * Get KYC step validation rules
     */
    protected function getKycStepRules(int $step): array
    {
        return match ($step) {
            1 => [
                'step' => 'required|integer',
                'full_name' => 'required|string|max:255',
                'national_id_number' => 'required|string|max:20',
                'contact_phone' => 'required|string|max:20',
                'contact_email' => 'nullable|email|max:255',
                'date_of_birth' => 'nullable|date',
                'nationality' => 'nullable|string|max:100',
                'city' => 'nullable|string|max:100',
                'address' => 'nullable|string|max:500',
            ],
            2 => [
                'step' => 'required|integer',
                'company_name' => 'required|string|max:255',
                'commercial_registration_number' => 'required|string|max:50',
                'business_activity_type' => 'nullable|string|max:255',
                'establishment_year' => 'nullable|integer|min:1900|max:' . date('Y'),
                'vat_number' => 'nullable|string|max:15',
                'national_address' => 'nullable|string|max:500',
                'employee_count' => 'nullable|string|max:50',
                'website' => 'nullable|string|max:255',
            ],
            3 => [
                'step' => 'required|integer',
                'bank_name' => 'required|string|max:255',
                'iban' => 'required|string|max:34',
                'account_holder_name' => 'required|string|max:255',
                'account_number' => 'nullable|string|max:30',
            ],
            4 => [
                'step' => 'required|integer',
                'national_id_image' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
                'commercial_registration_image' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
                'vat_certificate_image' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
                'authorization_letter_image' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
                'national_address_doc' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
                'bank_letter_image' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:5120',
                'company_profile_doc' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:10240',
                'product_catalog_doc' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:10240',
            ],
            5 => [
                'step' => 'required|integer',
                'legal_declaration_accepted' => 'required|boolean',
            ],
            default => [],
        };
    }

    /**
     * Handle KYC document uploads (step 4)
     */
    protected function handleKycDocumentUploads(Request $request, BusinessProfile $profile): array
    {
        $uploadPath = config('expo-api.uploads.paths.profiles') . '/kyc';
        $data = [];

        $fields = [
            'national_id_image',
            'commercial_registration_image',
            'vat_certificate_image',
            'authorization_letter_image',
            'national_address_doc',
            'bank_letter_image',
            'company_profile_doc',
            'product_catalog_doc',
        ];

        foreach ($fields as $field) {
            if ($request->hasFile($field)) {
                // Delete old file
                if ($profile->$field) {
                    Storage::disk('public')->delete($profile->$field);
                }
                $data[$field] = $request->file($field)->store($uploadPath, 'public');
            }
        }

        return $data;
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

        // Avatar
        if ($request->hasFile('avatar')) {
            if ($profile && $profile->avatar) {
                Storage::disk('public')->delete($profile->avatar);
            }
            $data['avatar'] = $request->file('avatar')
                ->store($uploadPath . '/avatars', 'public');
        }

        return $data;
    }
}
