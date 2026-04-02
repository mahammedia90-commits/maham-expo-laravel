<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PerformanceSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Performance::create(['user_id' => null, 'date' => now()->subDay(), 'leads_assigned' => 5, 'leads_contacted' => 3, 'followups_completed' => 2, 'meetings_held' => 1, 'proposals_sent' => 1, 'deals_closed' => 0, 'revenue_generated' => 0, 'conversion_rate' => 20, 'avg_deal_value' => 50000, 'response_time_hours' => 4.5, 'daily_score' => 78]);
        \App\Models\Performance::create(['user_id' => null, 'date' => now()->subDay(), 'leads_assigned' => 8, 'leads_contacted' => 6, 'followups_completed' => 4, 'meetings_held' => 2, 'proposals_sent' => 2, 'deals_closed' => 1, 'revenue_generated' => 75000, 'conversion_rate' => 25, 'avg_deal_value' => 75000, 'response_time_hours' => 3.2, 'daily_score' => 92]);
    }
}
