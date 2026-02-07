<?php

namespace App\Http\Requests\Admin;

use App\Enums\SpaceStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSpaceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $eventId = $this->route('event')?->id ?? $this->event_id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'description_ar' => ['nullable', 'string', 'max:2000'],
            'location_code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('spaces')->where(function ($query) use ($eventId) {
                    return $query->where('event_id', $eventId);
                }),
            ],
            'area_sqm' => ['required', 'numeric', 'min:1'],
            'price_per_day' => ['nullable', 'numeric', 'min:0'],
            'price_total' => ['required', 'numeric', 'min:0'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:5120'],
            'amenities' => ['nullable', 'array'],
            'amenities_ar' => ['nullable', 'array'],
            'status' => ['sometimes', Rule::enum(SpaceStatus::class)],
            'floor_number' => ['nullable', 'integer'],
            'section' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('messages.validation.name_required'),
            'location_code.required' => __('messages.validation.location_code_required'),
            'location_code.unique' => __('messages.validation.location_code_unique'),
            'area_sqm.required' => __('messages.validation.area_required'),
            'price_total.required' => __('messages.validation.price_required'),
        ];
    }
}
