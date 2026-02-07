<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPermissions();
        $this->seedRoles();
        $this->seedSuperAdmin();
    }

    protected function seedPermissions(): void
    {
        $permissions = config('auth-service.default_permissions', [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'roles.view',
            'roles.create',
            'roles.update',
            'roles.delete',
            'permissions.view',
            'permissions.create',
            'permissions.update',
            'permissions.delete',
            'services.view',
            'services.create',
            'services.update',
            'services.delete',
        ]);

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, ['is_system' => true]);
        }

        $this->command->info('✅ Permissions seeded successfully');
    }

    protected function seedRoles(): void
    {
        $roles = config('auth-service.default_roles', [
            'super-admin' => [
                'description' => 'Full system access',
                'is_system' => true,
                'level' => 100,
            ],
            'admin' => [
                'description' => 'Administrative access',
                'is_system' => true,
                'level' => 50,
            ],
            'user' => [
                'description' => 'Regular user access',
                'is_system' => true,
                'level' => 10,
            ],
        ]);

        foreach ($roles as $name => $data) {
            $role = Role::findOrCreate($name, [
                'display_name' => ucfirst(str_replace('-', ' ', $name)),
                'description' => $data['description'] ?? null,
                'is_system' => $data['is_system'] ?? false,
                'level' => $data['level'] ?? 0,
            ]);

            // إعطاء صلاحيات للـ admin
            if ($name === 'admin') {
                $role->syncPermissions([
                    'users.view',
                    'users.create',
                    'users.update',
                    'roles.view',
                    'permissions.view',
                ]);
            }
        }

        $this->command->info('✅ Roles seeded successfully');
    }

    protected function seedSuperAdmin(): void
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@auth-service.local'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        $superAdmin->assignRole('super-admin');

        $this->command->info('✅ Super Admin created');
        $this->command->info('   Email: admin@auth-service.local');
        $this->command->info('   Password: password');
        $this->command->warn('   ⚠️ تأكد من تغيير كلمة المرور في بيئة الإنتاج!');
    }
}
