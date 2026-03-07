<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string'],
            'password' => [
                'required',
                'confirmed',
                'different:current_password',
                Password::min(8)
                    ->mixedCase()
                    ->numbers(),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => __('messages.validation.current_password_required'),
            'password.required' => __('messages.validation.password_required'),
            'password.confirmed' => __('messages.validation.password_confirmation'),
            'password.different' => __('messages.validation.password_different'),
            'password.min' => __('messages.validation.password_min'),
        ];
    }
}
