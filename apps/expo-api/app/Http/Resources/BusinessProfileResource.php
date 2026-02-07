<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'company_name' => $this->localized_company_name,
            'company_name_en' => $this->company_name,
            'company_name_ar' => $this->company_name_ar,
            'commercial_registration_number' => $this->commercial_registration_number,
            'commercial_registration_image' => $this->commercial_registration_image,
            'national_id_image' => $this->national_id_image,
            'company_logo' => $this->company_logo,
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
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
