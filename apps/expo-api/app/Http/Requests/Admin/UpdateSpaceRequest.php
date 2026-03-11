<?php

namespace App\Http\Requests\Admin;

use App\Enums\PaymentSystem;
use App\Enums\RentalDuration;
use App\Enums\SpaceStatus;
use App\Enums\SpaceType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSpaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $space = $this->route('space');

        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'description_ar' => ['nullable', 'string', 'max:2000'],
            'location_code' => [
                'sometimes',
                'string',
                'max:20',
                Rule::unique('spaces')->where(function ($query) use ($space) {
                    return $query->where('event_id', $space->event_id);
                })->ignore($space->id),
            ],
            'area_sqm' => ['sometimes', 'numeric', 'min:1', 'max:999999'],
            'price_per_day' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'price_total' => ['sometimes', 'numeric', 'min:0', 'max:9999999999'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'images_360' => ['nullable', 'array', 'max:5'],
            'images_360.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'amenities' => ['nullable', 'array', 'max:50'],
            'amenities_ar' => ['nullable', 'array', 'max:50'],
            'status' => ['sometimes', Rule::enum(SpaceStatus::class)],
            'floor_number' => ['nullable', 'integer', 'min:-10', 'max:200'],
            // Verify section belongs to the same event (IDOR prevention)
            'section_id' => ['nullable', 'uuid', Rule::exists('sections', 'id')->where('event_id', $space->event_id)],
            'space_type' => ['nullable', Rule::enum(SpaceType::class)],
            'payment_system' => ['nullable', Rule::enum(PaymentSystem::class)],
            'rental_duration' => ['nullable', Rule::enum(RentalDuration::class)],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'address' => ['nullable', 'string', 'max:500'],
            'address_ar' => ['nullable', 'string', 'max:500'],
            'services' => ['nullable', 'array', 'max:20'],
            'services.*' => ['uuid', 'exists:services,id'],
            'is_featured' => ['sometimes', 'boolean'],
        ];
    }
}
