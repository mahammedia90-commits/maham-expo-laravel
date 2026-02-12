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
        ];
        $admin->syncPermissions($adminPermissions);
        $this->command->info("   ✅ Admin: " . count($adminPermissions) . " صلاحية");

        // ============================================================
        // Moderator - مشرف
        // ============================================================
        $moderator = Role::firstOrCreate(
            ['name' => 'moderator'],
            [
                'display_name' => 'مشرف',
                'description' => 'صلاحيات إشرافية - مراجعة الطلبات والملفات',
                'is_system' => true,
                'level' => 50,
            ]
        );
        $moderatorPermissions = [
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
        ];
        $moderator->syncPermissions($moderatorPermissions);
        $this->command->info("   ✅ Moderator: " . count($moderatorPermissions) . " صلاحية");

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

            // طلبات الاستئجار - عرض (الطلبات على مساحاته)
            'rental-requests.view',

            // المدفوعات
            'payments.view',

            // التقارير (إحصائيات مساحاته)
            'reports.view',
        ];
        $investor->syncPermissions($investorPermissions);
        $this->command->info("   ✅ Investor (مستثمر): " . count($investorPermissions) . " صلاحية");

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
