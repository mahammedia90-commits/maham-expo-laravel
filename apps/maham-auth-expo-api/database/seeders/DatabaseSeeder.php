<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed permissions and roles (comprehensive)
        $this->call(RolesAndPermissionsSeeder::class);

        // Seed services (service-to-service auth)
        $this->call(ServiceSeeder::class);

        // Seed sample users with roles
        $this->call(SampleUsersSeeder::class);
    }
}
