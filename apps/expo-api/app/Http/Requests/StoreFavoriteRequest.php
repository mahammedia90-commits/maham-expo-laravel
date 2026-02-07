<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFavoriteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['event', 'space'])],
            'id' => ['required', 'uuid'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $table = $this->type === 'event' ? 'events' : 'spaces';

            if (!\DB::table($table)->where('id', $this->id)->exists()) {
                $validator->errors()->add('id', __('messages.resource_not_found'));
            }
        });
    }

    public function messages(): array
    {
        return [
            'type.required' => __('messages.validation.type_required'),
            'type.in' => __('messages.validation.invalid_type'),
            'id.required' => __('messages.validation.id_required'),
        ];
    }
}
