<?php

namespace App\Http\Requests;

use App\Models\Space;
use Illuminate\Foundation\Http\FormRequest;

class StoreRentalRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'space_id' => ['required', 'uuid', 'exists:spaces,id'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->space_id && $this->start_date && $this->end_date) {
                $space = Space::with('event')->find($this->space_id);

                if ($space) {
                    $event = $space->event;

                    // Check if dates are within event dates
                    if ($this->start_date < $event->start_date->format('Y-m-d') ||
                        $this->end_date > $event->end_date->format('Y-m-d')) {
                        $validator->errors()->add('start_date', __('messages.rental_request.dates_outside_event'));
                    }

                    // Check if space is available for these dates
                    if (!$space->isAvailableForDates($this->start_date, $this->end_date)) {
                        $validator->errors()->add('space_id', __('messages.rental_request.space_not_available'));
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'space_id.required' => __('messages.validation.space_required'),
            'space_id.exists' => __('messages.space.not_found'),
            'start_date.required' => __('messages.validation.start_date_required'),
            'start_date.after_or_equal' => __('messages.validation.start_date_future'),
            'end_date.required' => __('messages.validation.end_date_required'),
            'end_date.after_or_equal' => __('messages.validation.end_date_after_start'),
        ];
    }
}
