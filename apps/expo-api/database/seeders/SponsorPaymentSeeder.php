<?php

namespace Database\Seeders;

use App\Models\SponsorPayment;
use App\Models\Sponsor;
use Illuminate\Database\Seeder;

class SponsorPaymentSeeder extends Seeder
{
    public function run(): void
    {
        $sponsors = Sponsor::limit(6)->get();

        $payments = [
            [
                'sponsor_id' => $sponsors[0]->id ?? 1,
                'amount' => 2500000,
                'currency' => 'SAR',
                'payment_method' => 'bank_transfer',
                'status' => 'completed',
                'payment_date' => now()->subMonths(5)->toDateString(),
                'reference_number' => 'PAY-2024-001',
                'notes' => 'Platinum sponsorship - Full amount',
            ],
            [
                'sponsor_id' => $sponsors[1]->id ?? 2,
                'amount' => 750000,
                'currency' => 'SAR',
                'payment_method' => 'bank_transfer',
                'status' => 'completed',
                'payment_date' => now()->subMonths(3)->toDateString(),
                'reference_number' => 'PAY-2024-002',
                'notes' => 'Gold sponsorship - First installment',
            ],
            [
                'sponsor_id' => $sponsors[1]->id ?? 2,
                'amount' => 750000,
                'currency' => 'SAR',
                'payment_method' => 'bank_transfer',
                'status' => 'completed',
                'payment_date' => now()->subDays(30)->toDateString(),
                'reference_number' => 'PAY-2024-003',
                'notes' => 'Gold sponsorship - Second installment',
            ],
            [
                'sponsor_id' => $sponsors[2]->id ?? 3,
                'amount' => 600000,
                'currency' => 'SAR',
                'payment_method' => 'bank_transfer',
                'status' => 'completed',
                'payment_date' => now()->subMonths(2)->toDateString(),
                'reference_number' => 'PAY-2024-004',
                'notes' => 'Gold sponsorship - First installment',
            ],
            [
                'sponsor_id' => $sponsors[2]->id ?? 3,
                'amount' => 600000,
                'currency' => 'SAR',
                'payment_method' => 'bank_transfer',
                'status' => 'pending',
                'payment_date' => now()->toDateString(),
                'reference_number' => 'PAY-2024-005',
                'notes' => 'Gold sponsorship - Second installment - Pending',
            ],
            [
                'sponsor_id' => $sponsors[3]->id ?? 4,
                'amount' => 800000,
                'currency' => 'SAR',
                'payment_method' => 'credit_card',
                'status' => 'completed',
                'payment_date' => now()->subMonths(1)->toDateString(),
                'reference_number' => 'PAY-2024-006',
                'notes' => 'Silver sponsorship - Media partner',
            ],
            [
                'sponsor_id' => $sponsors[4]->id ?? 5,
                'amount' => 375000,
                'currency' => 'SAR',
                'payment_method' => 'bank_transfer',
                'status' => 'completed',
                'payment_date' => now()->subDays(20)->toDateString(),
                'reference_number' => 'PAY-2024-007',
                'notes' => 'Silver sponsorship - First installment',
            ],
            [
                'sponsor_id' => $sponsors[4]->id ?? 5,
                'amount' => 375000,
                'currency' => 'SAR',
                'payment_method' => 'bank_transfer',
                'status' => 'pending',
                'payment_date' => now()->addDays(10)->toDateString(),
                'reference_number' => 'PAY-2024-008',
                'notes' => 'Silver sponsorship - Second installment - Scheduled',
            ],
            [
                'sponsor_id' => $sponsors[5]->id ?? 6,
                'amount' => 400000,
                'currency' => 'SAR',
                'payment_method' => 'bank_transfer',
                'status' => 'completed',
                'payment_date' => now()->subDays(10)->toDateString(),
                'reference_number' => 'PAY-2024-009',
                'notes' => 'Bronze sponsorship - Full upfront payment',
            ],
        ];

        foreach ($payments as $payment) {
            SponsorPayment::create($payment);
        }
    }
}
