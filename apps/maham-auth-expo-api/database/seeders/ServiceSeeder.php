<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'expo-api',
                'display_name' => 'Expo API Service',
                'description' => 'Main exhibition and marketplace API service',
                'status' => 'active',
                'allowed_permissions' => [
                    'users.view',
                    'users.create',
                    'users.update',
                ],
            ],
            [
                'name' => 'expo-mobile',
                'display_name' => 'Expo Mobile App',
                'description' => 'Mobile application for exhibition visitors and merchants',
                'status' => 'active',
                'allowed_permissions' => [
                    'users.view',
                ],
            ],
            [
                'name' => 'expo-admin',
                'display_name' => 'Expo Admin Dashboard',
                'description' => 'Administrative dashboard for managing exhibitions',
                'status' => 'active',
                'allowed_permissions' => [
                    'users.view',
                    'users.create',
                    'users.update',
                    'users.delete',
                    'roles.view',
                    'roles.create',
                    'roles.update',
                    'permissions.view',
                ],
            ],
        ];

        foreach ($services as $serviceData) {
            $service = Service::firstOrCreate(
                ['name' => $serviceData['name']],
                $serviceData
            );

            $this->command->info("Created service: {$service->display_name}");
            $this->command->info("  Token: {$service->token}");
        }
    }
}
