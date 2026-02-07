<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ForgotPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'exists:users,email'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => __('messages.validation.email_required'),
            'email.email' => __('messages.validation.email_invalid'),
            'email.exists' => __('messages.validation.email_not_found'),
        ];
    }
}
