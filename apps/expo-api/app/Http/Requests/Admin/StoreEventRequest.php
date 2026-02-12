<?php

namespace App\Http\Requests\Admin;

use App\Enums\EventStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'description_ar' => ['nullable', 'string', 'max:5000'],
            'category_id' => ['required', 'uuid', 'exists:categories,id'],
            'city_id' => ['required', 'uuid', 'exists:cities,id'],
            'address' => ['required', 'string', 'max:500'],
            'address_ar' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'opening_time' => ['nullable', 'date_format:H:i'],
            'closing_time' => ['nullable', 'date_format:H:i'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'images_360' => ['nullable', 'array', 'max:5'],
            'images_360.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'features' => ['nullable', 'array', 'max:50'],
            'features_ar' => ['nullable', 'array', 'max:50'],
            'organizer_name' => ['nullable', 'string', 'max:255'],
            'organizer_phone' => ['nullable', 'string', 'max:20'],
            'organizer_email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'status' => ['sometimes', Rule::enum(EventStatus::class)],
            'is_featured' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('messages.validation.name_required'),
            'name_ar.required' => __('messages.validation.name_ar_required'),
            'category_id.required' => __('messages.validation.category_required'),
            'city_id.required' => __('messages.validation.city_required'),
            'address.required' => __('messages.validation.address_required'),
            'start_date.required' => __('messages.validation.start_date_required'),
            'end_date.required' => __('messages.validation.end_date_required'),
            'end_date.after_or_equal' => __('messages.validation.end_date_after_start'),
        ];
    }
}
