<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lead;

class LeadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Lead::create([
            'full_name' => 'Ahmed Al-Rashid',
            'company' => 'Tech Solutions LLC',
            'phone' => '+966501234567',
            'phone_whatsapp' => '+966501234567',
            'email' => 'ahmed@techsolutions.sa',
            'city' => 'Riyadh',
            'sector' => 'Technology',
            'lead_type' => 'investor',
            'source' => 'referral',
            'priority' => 'high',
            'assigned_to' => null,
            'ai_score' => 85,
            'status' => 'active',
            'next_action' => 'Schedule meeting',
            'next_action_date' => now()->addDays(3),
            'notes' => 'Interested in sponsorship',
            'last_contacted_at' => now(),
        ]);

        Lead::create([
            'full_name' => 'Sara Mohammed',
            'company' => 'Finance Corp',
            'phone' => '+966501234568',
            'email' => 'sara@financecorp.sa',
            'city' => 'Jeddah',
            'sector' => 'Finance',
            'lead_type' => 'sponsor',
            'source' => 'website',
            'priority' => 'high',
            'assigned_to' => null,
            'ai_score' => 78,
            'status' => 'active',
            'next_action' => 'Send proposal',
            'next_action_date' => now()->addDays(5),
            'notes' => 'Ready to invest',
            'last_contacted_at' => now()->subDay(),
        ]);

        Lead::create([
            'full_name' => 'Mohammed Hassan',
            'company' => 'Fashion Boutique',
            'phone' => '+966501234569',
            'email' => 'moh@fashionboutique.sa',
            'city' => 'Dammam',
            'sector' => 'Retail',
            'lead_type' => 'merchant',
            'source' => 'email',
            'priority' => 'medium',
            'assigned_to' => null,
            'ai_score' => 65,
            'status' => 'active',
            'next_action' => 'Follow up',
            'next_action_date' => now()->addDays(7),
            'notes' => 'First-time buyer',
        ]);
    }
}
