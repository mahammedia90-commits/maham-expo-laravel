<?php

namespace App\Http\Requests;

use App\Enums\BusinessType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBusinessProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['sometimes', 'string', 'max:255'],
            'company_name_ar' => ['nullable', 'string', 'max:255'],
            'commercial_registration_number' => ['nullable', 'string', 'max:50'],
            'commercial_registration_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,pdf', 'max:5120'],
            'national_id_number' => ['nullable', 'string', 'max:20'],
            'national_id_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,pdf', 'max:5120'],
            'company_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'company_address' => ['nullable', 'string', 'max:500'],
            'company_address_ar' => ['nullable', 'string', 'max:500'],
            'contact_phone' => ['sometimes', 'string', 'max:20'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'business_type' => ['sometimes', Rule::enum(BusinessType::class)],
        ];
    }
}
