<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            CitySeeder::class,
            ServiceSeeder::class,
            EventSeeder::class,
            BusinessProfileSeeder::class,
            VisitRequestSeeder::class,
            RentalRequestSeeder::class,
            FavoriteSeeder::class,
            NotificationSeeder::class,
        ]);
    }
}
