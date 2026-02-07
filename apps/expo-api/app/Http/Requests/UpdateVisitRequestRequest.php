<?php

namespace App\Http\Requests;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVisitRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'visit_date' => ['sometimes', 'date', 'after_or_equal:today'],
            'visit_time' => ['nullable', 'date_format:H:i'],
            'visitors_count' => ['sometimes', 'integer', 'min:1', 'max:' . config('expo-api.visit_request.max_visitors', 10)],
            'notes' => ['nullable', 'string', 'max:1000'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $visitRequest = $this->route('visitRequest');

            if ($visitRequest && $this->visit_date) {
                $event = $visitRequest->event;

                if ($this->visit_date < $event->start_date->format('Y-m-d') ||
                    $this->visit_date > $event->end_date->format('Y-m-d')) {
                    $validator->errors()->add('visit_date', __('messages.visit_request.date_outside_event'));
                }
            }
        });
    }
}
