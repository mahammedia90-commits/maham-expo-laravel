<?php

namespace Database\Seeders;

use App\Models\RentalContract;
use App\Models\Space;
use Illuminate\Database\Seeder;

class RentalContractSeeder extends Seeder
{
    public function run(): void
    {
        $spaces = Space::limit(5)->get();

        $contracts = [
            [
                'space_id' => $spaces[0]->id ?? 1,
                'merchant_id' => 1,
                'start_date' => now()->subDays(30)->toDateString(),
                'end_date' => now()->addDays(60)->toDateString(),
                'amount' => 50000,
                'currency' => 'SAR',
                'status' => 'active',
                'payment_status' => 'paid',
                'contract_number' => 'RC-2024-001',
                'notes' => 'Premium booth rental for MAHAM Expo 2024',
            ],
            [
                'space_id' => $spaces[1]->id ?? 2,
                'merchant_id' => 2,
                'start_date' => now()->subDays(15)->toDateString(),
                'end_date' => now()->addDays(75)->toDateString(),
                'amount' => 70000,
                'currency' => 'SAR',
                'status' => 'active',
                'payment_status' => 'partial',
                'contract_number' => 'RC-2024-002',
                'notes' => 'Large booth with premium features',
            ],
            [
                'space_id' => $spaces[2]->id ?? 3,
                'merchant_id' => 3,
                'start_date' => now()->subDays(5)->toDateString(),
                'end_date' => now()->addDays(85)->toDateString(),
                'amount' => 55000,
                'currency' => 'SAR',
                'status' => 'active',
                'payment_status' => 'unpaid',
                'contract_number' => 'RC-2024-003',
                'notes' => 'Standard booth rental',
            ],
            [
                'space_id' => $spaces[3]->id ?? 4,
                'merchant_id' => 4,
                'start_date' => now()->subDays(45)->toDateString(),
                'end_date' => now()->addDays(30)->toDateString(),
                'amount' => 85000,
                'currency' => 'SAR',
                'status' => 'active',
                'payment_status' => 'paid',
                'contract_number' => 'RC-2024-004',
                'notes' => 'XL booth with extended services',
            ],
            [
                'space_id' => $spaces[4]->id ?? 5,
                'merchant_id' => 5,
                'start_date' => now()->addDays(10)->toDateString(),
                'end_date' => now()->addDays(100)->toDateString(),
                'amount' => 120000,
                'currency' => 'SAR',
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'contract_number' => 'RC-2024-005',
                'notes' => 'Premium corner booth - awaiting signature',
            ],
        ];

        foreach ($contracts as $contract) {
            RentalContract::create($contract);
        }
    }
}
