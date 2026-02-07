<?php

return [

    // ================== AUTH ==================
    'auth' => [
        'unauthenticated' => 'غير مصرح لك بالوصول',
        'token_expired' => 'انتهت صلاحية التوكن',
        'token_invalid' => 'التوكن غير صالح',
        'token_blacklisted' => 'التوكن محظور',
        'token_error' => 'خطأ في التوكن',
        'account_suspended' => 'الحساب موقوف',
        'account_blocked' => 'الحساب محظور',
        'account_inactive' => 'الحساب غير مفعل',

        // Password Reset
        'user_not_found' => 'المستخدم غير موجود',
        'reset_link_sent' => 'تم إرسال رابط إعادة تعيين كلمة المرور',
        'invalid_reset_token' => 'رمز إعادة التعيين غير صالح',
        'reset_token_expired' => 'انتهت صلاحية رمز إعادة التعيين',
        'password_reset_success' => 'تم إعادة تعيين كلمة المرور بنجاح',

        // Change Password
        'current_password_incorrect' => 'كلمة المرور الحالية غير صحيحة',
        'password_same_as_old' => 'كلمة المرور الجديدة يجب أن تكون مختلفة عن الحالية',
        'password_changed' => 'تم تغيير كلمة المرور بنجاح',

        // Profile
        'profile_updated' => 'تم تحديث الملف الشخصي بنجاح',

        // Email Verification
        'email_already_verified' => 'البريد الإلكتروني مفعل مسبقاً',
        'verification_code_sent' => 'تم إرسال كود التحقق',
        'invalid_verification_code' => 'كود التحقق غير صالح',
        'email_verified' => 'تم التحقق من البريد الإلكتروني بنجاح',
    ],

    // ================== PERMISSIONS ==================
    'permissions' => [
        'denied' => 'ليس لديك صلاحية للقيام بهذا الإجراء',
    ],

    // ================== SERVICES ==================
    'services' => [
        'token_required' => 'توكن الخدمة مطلوب',
        'token_invalid' => 'توكن الخدمة غير صالح',
        'ip_not_allowed' => 'عنوان IP غير مسموح به',
        'suspended' => 'الخدمة موقوفة',
        'disabled' => 'الخدمة معطلة',
    ],

    // ================== RESOURCES ==================
    'user' => [
        'not_found' => 'المستخدم غير موجود',
    ],
    'role' => [
        'not_found' => 'الدور غير موجود',
    ],
    'permission' => [
        'not_found' => 'الصلاحية غير موجودة',
    ],
    'service' => [
        'not_found' => 'الخدمة غير موجودة',
    ],

    // ================== VALIDATION ==================
    'validation' => [
        'email_required' => 'البريد الإلكتروني مطلوب',
        'email_invalid' => 'البريد الإلكتروني غير صالح',
        'email_not_found' => 'البريد الإلكتروني غير مسجل',
        'email_unique' => 'البريد الإلكتروني مستخدم مسبقاً',
        'token_required' => 'الرمز مطلوب',
        'password_required' => 'كلمة المرور مطلوبة',
        'password_confirmation' => 'تأكيد كلمة المرور غير متطابق',
        'password_min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
        'password_different' => 'كلمة المرور الجديدة يجب أن تكون مختلفة عن الحالية',
        'current_password_required' => 'كلمة المرور الحالية مطلوبة',
        'name_max' => 'الاسم يجب ألا يتجاوز 255 حرف',
        'phone_max' => 'رقم الهاتف يجب ألا يتجاوز 20 رقم',
        'phone_required' => 'رقم الهاتف مطلوب',
        'roles_array' => 'الأدوار يجب أن تكون مصفوفة',
        'roles_exists' => 'واحد أو أكثر من الأدوار المحددة غير موجود',
    ],

    // ================== GENERAL ==================
    'resource_not_found' => 'المورد غير موجود',
    'route_not_found' => 'المسار غير موجود',
    'method_not_allowed' => 'طريقة الطلب غير مسموحة',
    'rate_limit_exceeded' => 'تم تجاوز الحد المسموح من الطلبات',
    'validation_failed' => 'فشل التحقق من البيانات',
    'server_error' => 'خطأ في الخادم',
    'unauthorized' => 'غير مصرح',
    'forbidden' => 'غير مسموح',
    'bad_request' => 'طلب غير صالح',
    'not_found' => 'غير موجود',
    'conflict' => 'تعارض في البيانات',
    'unprocessable_entity' => 'لا يمكن معالجة الطلب',
    'bad_gateway' => 'خطأ في البوابة',
    'service_unavailable' => 'الخدمة غير متوفرة',
    'gateway_timeout' => 'انتهت مهلة البوابة',
    'unknown_error' => 'خطأ غير معروف',
];
