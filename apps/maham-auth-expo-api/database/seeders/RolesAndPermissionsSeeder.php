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

        // إنشاء الأدوار الافتراضية
        $this->createDefaultRoles($permissions);

        // إنشاء مستخدم Super Admin
        $this->createSuperAdmin();
    }

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
                        'group' => $group,
                        'is_system' => true,
                    ]
                );
            }
        }

        return $permissions;
    }

    protected function createDefaultRoles(array $permissions): void
    {
        // Super Admin - كل الصلاحيات
        $superAdmin = Role::firstOrCreate(
            ['name' => 'super-admin'],
            [
                'display_name' => 'مدير عام',
                'description' => 'صلاحيات كاملة على النظام',
                'is_system' => true,
                'level' => 100,
            ]
        );
        $superAdmin->syncPermissions(array_keys($permissions));

        // Admin - صلاحيات إدارية
        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'مدير',
                'description' => 'صلاحيات إدارية',
                'is_system' => true,
                'level' => 80,
            ]
        );
        $admin->syncPermissions([
            'users.view', 'users.create', 'users.update',
            'roles.view',
            'permissions.view',
            'services.view',
            'audit.view',
        ]);

        // Moderator - صلاحيات متوسطة
        $moderator = Role::firstOrCreate(
            ['name' => 'moderator'],
            [
                'display_name' => 'مشرف',
                'description' => 'صلاحيات إشرافية',
                'is_system' => true,
                'level' => 50,
            ]
        );
        $moderator->syncPermissions([
            'users.view',
            'roles.view',
            'permissions.view',
        ]);

        // User - صلاحيات أساسية
        Role::firstOrCreate(
            ['name' => 'user'],
            [
                'display_name' => 'مستخدم',
                'description' => 'صلاحيات مستخدم عادي',
                'is_system' => true,
                'level' => 10,
            ]
        );

        // Investor - مستثمر
        Role::firstOrCreate(
            ['name' => 'investor'],
            [
                'display_name' => 'مستثمر',
                'description' => 'مستثمر في المعارض',
                'is_system' => true,
                'level' => 20,
            ]
        );

        // Merchant - تاجر
        Role::firstOrCreate(
            ['name' => 'merchant'],
            [
                'display_name' => 'تاجر',
                'description' => 'تاجر يرغب في استئجار مساحات',
                'is_system' => true,
                'level' => 20,
            ]
        );
    }

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

        $this->command->info('✅ Super Admin created:');
        $this->command->info('   Email: admin@example.com');
        $this->command->info('   Password: password');
        $this->command->warn('   ⚠️  Change this password in production!');
    }
}
