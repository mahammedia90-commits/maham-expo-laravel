<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Badge::create(['name' => 'بطل الأداء', 'name_en' => 'Performance Champion', 'description' => 'Top performer', 'icon' => 'trophy', 'tier' => 'gold', 'criteria' => 'Top 5%', 'auto_grant' => false, 'active' => true]);
        \App\Models\Badge::create(['name' => 'الملك', 'name_en' => 'Royal Member', 'description' => 'VIP member', 'icon' => 'crown', 'tier' => 'platinum', 'criteria' => 'Spend 500k+', 'auto_grant' => true, 'active' => true]);
        \App\Models\Badge::create(['name' => 'النجم', 'name_en' => 'Rising Star', 'description' => 'New member', 'icon' => 'star', 'tier' => 'silver', 'criteria' => 'Join event', 'auto_grant' => true, 'active' => true]);
    }
}
