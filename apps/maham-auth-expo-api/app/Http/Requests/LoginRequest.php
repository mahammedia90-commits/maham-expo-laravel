<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'identifier.required' => 'البريد الإلكتروني أو رقم الجوال مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
        ];
    }

    /**
     * تحديد نوع المعرف (إيميل أو جوال)
     */
    public function getIdentifierType(): string
    {
        $identifier = $this->input('identifier');

        // إذا كان يحتوي على @ فهو إيميل
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return 'email';
        }

        // غير ذلك يعتبر رقم جوال
        return 'phone';
    }
}
