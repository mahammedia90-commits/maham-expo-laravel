<?php

return [

    // ================== AUTH ==================
    'auth' => [
        'token_required' => 'التوكن مطلوب',
        'token_invalid' => 'التوكن غير صالح',
        'token_expired' => 'انتهت صلاحية التوكن',
        'account_inactive' => 'الحساب غير مفعل',
        'role_required' => 'ليس لديك الصلاحية للوصول',
        'permission_denied' => 'ليس لديك صلاحية للقيام بهذا الإجراء',
        'unauthorized' => 'غير مصرح لك بالوصول',
    ],

    // ================== PROFILE ==================
    'profile' => [
        'not_found' => 'الملف التجاري غير موجود',
        'already_exists' => 'لديك ملف تجاري مسبقاً',
        'created' => 'تم إنشاء الملف التجاري بنجاح',
        'updated' => 'تم تحديث الملف التجاري بنجاح',
        'cannot_be_modified' => 'لا يمكن تعديل الملف التجاري في هذه الحالة',
        'required' => 'يجب إنشاء ملف تجاري أولاً',
        'required_for_rental' => 'يجب توثيق حسابك لتقديم طلبات الإيجار',
        'pending' => 'ملفك التجاري قيد المراجعة',
        'approved' => 'تم توثيق الملف التجاري',
        'rejected' => 'تم رفض توثيق الملف التجاري',
        'not_pending' => 'الملف ليس قيد المراجعة',
    ],

    // ================== EVENT ==================
    'event' => [
        'not_found' => 'الفعالية غير موجودة',
        'created' => 'تم إنشاء الفعالية بنجاح',
        'updated' => 'تم تحديث الفعالية بنجاح',
        'deleted' => 'تم حذف الفعالية بنجاح',
        'has_active_requests' => 'لا يمكن حذف الفعالية لوجود طلبات نشطة',
    ],

    // ================== SPACE ==================
    'space' => [
        'not_found' => 'المساحة غير موجودة',
        'created' => 'تم إنشاء المساحة بنجاح',
        'updated' => 'تم تحديث المساحة بنجاح',
        'deleted' => 'تم حذف المساحة بنجاح',
        'has_active_rentals' => 'لا يمكن حذف المساحة لوجود عقود إيجار نشطة',
    ],

    // ================== VISIT REQUEST ==================
    'visit_request' => [
        'not_found' => 'طلب الزيارة غير موجود',
        'already_exists' => 'لديك طلب زيارة مسبق لهذا التاريخ',
        'created' => 'تم إنشاء طلب الزيارة بنجاح',
        'updated' => 'تم تحديث طلب الزيارة بنجاح',
        'cancelled' => 'تم إلغاء طلب الزيارة',
        'cannot_be_modified' => 'لا يمكن تعديل الطلب في هذه الحالة',
        'cannot_be_cancelled' => 'لا يمكن إلغاء الطلب في هذه الحالة',
        'date_outside_event' => 'تاريخ الزيارة خارج فترة الفعالية',
        'event_not_available' => 'الفعالية غير متاحة للزيارة',
        'approved' => 'تمت الموافقة على طلب الزيارة',
        'rejected' => 'تم رفض طلب الزيارة',
        'not_pending' => 'الطلب ليس قيد المراجعة',
    ],

    // ================== RENTAL REQUEST ==================
    'rental_request' => [
        'not_found' => 'طلب الإيجار غير موجود',
        'already_exists' => 'لديك طلب إيجار مسبق لهذه المساحة في هذه الفترة',
        'created' => 'تم إنشاء طلب الإيجار بنجاح',
        'updated' => 'تم تحديث طلب الإيجار بنجاح',
        'cancelled' => 'تم إلغاء طلب الإيجار',
        'cannot_be_modified' => 'لا يمكن تعديل الطلب في هذه الحالة',
        'cannot_be_cancelled' => 'لا يمكن إلغاء الطلب في هذه الحالة',
        'dates_outside_event' => 'تواريخ الإيجار خارج فترة الفعالية',
        'space_not_available' => 'المساحة غير متاحة في هذه الفترة',
        'approved' => 'تمت الموافقة على طلب الإيجار',
        'rejected' => 'تم رفض طلب الإيجار',
        'not_pending' => 'الطلب ليس قيد المراجعة',
        'must_be_approved' => 'يجب أن يكون الطلب موافق عليه',
        'payment_recorded' => 'تم تسجيل الدفعة بنجاح',
    ],

    // ================== FAVORITE ==================
    'favorite' => [
        'added' => 'تمت الإضافة إلى المفضلة',
        'removed' => 'تمت الإزالة من المفضلة',
    ],

    // ================== NOTIFICATION ==================
    'notification' => [
        'marked_as_read' => 'تم تحديد الإشعار كمقروء',
        'all_marked_as_read' => 'تم تحديد جميع الإشعارات كمقروءة',
    ],

    // ================== VALIDATION ==================
    'validation' => [
        'company_name_required' => 'اسم الشركة مطلوب',
        'contact_phone_required' => 'رقم التواصل مطلوب',
        'business_type_required' => 'نوع النشاط التجاري مطلوب',
        'must_be_image' => 'يجب أن يكون الملف صورة',
        'file_too_large' => 'حجم الملف كبير جداً',
        'event_required' => 'الفعالية مطلوبة',
        'visit_date_required' => 'تاريخ الزيارة مطلوب',
        'visit_date_future' => 'تاريخ الزيارة يجب أن يكون في المستقبل',
        'visitors_count_required' => 'عدد الزوار مطلوب',
        'visitors_count_min' => 'عدد الزوار يجب أن يكون 1 على الأقل',
        'visitors_count_max' => 'عدد الزوار يجب ألا يتجاوز الحد المسموح',
        'space_required' => 'المساحة مطلوبة',
        'start_date_required' => 'تاريخ البداية مطلوب',
        'start_date_future' => 'تاريخ البداية يجب أن يكون في المستقبل',
        'end_date_required' => 'تاريخ النهاية مطلوب',
        'end_date_after_start' => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية',
        'type_required' => 'النوع مطلوب',
        'invalid_type' => 'النوع غير صالح',
        'id_required' => 'المعرف مطلوب',
        'name_required' => 'الاسم مطلوب',
        'name_ar_required' => 'الاسم بالعربي مطلوب',
        'category_required' => 'التصنيف مطلوب',
        'city_required' => 'المدينة مطلوبة',
        'address_required' => 'العنوان مطلوب',
        'location_code_required' => 'رمز الموقع مطلوب',
        'location_code_unique' => 'رمز الموقع مستخدم مسبقاً',
        'area_required' => 'المساحة مطلوبة',
        'price_required' => 'السعر مطلوب',
        'rejection_reason_required' => 'سبب الرفض مطلوب',
    ],

    // ================== GENERAL ==================
    'resource_not_found' => 'المورد غير موجود',
    'forbidden' => 'غير مسموح بهذا الإجراء',
    'unauthorized' => 'غير مصرح',
    'validation_failed' => 'فشل التحقق من البيانات',
    'server_error' => 'خطأ في الخادم',
];
