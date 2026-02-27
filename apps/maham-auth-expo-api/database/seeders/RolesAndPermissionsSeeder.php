<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء الصلاحيات الافتراضية
        $permissions = $this->createDefaultPermissions();

        // إنشاء صلاحيات المعارض (Expo)
        $expoPermissions = $this->createExpoPermissions();

        // إنشاء الأدوار الافتراضية
        $this->createDefaultRoles(array_merge($permissions, $expoPermissions));

        // إنشاء مستخدم Super Admin
        $this->createSuperAdmin();
    }

    /**
     * صلاحيات نظام المصادقة (Auth System)
     */
    protected function createDefaultPermissions(): array
    {
        $permissionGroups = [
            'users' => ['view', 'create', 'update', 'delete'],
            'roles' => ['view', 'create', 'update', 'delete'],
            'permissions' => ['view', 'create', 'update', 'delete'],
            'services' => ['view', 'create', 'update', 'delete'],
            'audit' => ['view'],
        ];

        $permissions = [];

        foreach ($permissionGroups as $group => $actions) {
            foreach ($actions as $action) {
                $name = "{$group}.{$action}";
                $permissions[$name] = Permission::firstOrCreate(
                    ['name' => $name],
                    [
                        'display_name' => ucfirst($action) . ' ' . ucfirst($group),
                        'description' => ucfirst($action) . ' ' . $group . ' management',
                        'group' => $group,
                        'is_system' => true,
                    ]
                );
            }
        }

        $this->command->info('✅ صلاحيات نظام المصادقة تم إنشاؤها');
        return $permissions;
    }

    /**
     * صلاحيات تطبيق المعارض (Expo API)
     * تشمل جميع الصلاحيات المطلوبة للتاجر والمستثمر والمدير
     */
    protected function createExpoPermissions(): array
    {
        $permissionGroups = [
            // ==================== إدارة الفعاليات ====================
            'events' => [
                'view' => 'عرض الفعاليات',
                'create' => 'إنشاء فعالية',
                'update' => 'تعديل فعالية',
                'delete' => 'حذف فعالية',
            ],

            // ==================== إدارة الأقسام ====================
            'sections' => [
                'view' => 'عرض الأقسام',
                'create' => 'إنشاء قسم',
                'update' => 'تعديل قسم',
                'delete' => 'حذف قسم',
            ],

            // ==================== إدارة المساحات ====================
            'spaces' => [
                'view' => 'عرض المساحات',
                'create' => 'إنشاء مساحة',
                'update' => 'تعديل مساحة',
                'delete' => 'حذف مساحة',
            ],

            // ==================== إدارة الخدمات ====================
            'expo-services' => [
                'view' => 'عرض خدمات المعرض',
                'create' => 'إنشاء خدمة معرض',
                'update' => 'تعديل خدمة معرض',
                'delete' => 'حذف خدمة معرض',
            ],

            // ==================== إدارة التصنيفات ====================
            'categories' => [
                'view' => 'عرض التصنيفات',
                'create' => 'إنشاء تصنيف',
                'update' => 'تعديل تصنيف',
                'delete' => 'حذف تصنيف',
            ],

            // ==================== إدارة المدن ====================
            'cities' => [
                'view' => 'عرض المدن',
                'create' => 'إنشاء مدينة',
                'update' => 'تعديل مدينة',
                'delete' => 'حذف مدينة',
            ],

            // ==================== الملف التجاري ====================
            'profiles' => [
                'view' => 'عرض الملف التجاري',
                'create' => 'إنشاء ملف تجاري',
                'update' => 'تعديل الملف التجاري',
                'delete' => 'حذف الملف التجاري',
                'approve' => 'اعتماد ملف تجاري',
                'reject' => 'رفض ملف تجاري',
                'view-all' => 'عرض جميع الملفات التجارية',
            ],

            // ==================== المفضلة ====================
            'favorites' => [
                'view' => 'عرض المفضلة',
                'create' => 'إضافة للمفضلة',
                'delete' => 'حذف من المفضلة',
            ],

            // ==================== الإشعارات ====================
            'notifications' => [
                'view' => 'عرض الإشعارات',
                'update' => 'تحديث حالة الإشعارات',
            ],

            // ==================== طلبات الزيارة ====================
            'visit-requests' => [
                'view' => 'عرض طلبات الزيارة',
                'create' => 'إنشاء طلب زيارة',
                'update' => 'تعديل طلب زيارة',
                'delete' => 'إلغاء طلب زيارة',
                'approve' => 'اعتماد طلب زيارة',
                'reject' => 'رفض طلب زيارة',
                'view-all' => 'عرض جميع طلبات الزيارة',
            ],

            // ==================== طلبات الاستئجار ====================
            'rental-requests' => [
                'view' => 'عرض طلبات الاستئجار',
                'create' => 'إنشاء طلب استئجار',
                'update' => 'تعديل طلب استئجار',
                'delete' => 'إلغاء طلب استئجار',
                'approve' => 'اعتماد طلب استئجار',
                'reject' => 'رفض طلب استئجار',
                'view-all' => 'عرض جميع طلبات الاستئجار',
                'record-payment' => 'تسجيل دفعة للاستئجار',
            ],

            // ==================== التقارير والإحصائيات ====================
            'reports' => [
                'view' => 'عرض التقارير',
                'export' => 'تصدير التقارير',
            ],

            // ==================== الدفع والمالية ====================
            'payments' => [
                'view' => 'عرض المدفوعات',
                'create' => 'إنشاء دفعة',
                'refund' => 'استرداد دفعة',
            ],

            // ==================== الرعاة ====================
            'sponsors' => [
                'view' => 'عرض الرعاة',
                'create' => 'إنشاء راعي',
                'update' => 'تعديل راعي',
                'delete' => 'حذف راعي',
                'approve' => 'اعتماد راعي',
                'reject' => 'رفض راعي',
                'view-all' => 'عرض جميع الرعاة',
            ],

            // ==================== باقات الرعاية ====================
            'sponsor-packages' => [
                'view' => 'عرض باقات الرعاية',
                'create' => 'إنشاء باقة رعاية',
                'update' => 'تعديل باقة رعاية',
                'delete' => 'حذف باقة رعاية',
            ],

            // ==================== عقود الرعاية ====================
            'sponsor-contracts' => [
                'view' => 'عرض عقود الرعاية',
                'create' => 'إنشاء عقد رعاية',
                'update' => 'تعديل عقد رعاية',
                'approve' => 'اعتماد عقد رعاية',
                'reject' => 'رفض عقد رعاية',
                'view-all' => 'عرض جميع عقود الرعاية',
            ],

            // ==================== مدفوعات الرعاية ====================
            'sponsor-payments' => [
                'view' => 'عرض مدفوعات الرعاية',
                'create' => 'إنشاء دفعة رعاية',
                'view-all' => 'عرض جميع مدفوعات الرعاية',
            ],

            // ==================== أصول الراعي (مواد إعلانية) ====================
            'sponsor-assets' => [
                'view' => 'عرض مواد الراعي',
                'create' => 'رفع مادة إعلانية',
                'update' => 'تعديل مادة إعلانية',
                'delete' => 'حذف مادة إعلانية',
                'approve' => 'اعتماد مادة إعلانية',
            ],

            // ==================== تتبع ظهور الراعي ====================
            'sponsor-exposure' => [
                'view' => 'عرض إحصائيات الظهور',
            ],

            // ==================== نظام التقييمات ====================
            'ratings' => [
                'view'       => 'عرض التقييمات',
                'create'     => 'إضافة تقييم',
                'update'     => 'تعديل التقييم',
                'delete'     => 'حذف تقييم',
                'approve'    => 'اعتماد تقييم',
                'reject'     => 'رفض تقييم',
                'view-all'   => 'عرض جميع التقييمات',
            ],

            // ==================== تذاكر الدعم ====================
            'support-tickets' => [
                'view'       => 'عرض تذاكر الدعم الخاصة',
                'create'     => 'إنشاء تذكرة دعم',
                'update'     => 'تعديل تذكرة دعم',
                'close'      => 'إغلاق تذكرة دعم',
                'reply'      => 'الرد على تذكرة دعم',
                'view-all'   => 'عرض جميع التذاكر',
                'assign'     => 'تعيين تذكرة لموظف',
                'delete'     => 'حذف تذكرة دعم',
            ],

            // ==================== عقود الاستئجار ====================
            'rental-contracts' => [
                'view'       => 'عرض عقود الاستئجار الخاصة',
                'create'     => 'إنشاء عقد استئجار',
                'update'     => 'تعديل عقد استئجار',
                'sign'       => 'توقيع عقد استئجار',
                'approve'    => 'اعتماد عقد استئجار',
                'reject'     => 'رفض عقد استئجار',
                'terminate'  => 'إنهاء عقد استئجار',
                'view-all'   => 'عرض جميع عقود الاستئجار',
            ],

            // ==================== الفواتير ====================
            'invoices' => [
                'view'       => 'عرض الفواتير الخاصة',
                'create'     => 'إنشاء فاتورة',
                'update'     => 'تعديل فاتورة',
                'issue'      => 'إصدار فاتورة',
                'mark-paid'  => 'تسجيل دفع فاتورة',
                'cancel'     => 'إلغاء فاتورة',
                'view-all'   => 'عرض جميع الفواتير',
            ],

            // ==================== إدارة المحتوى (CMS) ====================
            'pages' => [
                'view'   => 'عرض الصفحات',
                'create' => 'إنشاء صفحة',
                'update' => 'تعديل صفحة',
                'delete' => 'حذف صفحة',
            ],

            'faqs' => [
                'view'   => 'عرض الأسئلة الشائعة',
                'create' => 'إضافة سؤال شائع',
                'update' => 'تعديل سؤال شائع',
                'delete' => 'حذف سؤال شائع',
            ],

            'banners' => [
                'view'   => 'عرض الإعلانات',
                'create' => 'إنشاء إعلان',
                'update' => 'تعديل إعلان',
                'delete' => 'حذف إعلان',
            ],

            // ==================== تفضيلات الإشعارات ====================
            'notification-preferences' => [
                'view'   => 'عرض تفضيلات الإشعارات',
                'update' => 'تعديل تفضيلات الإشعارات',
            ],

            // ==================== إعدادات النظام ====================
            'settings' => [
                'view'   => 'عرض الإعدادات',
                'update' => 'تعديل الإعدادات',
            ],

            // ==================== مزايا الرعاية ====================
            'sponsor-benefits' => [
                'view'    => 'عرض مزايا الرعاية',
                'create'  => 'إنشاء ميزة رعاية',
                'update'  => 'تعديل ميزة رعاية',
                'deliver' => 'تسليم ميزة رعاية',
            ],
        ];

        $permissions = [];

        foreach ($permissionGroups as $group => $actions) {
            foreach ($actions as $action => $displayNameAr) {
                $name = "{$group}.{$action}";
                $permissions[$name] = Permission::firstOrCreate(
                    ['name' => $name],
                    [
                        'display_name' => $displayNameAr,
                        'description' => $displayNameAr,
                        'group' => $group,
                        'is_system' => true,
                    ]
                );
            }
        }

        $this->command->info('✅ صلاحيات تطبيق المعارض (Expo) تم إنشاؤها (' . count($permissions) . ' صلاحية)');
        return $permissions;
    }

    /**
     * إنشاء الأدوار مع ربط الصلاحيات
     */
    protected function createDefaultRoles(array $permissions): void
    {
        // ============================================================
        // Super Admin - مدير عام - كل الصلاحيات
        // ============================================================
        $superAdmin = Role::firstOrCreate(
            ['name' => 'super-admin'],
            [
                'display_name' => 'مدير عام',
                'description' => 'صلاحيات كاملة على النظام - يتحكم بكل شيء',
                'is_system' => true,
                'level' => 100,
            ]
        );
        $superAdmin->syncPermissions(array_keys($permissions));
        $this->command->info("   ✅ Super Admin: " . count($permissions) . " صلاحية");

        // ============================================================
        // Admin - مدير - صلاحيات إدارية شاملة
        // ============================================================
        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'مدير',
                'description' => 'صلاحيات إدارية - إدارة المحتوى والمستخدمين والطلبات',
                'is_system' => true,
                'level' => 80,
            ]
        );
        $adminPermissions = [
            // Auth System
            'users.view', 'users.create', 'users.update',
            'roles.view',
            'permissions.view',
            'services.view',
            'audit.view',

            // Expo - إدارة الفعاليات (CRUD كامل)
            'events.view', 'events.create', 'events.update', 'events.delete',
            'sections.view', 'sections.create', 'sections.update', 'sections.delete',
            'spaces.view', 'spaces.create', 'spaces.update', 'spaces.delete',
            'expo-services.view', 'expo-services.create', 'expo-services.update', 'expo-services.delete',
            'categories.view', 'categories.create', 'categories.update', 'categories.delete',
            'cities.view', 'cities.create', 'cities.update', 'cities.delete',

            // Expo - إدارة الملفات التجارية
            'profiles.view', 'profiles.view-all', 'profiles.approve', 'profiles.reject',

            // Expo - إدارة طلبات الزيارة
            'visit-requests.view', 'visit-requests.view-all',
            'visit-requests.approve', 'visit-requests.reject',

            // Expo - إدارة طلبات الاستئجار
            'rental-requests.view', 'rental-requests.view-all',
            'rental-requests.approve', 'rental-requests.reject', 'rental-requests.record-payment',

            // التقارير والمالية
            'reports.view', 'reports.export',
            'payments.view', 'payments.create', 'payments.refund',

            // الرعاة - إدارة كاملة
            'sponsors.view', 'sponsors.create', 'sponsors.update', 'sponsors.delete',
            'sponsors.approve', 'sponsors.reject', 'sponsors.view-all',
            'sponsor-packages.view', 'sponsor-packages.create', 'sponsor-packages.update', 'sponsor-packages.delete',
            'sponsor-contracts.view', 'sponsor-contracts.create', 'sponsor-contracts.update',
            'sponsor-contracts.approve', 'sponsor-contracts.reject', 'sponsor-contracts.view-all',
            'sponsor-payments.view', 'sponsor-payments.create', 'sponsor-payments.view-all',
            'sponsor-assets.view', 'sponsor-assets.approve',
            'sponsor-exposure.view',

            // التقييمات - إدارة كاملة
            'ratings.view', 'ratings.create', 'ratings.update', 'ratings.delete',
            'ratings.approve', 'ratings.reject', 'ratings.view-all',

            // تذاكر الدعم - إدارة كاملة
            'support-tickets.view', 'support-tickets.create', 'support-tickets.update',
            'support-tickets.close', 'support-tickets.reply', 'support-tickets.view-all',
            'support-tickets.assign', 'support-tickets.delete',

            // عقود الاستئجار - إدارة كاملة
            'rental-contracts.view', 'rental-contracts.create', 'rental-contracts.update',
            'rental-contracts.sign', 'rental-contracts.approve', 'rental-contracts.reject',
            'rental-contracts.terminate', 'rental-contracts.view-all',

            // الفواتير - إدارة كاملة
            'invoices.view', 'invoices.create', 'invoices.update',
            'invoices.issue', 'invoices.mark-paid', 'invoices.cancel', 'invoices.view-all',

            // CMS - إدارة كاملة
            'pages.view', 'pages.create', 'pages.update', 'pages.delete',
            'faqs.view', 'faqs.create', 'faqs.update', 'faqs.delete',
            'banners.view', 'banners.create', 'banners.update', 'banners.delete',

            // تفضيلات الإشعارات
            'notification-preferences.view', 'notification-preferences.update',

            // إعدادات النظام
            'settings.view', 'settings.update',

            // مزايا الرعاية
            'sponsor-benefits.view', 'sponsor-benefits.create', 'sponsor-benefits.update', 'sponsor-benefits.deliver',
        ];
        $admin->syncPermissions($adminPermissions);
        $this->command->info("   ✅ Admin: " . count($adminPermissions) . " صلاحية");

        // ============================================================
        // Supervisor - مشرف
        // ============================================================
        $supervisor = Role::firstOrCreate(
            ['name' => 'supervisor'],
            [
                'display_name' => 'مشرف',
                'description' => 'صلاحيات إشرافية - مراجعة الطلبات والملفات',
                'is_system' => true,
                'level' => 50,
            ]
        );
        $supervisorPermissions = [
            'users.view',
            'roles.view',
            'permissions.view',

            // عرض المحتوى
            'events.view',
            'sections.view',
            'spaces.view',
            'expo-services.view',
            'categories.view',
            'cities.view',

            // مراجعة الملفات والطلبات
            'profiles.view', 'profiles.view-all', 'profiles.approve', 'profiles.reject',
            'visit-requests.view', 'visit-requests.view-all', 'visit-requests.approve', 'visit-requests.reject',
            'rental-requests.view', 'rental-requests.view-all', 'rental-requests.approve', 'rental-requests.reject',

            // التقارير
            'reports.view',
            'payments.view',

            // الرعاة - عرض + موافقة/رفض عقود
            'sponsors.view', 'sponsors.view-all',
            'sponsor-contracts.view', 'sponsor-contracts.view-all',
            'sponsor-contracts.approve', 'sponsor-contracts.reject',
            'sponsor-packages.view',
            'sponsor-payments.view',
            'sponsor-assets.view',

            // التقييمات - عرض + اعتماد/رفض
            'ratings.view', 'ratings.view-all', 'ratings.approve', 'ratings.reject',

            // تذاكر الدعم - عرض + رد + إغلاق + تعيين
            'support-tickets.view', 'support-tickets.view-all',
            'support-tickets.reply', 'support-tickets.close', 'support-tickets.assign',

            // عقود الاستئجار - عرض + اعتماد/رفض
            'rental-contracts.view', 'rental-contracts.view-all',
            'rental-contracts.approve', 'rental-contracts.reject',

            // الفواتير - عرض + إصدار + تسجيل دفع
            'invoices.view', 'invoices.view-all',
            'invoices.issue', 'invoices.mark-paid',

            // CMS - عرض فقط
            'pages.view',
            'faqs.view',
            'banners.view',

            // إعدادات النظام - عرض
            'settings.view',

            // مزايا الرعاية - عرض
            'sponsor-benefits.view',

            // تسجيل دفعة استئجار
            'rental-requests.record-payment',
        ];
        $supervisor->syncPermissions($supervisorPermissions);
        $this->command->info("   ✅ Supervisor (مشرف): " . count($supervisorPermissions) . " صلاحية");

        // ============================================================
        // Merchant - تاجر (يستأجر مساحات في المعارض)
        // ============================================================
        $merchant = Role::firstOrCreate(
            ['name' => 'merchant'],
            [
                'display_name' => 'تاجر',
                'description' => 'تاجر يستأجر مساحات في المعارض - بحث وحجز وتأجير',
                'is_system' => true,
                'level' => 20,
            ]
        );
        $merchantPermissions = [
            // الملف التجاري - إنشاء وعرض وتعديل
            'profiles.view', 'profiles.create', 'profiles.update',

            // المفضلة
            'favorites.view', 'favorites.create', 'favorites.delete',

            // الإشعارات
            'notifications.view', 'notifications.update',

            // طلبات الزيارة - CRUD كامل (ملكه فقط)
            'visit-requests.view', 'visit-requests.create', 'visit-requests.update', 'visit-requests.delete',

            // طلبات الاستئجار - CRUD كامل (ملكه فقط)
            'rental-requests.view', 'rental-requests.create', 'rental-requests.update', 'rental-requests.delete',

            // عرض الفعاليات والمساحات والخدمات (بحث واستعراض)
            'events.view',
            'sections.view',
            'spaces.view',
            'expo-services.view',
            'categories.view',
            'cities.view',

            // عرض المدفوعات الخاصة به
            'payments.view',

            // التقييمات - إنشاء وتعديل وحذف (للكيانات التي تعامل معها)
            'ratings.view', 'ratings.create', 'ratings.update', 'ratings.delete',

            // تذاكر الدعم - إنشاء والمتابعة
            'support-tickets.view', 'support-tickets.create',
            'support-tickets.reply', 'support-tickets.close',

            // عقود الاستئجار - عرض وتوقيع (عقوده هو)
            'rental-contracts.view', 'rental-contracts.sign',

            // الفواتير - عرض الخاصة به
            'invoices.view',

            // تفضيلات الإشعارات
            'notification-preferences.view', 'notification-preferences.update',
        ];
        $merchant->syncPermissions($merchantPermissions);
        $this->command->info("   ✅ Merchant (تاجر): " . count($merchantPermissions) . " صلاحية");

        // ============================================================
        // Investor - مستثمر (يعرض مساحاته للإيجار)
        // ============================================================
        $investor = Role::firstOrCreate(
            ['name' => 'investor'],
            [
                'display_name' => 'مستثمر',
                'description' => 'مستثمر يعرض مساحاته في المعارض للإيجار - إدارة المساحات والطلبات',
                'is_system' => true,
                'level' => 20,
            ]
        );
        $investorPermissions = [
            // الملف التجاري - إنشاء وعرض وتعديل
            'profiles.view', 'profiles.create', 'profiles.update',

            // المفضلة
            'favorites.view', 'favorites.create', 'favorites.delete',

            // الإشعارات
            'notifications.view', 'notifications.update',

            // طلبات الزيارة - عرض وإنشاء فقط
            'visit-requests.view', 'visit-requests.create', 'visit-requests.update', 'visit-requests.delete',

            // الفعاليات - عرض
            'events.view',
            'sections.view',

            // المساحات - إدارة كاملة (يضيف مساحاته الخاصة)
            'spaces.view', 'spaces.create', 'spaces.update', 'spaces.delete',

            // الخدمات والتصنيفات والمدن (عرض)
            'expo-services.view',
            'categories.view',
            'cities.view',

            // طلبات الاستئجار - عرض والموافقة/الرفض (الطلبات على مساحاته)
            'rental-requests.view', 'rental-requests.approve', 'rental-requests.reject',

            // طلبات الزيارة - الموافقة/الرفض (لمساحاته)
            'visit-requests.approve', 'visit-requests.reject',

            // المدفوعات
            'payments.view',

            // التقارير (إحصائيات مساحاته)
            'reports.view',

            // التقييمات - عرض (تقييمات مساحاته)
            'ratings.view',

            // تذاكر الدعم - إنشاء والمتابعة
            'support-tickets.view', 'support-tickets.create',
            'support-tickets.reply', 'support-tickets.close',

            // عقود الاستئجار - عرض وتوقيع (عقود مساحاته)
            'rental-contracts.view', 'rental-contracts.sign',

            // الفواتير - عرض الخاصة به
            'invoices.view',

            // تفضيلات الإشعارات
            'notification-preferences.view', 'notification-preferences.update',
        ];
        $investor->syncPermissions($investorPermissions);
        $this->command->info("   ✅ Investor (مستثمر): " . count($investorPermissions) . " صلاحية");

        // ============================================================
        // Sponsor - راعي (يرعى فعاليات ومعارض)
        // ============================================================
        $sponsor = Role::firstOrCreate(
            ['name' => 'sponsor'],
            [
                'display_name' => 'راعي',
                'description' => 'راعي يرعى فعاليات ومعارض - يدير عقوده ومواده الإعلانية ويتابع ظهوره',
                'is_system' => true,
                'level' => 20,
            ]
        );
        $sponsorPermissions = [
            // الملف التجاري
            'profiles.view', 'profiles.create', 'profiles.update',

            // الإشعارات
            'notifications.view', 'notifications.update',

            // عرض الفعاليات
            'events.view',
            'categories.view',
            'cities.view',

            // الرعاة - عقوده ومدفوعاته (read-only)
            'sponsors.view',
            'sponsor-contracts.view',
            'sponsor-payments.view',
            'sponsor-packages.view',

            // المواد الإعلانية - CRUD كامل
            'sponsor-assets.view', 'sponsor-assets.create', 'sponsor-assets.update', 'sponsor-assets.delete',

            // تتبع الظهور
            'sponsor-exposure.view',

            // تذاكر الدعم - إنشاء والمتابعة
            'support-tickets.view', 'support-tickets.create',
            'support-tickets.reply', 'support-tickets.close',

            // الفواتير - عرض الخاصة به
            'invoices.view',

            // تفضيلات الإشعارات
            'notification-preferences.view', 'notification-preferences.update',
        ];
        $sponsor->syncPermissions($sponsorPermissions);
        $this->command->info("   ✅ Sponsor (راعي): " . count($sponsorPermissions) . " صلاحية");

        // ============================================================
        // User - مستخدم عادي (زائر)
        // ============================================================
        $user = Role::firstOrCreate(
            ['name' => 'user'],
            [
                'display_name' => 'مستخدم',
                'description' => 'مستخدم عادي - تصفح الفعاليات وإنشاء طلبات زيارة',
                'is_system' => true,
                'level' => 10,
            ]
        );
        $userPermissions = [
            // عرض الفعاليات والمساحات
            'events.view',
            'sections.view',
            'spaces.view',
            'expo-services.view',
            'categories.view',
            'cities.view',

            // المفضلة
            'favorites.view', 'favorites.create', 'favorites.delete',

            // الإشعارات
            'notifications.view', 'notifications.update',

            // طلبات الزيارة فقط (بدون استئجار)
            'visit-requests.view', 'visit-requests.create', 'visit-requests.update', 'visit-requests.delete',

            // تذاكر الدعم - إنشاء والمتابعة
            'support-tickets.view', 'support-tickets.create',
            'support-tickets.reply', 'support-tickets.close',

            // تفضيلات الإشعارات
            'notification-preferences.view', 'notification-preferences.update',
        ];
        $user->syncPermissions($userPermissions);
        $this->command->info("   ✅ User (مستخدم): " . count($userPermissions) . " صلاحية");

        $this->command->info('');
        $this->command->info('✅ جميع الأدوار والصلاحيات تم إنشاؤها بنجاح');
    }

    /**
     * إنشاء مستخدم Super Admin
     */
    protected function createSuperAdmin(): void
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $superAdmin->assignRole('super-admin');

        $this->command->info('');
        $this->command->info('✅ Super Admin created:');
        $this->command->info('   Email: admin@example.com');
        $this->command->info('   Password: password');
        $this->command->warn('   ⚠️  Change this password in production!');
    }
}
