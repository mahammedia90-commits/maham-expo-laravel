<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FollowUpSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\FollowUp::create(['lead_id' => 2, 'deal_id' => 1, 'due_date' => now()->addDays(7), 'type' => 'email', 'status' => 'pending', 'assigned_to' => null]);
        \App\Models\FollowUp::create(['lead_id' => 3, 'deal_id' => 2, 'due_date' => now()->addDays(3), 'type' => 'call', 'status' => 'pending', 'assigned_to' => null]);
        \App\Models\FollowUp::create(['lead_id' => 4, 'deal_id' => 3, 'due_date' => now()->addDays(5), 'type' => 'meeting', 'status' => 'completed', 'outcome' => 'positive', 'assigned_to' => null]);
    }
}
