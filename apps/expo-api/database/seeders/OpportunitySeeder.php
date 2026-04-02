<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OpportunitySeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\Opportunity::create(['title' => 'Investment Round A', 'description' => 'Series A investment opportunity for market expansion', 'type' => 'investment', 'status' => 'open', 'value' => 500000, 'event_id' => 1, 'assigned_to' => null, 'deadline' => now()->addDays(30)]);
        \App\Models\Opportunity::create(['title' => 'Premium Sponsorship Package', 'description' => 'Top-tier sponsorship with premium branding and visibility', 'type' => 'sponsorship', 'status' => 'in_review', 'value' => 250000, 'event_id' => 1, 'assigned_to' => null, 'deadline' => now()->addDays(60)]);
        \App\Models\Opportunity::create(['title' => 'Merchant Partnership Deal', 'description' => 'Exclusive merchant agreement for e-commerce integration', 'type' => 'merchant', 'status' => 'closed', 'value' => 150000, 'event_id' => 2, 'assigned_to' => null, 'deadline' => now()->subDays(10)]);
    }
}
