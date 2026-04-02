<?php

namespace Database\Seeders;

use App\Models\SponsorContract;
use App\Models\Sponsor;
use Illuminate\Database\Seeder;

class SponsorContractSeeder extends Seeder
{
    public function run(): void
    {
        $sponsors = Sponsor::limit(6)->get();

        $contracts = [
            [
                'sponsor_id' => $sponsors[0]->id ?? 1,
                'event_id' => 1,
                'contract_type' => 'sponsorship',
                'start_date' => now()->subMonths(6)->toDateString(),
                'end_date' => now()->addMonths(6)->toDateString(),
                'amount' => 2500000,
                'currency' => 'SAR',
                'status' => 'active',
                'contract_number' => 'SC-2024-001',
                'deliverables_json' => json_encode(['Logo placement', 'Booth space', 'Media coverage']),
                'payment_terms' => 'Monthly',
                'notes' => 'Platinum sponsorship for MAHAM Expo 2024',
            ],
            [
                'sponsor_id' => $sponsors[1]->id ?? 2,
                'event_id' => 1,
                'contract_type' => 'sponsorship',
                'start_date' => now()->subMonths(4)->toDateString(),
                'end_date' => now()->addMonths(8)->toDateString(),
                'amount' => 1500000,
                'currency' => 'SAR',
                'status' => 'active',
                'contract_number' => 'SC-2024-002',
                'deliverables_json' => json_encode(['Logo placement', 'Premium booth', 'Event promotion']),
                'payment_terms' => 'Quarterly',
                'notes' => 'Gold sponsorship agreement',
            ],
            [
                'sponsor_id' => $sponsors[2]->id ?? 3,
                'event_id' => 1,
                'contract_type' => 'sponsorship',
                'start_date' => now()->subMonths(3)->toDateString(),
                'end_date' => now()->addMonths(9)->toDateString(),
                'amount' => 1200000,
                'currency' => 'SAR',
                'status' => 'active',
                'contract_number' => 'SC-2024-003',
                'deliverables_json' => json_encode(['Logo placement', 'Standard booth']),
                'payment_terms' => 'Monthly',
                'notes' => 'Gold sponsorship - logistics partner',
            ],
            [
                'sponsor_id' => $sponsors[3]->id ?? 4,
                'event_id' => 1,
                'contract_type' => 'sponsorship',
                'start_date' => now()->subMonths(2)->toDateString(),
                'end_date' => now()->addMonths(10)->toDateString(),
                'amount' => 800000,
                'currency' => 'SAR',
                'status' => 'active',
                'contract_number' => 'SC-2024-004',
                'deliverables_json' => json_encode(['Logo placement', 'Media support']),
                'payment_terms' => 'Upfront',
                'notes' => 'Silver sponsorship - media partner',
            ],
            [
                'sponsor_id' => $sponsors[4]->id ?? 5,
                'event_id' => 1,
                'contract_type' => 'sponsorship',
                'start_date' => now()->subMonths(1)->toDateString(),
                'end_date' => now()->addMonths(11)->toDateString(),
                'amount' => 750000,
                'currency' => 'SAR',
                'status' => 'active',
                'contract_number' => 'SC-2024-005',
                'deliverables_json' => json_encode(['Logo placement', 'Training workshop']),
                'payment_terms' => 'Monthly',
                'notes' => 'Silver sponsorship - education partner',
            ],
            [
                'sponsor_id' => $sponsors[5]->id ?? 6,
                'event_id' => 1,
                'contract_type' => 'sponsorship',
                'start_date' => now()->subDays(15)->toDateString(),
                'end_date' => now()->addMonths(12)->toDateString(),
                'amount' => 400000,
                'currency' => 'SAR',
                'status' => 'active',
                'contract_number' => 'SC-2024-006',
                'deliverables_json' => json_encode(['Logo placement']),
                'payment_terms' => 'Upfront',
                'notes' => 'Bronze sponsorship - security services',
            ],
        ];

        foreach ($contracts as $contract) {
            SponsorContract::create($contract);
        }
    }
}
