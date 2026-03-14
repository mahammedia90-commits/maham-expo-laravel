<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BusinessProfileResource extends JsonResource
{
    protected function toStorageUrl(?string $path): ?string
    {
        if (!$path) return null;
        if (str_starts_with($path, 'http')) return $path;
        return Storage::disk('public')->url($path);
    }

    public function toArray(Request $request): array
    {
        // Sensitive documents only visible to profile owner or admin
        $isOwnerOrAdmin = $this->isOwnerOrAdmin($request);

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'full_name' => $this->full_name,
            'company_name' => $this->localized_company_name,
            'company_name_en' => $this->company_name,
            'company_name_ar' => $this->company_name_ar,
            'commercial_registration_number' => $this->when($isOwnerOrAdmin, $this->commercial_registration_number),
            'commercial_registration_image' => $this->when($isOwnerOrAdmin, fn() => $this->toStorageUrl($this->commercial_registration_image)),
            'national_id_number' => $this->when($isOwnerOrAdmin, $this->national_id_number),
            'national_id_image' => $this->when($isOwnerOrAdmin, fn() => $this->toStorageUrl($this->national_id_image)),
            'company_logo' => $this->toStorageUrl($this->company_logo),
            'avatar' => $this->toStorageUrl($this->avatar),
            'company_address' => $this->localized_company_address,
            'contact_phone' => $this->contact_phone,
            'contact_email' => $this->contact_email,
            'website' => $this->website,
            'business_type' => $this->business_type?->value,
            'business_type_label' => $this->business_type
                ? (app()->getLocale() === 'ar' ? $this->business_type->label() : $this->business_type->labelEn())
                : null,
            'status' => $this->status?->value,
            'status_label' => $this->status
                ? (app()->getLocale() === 'ar' ? $this->status->label() : $this->status->labelEn())
                : null,
            'is_approved' => $this->is_approved,
            'is_pending' => $this->is_pending,
            'is_rejected' => $this->is_rejected,
            'rejection_reason' => $this->when($this->is_rejected, $this->rejection_reason),
            'reviewed_at' => $this->reviewed_at?->toISOString(),

            // KYC Fields (only for owner/admin)
            'date_of_birth' => $this->when($isOwnerOrAdmin, $this->date_of_birth?->format('Y-m-d')),
            'nationality' => $this->when($isOwnerOrAdmin, $this->nationality),
            'city' => $this->when($isOwnerOrAdmin, $this->city),
            'business_activity_type' => $this->when($isOwnerOrAdmin, $this->business_activity_type),
            'establishment_year' => $this->when($isOwnerOrAdmin, $this->establishment_year),
            'vat_number' => $this->when($isOwnerOrAdmin, $this->vat_number),
            'national_address' => $this->when($isOwnerOrAdmin, $this->national_address),
            'employee_count' => $this->when($isOwnerOrAdmin, $this->employee_count),
            'bank_name' => $this->when($isOwnerOrAdmin, $this->bank_name),
            'iban' => $this->when($isOwnerOrAdmin, $this->iban),
            'account_holder_name' => $this->when($isOwnerOrAdmin, $this->account_holder_name),
            'account_number' => $this->when($isOwnerOrAdmin, $this->account_number),
            'vat_certificate_image' => $this->when($isOwnerOrAdmin, fn() => $this->toStorageUrl($this->vat_certificate_image)),
            'authorization_letter_image' => $this->when($isOwnerOrAdmin, fn() => $this->toStorageUrl($this->authorization_letter_image)),
            'national_address_doc' => $this->when($isOwnerOrAdmin, fn() => $this->toStorageUrl($this->national_address_doc)),
            'bank_letter_image' => $this->when($isOwnerOrAdmin, fn() => $this->toStorageUrl($this->bank_letter_image)),
            'company_profile_doc' => $this->when($isOwnerOrAdmin, fn() => $this->toStorageUrl($this->company_profile_doc)),
            'product_catalog_doc' => $this->when($isOwnerOrAdmin, fn() => $this->toStorageUrl($this->product_catalog_doc)),
            'legal_declaration_accepted' => $this->when($isOwnerOrAdmin, $this->legal_declaration_accepted),
            'kyc_step' => $this->when($isOwnerOrAdmin, $this->kyc_step),
            'kyc_submitted_at' => $this->when($isOwnerOrAdmin, $this->kyc_submitted_at?->toISOString()),

            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }

    /**
     * Check if the current request user is the profile owner or an admin
     */
    protected function isOwnerOrAdmin(Request $request): bool
    {
        $authUserId = $request->input('auth_user_id');
        if (!$authUserId) {
            return false;
        }

        // Owner check
        if ($this->user_id === $authUserId) {
            return true;
        }

        // Admin check
        $roles = $request->input('auth_user_roles', []);
        if (is_array($roles) && (in_array('admin', $roles) || in_array('super-admin', $roles))) {
            return true;
        }

        return false;
    }
}
