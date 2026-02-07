<?php

namespace App\Http\Requests;

use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;

class StoreVisitRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_id' => ['required', 'uuid', 'exists:events,id'],
            'visit_date' => ['required', 'date', 'after_or_equal:today'],
            'visit_time' => ['nullable', 'date_format:H:i'],
            'visitors_count' => ['required', 'integer', 'min:1', 'max:' . config('expo-api.visit_request.max_visitors', 10)],
            'notes' => ['nullable', 'string', 'max:1000'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->event_id) {
                $event = Event::find($this->event_id);

                if ($event) {
                    // Check if visit date is within event dates
                    if ($this->visit_date < $event->start_date->format('Y-m-d') ||
                        $this->visit_date > $event->end_date->format('Y-m-d')) {
                        $validator->errors()->add('visit_date', __('messages.visit_request.date_outside_event'));
                    }

                    // Check if event can be visited
                    if (!$event->canBeVisited()) {
                        $validator->errors()->add('event_id', __('messages.visit_request.event_not_available'));
                    }
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'event_id.required' => __('messages.validation.event_required'),
            'event_id.exists' => __('messages.event.not_found'),
            'visit_date.required' => __('messages.validation.visit_date_required'),
            'visit_date.after_or_equal' => __('messages.validation.visit_date_future'),
            'visitors_count.required' => __('messages.validation.visitors_count_required'),
            'visitors_count.min' => __('messages.validation.visitors_count_min'),
            'visitors_count.max' => __('messages.validation.visitors_count_max'),
        ];
    }
}
