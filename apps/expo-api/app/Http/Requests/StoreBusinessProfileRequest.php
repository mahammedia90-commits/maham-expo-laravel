<?php

namespace App\Http\Requests;

use App\Enums\BusinessType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBusinessProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'company_name_ar' => ['nullable', 'string', 'max:255'],
            'commercial_registration_number' => ['nullable', 'string', 'max:50'],
            'commercial_registration_image' => ['nullable', 'image', 'max:5120'],
            'national_id_number' => ['nullable', 'string', 'max:20'],
            'national_id_image' => ['nullable', 'image', 'max:5120'],
            'company_logo' => ['nullable', 'image', 'max:2048'],
            'company_address' => ['nullable', 'string', 'max:500'],
            'company_address_ar' => ['nullable', 'string', 'max:500'],
            'contact_phone' => ['required', 'string', 'max:20'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'business_type' => ['required', Rule::enum(BusinessType::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => __('messages.validation.company_name_required'),
            'contact_phone.required' => __('messages.validation.contact_phone_required'),
            'business_type.required' => __('messages.validation.business_type_required'),
            'commercial_registration_image.image' => __('messages.validation.must_be_image'),
            'commercial_registration_image.max' => __('messages.validation.file_too_large'),
            'national_id_image.image' => __('messages.validation.must_be_image'),
            'national_id_image.max' => __('messages.validation.file_too_large'),
        ];
    }
}
