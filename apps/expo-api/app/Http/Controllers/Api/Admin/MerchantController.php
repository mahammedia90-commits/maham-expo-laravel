<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\BusinessType;
use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessProfileResource;
use App\Models\BusinessProfile;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MerchantController extends Controller
{
    use SafeOrderBy;

    /**
     * قائمة التجار
     */
    public function index(Request $request): JsonResponse
    {
        $query = BusinessProfile::where('business_type', BusinessType::MERCHANT);

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Search
        if ($search = $this->sanitizeSearch($request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('company_name_ar', 'like', "%{$search}%")
                    ->orWhere('contact_phone', 'like', "%{$search}%")
                    ->orWhere('contact_email', 'like', "%{$search}%")
                    ->orWhere('commercial_registration_number', 'like', "%{$search}%");
            });
        }

        // Sorting
        $this->applySafeOrder($query, $request, [
            'created_at', 'company_name', 'full_name', 'status', 'reviewed_at',
        ]);

        $profiles = $query->paginate($request->input('per_page', 15));

        return ApiResponse::paginated(
            $profiles->through(fn($item) => new BusinessProfileResource($item))
        );
    }

    /**
     * إنشاء تاجر جديد
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|uuid',
            'full_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'company_name_ar' => 'nullable|string|max:255',
            'commercial_registration_number' => 'nullable|string|max:50',
            'national_id_number' => 'nullable|string|max:20',
            'company_address' => 'nullable|string|max:500',
            'company_address_ar' => 'nullable|string|max:500',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'status' => 'sometimes|in:pending,approved,rejected',
            'notes' => 'nullable|string|max:1000',
            // KYC Personal
            'date_of_birth' => 'nullable|date',
            'nationality' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            // KYC Company
            'business_activity_type' => 'nullable|string|max:255',
            'establishment_year' => 'nullable|integer|min:1900|max:2030',
            'vat_number' => 'nullable|string|max:50',
            'national_address' => 'nullable|string|max:500',
            'employee_count' => 'nullable|integer|min:0',
            // KYC Bank
            'bank_name' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:50',
            'account_holder_name' => 'nullable|string|max:255',
        ]);

        $validated['business_type'] = BusinessType::MERCHANT;

        // Set defaults
        if (!isset($validated['status'])) {
            $validated['status'] = 'approved';
        }

        // If approved by admin, mark as reviewed
        if ($validated['status'] === 'approved') {
            $validated['reviewed_by'] = $request->auth_user_id;
            $validated['reviewed_at'] = now();
        }

        $profile = BusinessProfile::create($validated);

        return ApiResponse::created(
            new BusinessProfileResource($profile),
            'تم إنشاء حساب التاجر بنجاح'
        );
    }

    /**
     * عرض تاجر
     */
    public function show(BusinessProfile $merchant): JsonResponse
    {
        if ($merchant->business_type !== BusinessType::MERCHANT) {
            return ApiResponse::notFound('التاجر غير موجود');
        }

        return ApiResponse::success(new BusinessProfileResource($merchant));
    }

    /**
     * تحديث تاجر
     */
    public function update(Request $request, BusinessProfile $merchant): JsonResponse
    {
        if ($merchant->business_type !== BusinessType::MERCHANT) {
            return ApiResponse::notFound('التاجر غير موجود');
        }

        $validated = $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'company_name' => 'sometimes|string|max:255',
            'company_name_ar' => 'nullable|string|max:255',
            'commercial_registration_number' => 'nullable|string|max:50',
            'national_id_number' => 'nullable|string|max:20',
            'company_address' => 'nullable|string|max:500',
            'company_address_ar' => 'nullable|string|max:500',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'status' => 'sometimes|in:pending,approved,rejected',
            'notes' => 'nullable|string|max:1000',
            'date_of_birth' => 'nullable|date',
            'nationality' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'business_activity_type' => 'nullable|string|max:255',
            'establishment_year' => 'nullable|integer|min:1900|max:2030',
            'vat_number' => 'nullable|string|max:50',
            'national_address' => 'nullable|string|max:500',
            'employee_count' => 'nullable|integer|min:0',
            'bank_name' => 'nullable|string|max:255',
            'iban' => 'nullable|string|max:50',
            'account_holder_name' => 'nullable|string|max:255',
        ]);

        // If status changed to approved, mark reviewer
        if (isset($validated['status']) && $validated['status'] === 'approved' && $merchant->status->value !== 'approved') {
            $validated['reviewed_by'] = $request->auth_user_id;
            $validated['reviewed_at'] = now();
        }

        $merchant->update($validated);

        return ApiResponse::success(
            new BusinessProfileResource($merchant),
            'تم تحديث بيانات التاجر بنجاح'
        );
    }

    /**
     * حذف تاجر
     */
    public function destroy(BusinessProfile $merchant): JsonResponse
    {
        if ($merchant->business_type !== BusinessType::MERCHANT) {
            return ApiResponse::notFound('التاجر غير موجود');
        }

        $merchant->delete();

        return ApiResponse::success(null, 'تم حذف التاجر بنجاح');
    }
}
