<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRentalRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'start_date' => ['sometimes', 'date', 'after_or_equal:today'],
            'end_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $rentalRequest = $this->route('rentalRequest');

            if ($rentalRequest) {
                $space = $rentalRequest->space;
                $event = $space->event;

                $startDate = $this->start_date ?? $rentalRequest->start_date->format('Y-m-d');
                $endDate = $this->end_date ?? $rentalRequest->end_date->format('Y-m-d');

                // Check if dates are within event dates
                if ($startDate < $event->start_date->format('Y-m-d') ||
                    $endDate > $event->end_date->format('Y-m-d')) {
                    $validator->errors()->add('start_date', __('messages.rental_request.dates_outside_event'));
                }
            }
        });
    }
}
