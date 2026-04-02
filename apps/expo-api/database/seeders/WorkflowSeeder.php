<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkflowSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Workflow::create(['name' => 'New Lead Notification', 'trigger_type' => 'lead_created', 'steps' => json_encode(['notify_admin', 'assign_to_rep']), 'is_active' => true]);
        \App\Models\Workflow::create(['name' => 'Payment Overdue', 'trigger_type' => 'payment_overdue', 'steps' => json_encode(['send_reminder', 'escalate']), 'is_active' => true]);
        \App\Models\Workflow::create(['name' => 'Contract Expiring', 'trigger_type' => 'contract_expiring', 'steps' => json_encode(['notify_party', 'suggest_renewal']), 'is_active' => true]);
    }
}
