<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers(),
            ],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'roles' => 'array',
            'roles.*' => ['exists:roles,name', 'not_in:super-admin,admin,supervisor'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صالح',
            'email.unique' => 'البريد الإلكتروني مسجل مسبقاً',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'phone.required' => 'رقم الهاتف مطلوب',
            'phone.max' => 'رقم الهاتف طويل جداً',
            'roles.array' => 'الأدوار يجب أن تكون مصفوفة',
            'roles.*.exists' => 'الدور المحدد غير موجود',
            'roles.*.not_in' => 'لا يمكن تعيين هذا الدور عند التسجيل',
            
        ];
    }
}
