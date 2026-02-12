<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleUsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // Admin user
            [
                'name' => 'Admin User',
                'email' => 'admin@maham-expo.sa',
                'phone' => '0501000001',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
                'role' => 'admin',
            ],
            // Moderator
            [
                'name' => 'Moderator User',
                'email' => 'moderator@maham-expo.sa',
                'phone' => '0501000002',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
                'role' => 'moderator',
            ],
            // Investor 1 - TechVentures (matches expo-api profile user_id 10)
            [
                'id' => '00000000-0000-0000-0000-000000000010',
                'name' => 'Ahmed Al-Rashidi',
                'email' => 'ahmed@techventures.sa',
                'phone' => '0501234567',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
                'role' => 'investor',
            ],
            // Merchant 1 - Al-Salam Trading (matches expo-api profile user_id 11)
            [
                'id' => '00000000-0000-0000-0000-000000000011',
                'name' => 'Mohammed Al-Harbi',
                'email' => 'mohammed@alsalamtrading.sa',
                'phone' => '0567891234',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
                'role' => 'merchant',
            ],
            // Merchant 2 - Fresh Foods (matches expo-api profile user_id 12)
            [
                'id' => '00000000-0000-0000-0000-000000000012',
                'name' => 'Khalid Al-Otaibi',
                'email' => 'khalid@freshfoods.sa',
                'phone' => '0559876543',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
                'role' => 'merchant',
            ],
            // Investor 2 - Quick Invest (matches expo-api profile user_id 13)
            [
                'id' => '00000000-0000-0000-0000-000000000013',
                'name' => 'Sultan Al-Qahtani',
                'email' => 'sultan@quickinvest.sa',
                'phone' => '0544332211',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
                'role' => 'investor',
            ],
            // Merchant 3 - Beauty World (matches expo-api profile user_id 14)
            [
                'id' => '00000000-0000-0000-0000-000000000014',
                'name' => 'Noura Al-Dossary',
                'email' => 'noura@beautyworld.sa',
                'phone' => '0512345678',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
                'role' => 'merchant',
            ],
            // Investor 3 - Gulf Properties (matches expo-api profile user_id 15)
            [
                'id' => '00000000-0000-0000-0000-000000000015',
                'name' => 'Faisal Al-Ghamdi',
                'email' => 'faisal@gulfproperties.sa',
                'phone' => '0538765432',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
                'role' => 'investor',
            ],
            // Regular user (no business profile)
            [
                'name' => 'Visitor User',
                'email' => 'visitor@example.com',
                'phone' => '0505050505',
                'password' => Hash::make('password'),
                'status' => 'active',
                'email_verified_at' => now(),
                'role' => 'user',
            ],
            // Inactive user
            [
                'name' => 'Inactive User',
                'email' => 'inactive@example.com',
                'phone' => '0506060606',
                'password' => Hash::make('password'),
                'status' => 'inactive',
                'email_verified_at' => null,
                'role' => 'user',
            ],
            // Pending user
            [
                'name' => 'Pending User',
                'email' => 'pending@example.com',
                'phone' => '0507070707',
                'password' => Hash::make('password'),
                'status' => 'pending',
                'email_verified_at' => null,
                'role' => 'user',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'] ?? 'user';
            unset($userData['role']);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            $user->assignRole($role);

            $this->command->info("Created user: {$user->name} ({$user->email}) - Role: {$role}");
        }
    }
}
