<?php

namespace App\Http\Requests\Admin;

use App\Enums\EventStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'name_ar' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'description_ar' => ['nullable', 'string', 'max:5000'],
            'category_id' => ['sometimes', 'uuid', 'exists:categories,id'],
            'city_id' => ['sometimes', 'uuid', 'exists:cities,id'],
            'address' => ['sometimes', 'string', 'max:500'],
            'address_ar' => ['nullable', 'string', 'max:500'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
            'opening_time' => ['nullable', 'date_format:H:i'],
            'closing_time' => ['nullable', 'date_format:H:i'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:5120'],
            'features' => ['nullable', 'array'],
            'features_ar' => ['nullable', 'array'],
            'organizer_name' => ['nullable', 'string', 'max:255'],
            'organizer_phone' => ['nullable', 'string', 'max:20'],
            'organizer_email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'url', 'max:255'],
            'status' => ['sometimes', Rule::enum(EventStatus::class)],
            'is_featured' => ['sometimes', 'boolean'],
        ];
    }
}
