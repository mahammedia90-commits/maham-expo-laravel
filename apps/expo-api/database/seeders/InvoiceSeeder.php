<?php

namespace Database\Seeders;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\RentalContract;
use App\Models\SponsorContract;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        if (Invoice::count() > 0) {
            $this->command->info('Invoices already seeded, skipping.');
            return;
        }

        $invoiceCount = 0;

        // ============================================================
        // Invoices from Rental Contracts
        // ============================================================
        $rentalContracts = RentalContract::with(['rentalRequest.space', 'event'])->get();

        foreach ($rentalContracts as $contract) {
            $event = $contract->event;
            $space = $contract->rentalRequest?->space;

            $subtotal = (float) $contract->total_amount;
            $taxRate = 0.15; // 15% VAT
            $taxAmount = round($subtotal * $taxRate, 2);
            $totalAmount = round($subtotal + $taxAmount, 2);
            $paidAmount = (float) $contract->paid_amount;

            // Determine invoice status from contract
            $status = match ($contract->payment_status?->value ?? $contract->payment_status) {
                'paid' => InvoiceStatus::PAID,
                'partial' => InvoiceStatus::PARTIALLY_PAID,
                'pending' => InvoiceStatus::ISSUED,
                default => InvoiceStatus::DRAFT,
            };

            $isPaid = $status === InvoiceStatus::PAID;

            $items = [
                [
                    'description' => "Space rental: " . ($space?->name ?? 'Exhibition Space'),
                    'description_ar' => "استئجار مساحة: " . ($space?->name_ar ?? 'مساحة معرضية'),
                    'quantity' => 1,
                    'unit_price' => $subtotal,
                    'total' => $subtotal,
                ],
                [
                    'description' => 'VAT (15%)',
                    'description_ar' => 'ضريبة القيمة المضافة (15%)',
                    'quantity' => 1,
                    'unit_price' => $taxAmount,
                    'total' => $taxAmount,
                ],
            ];

            Invoice::create([
                'user_id' => $contract->merchant_id,
                'invoiceable_type' => RentalContract::class,
                'invoiceable_id' => $contract->id,
                'title' => "Rental Invoice - " . ($event?->name ?? 'Event') . " / " . ($space?->name ?? 'Space'),
                'title_ar' => "فاتورة استئجار - " . ($event?->name_ar ?? 'فعالية') . " / " . ($space?->name_ar ?? 'مساحة'),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'paid_amount' => $isPaid ? $totalAmount : round($paidAmount * (1 + $taxRate), 2),
                'status' => $status,
                'issue_date' => $contract->created_at?->toDateString() ?? now()->subDays(10)->toDateString(),
                'due_date' => $contract->start_date ?? now()->addDays(14)->toDateString(),
                'paid_at' => $isPaid ? now()->subDays(4) : null,
                'payment_method' => $isPaid ? 'bank_transfer' : null,
                'transaction_reference' => $isPaid ? 'TXN-RC-' . str_pad($invoiceCount + 1, 3, '0', STR_PAD_LEFT) : null,
                'items' => $items,
                'notes' => "Invoice for space rental at " . ($event?->name ?? 'event'),
                'notes_ar' => "فاتورة استئجار مساحة في " . ($event?->name_ar ?? 'فعالية'),
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ]);

            $invoiceCount++;
            $this->command->info("Created rental invoice for contract: {$contract->contract_number}");
        }

        // ============================================================
        // Invoices from Sponsor Contracts
        // ============================================================
        $sponsorContracts = SponsorContract::with(['sponsor', 'sponsorPackage', 'event'])->get();

        foreach ($sponsorContracts as $contract) {
            $sponsor = $contract->sponsor;
            $package = $contract->sponsorPackage;
            $event = $contract->event;

            $subtotal = (float) $contract->total_amount;
            $taxRate = 0.15;
            $taxAmount = round($subtotal * $taxRate, 2);
            $totalAmount = round($subtotal + $taxAmount, 2);
            $paidAmount = (float) $contract->paid_amount;

            $status = match ($contract->payment_status?->value ?? $contract->payment_status) {
                'paid' => InvoiceStatus::PAID,
                'partial' => InvoiceStatus::PARTIALLY_PAID,
                'pending' => InvoiceStatus::ISSUED,
                default => InvoiceStatus::DRAFT,
            };

            $isPaid = $status === InvoiceStatus::PAID;

            $items = [
                [
                    'description' => "Sponsorship: " . ($package?->name ?? 'Sponsor Package') . " - " . ($event?->name ?? 'Event'),
                    'description_ar' => "رعاية: " . ($package?->name_ar ?? 'باقة رعاية') . " - " . ($event?->name_ar ?? 'فعالية'),
                    'quantity' => 1,
                    'unit_price' => $subtotal,
                    'total' => $subtotal,
                ],
                [
                    'description' => 'VAT (15%)',
                    'description_ar' => 'ضريبة القيمة المضافة (15%)',
                    'quantity' => 1,
                    'unit_price' => $taxAmount,
                    'total' => $taxAmount,
                ],
            ];

            Invoice::create([
                'user_id' => $sponsor?->user_id ?? $contract->signed_by,
                'invoiceable_type' => SponsorContract::class,
                'invoiceable_id' => $contract->id,
                'title' => "Sponsorship Invoice - " . ($sponsor?->company_name ?? 'Sponsor') . " / " . ($package?->name ?? 'Package'),
                'title_ar' => "فاتورة رعاية - " . ($sponsor?->company_name_ar ?? 'راعي') . " / " . ($package?->name_ar ?? 'باقة'),
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => 0,
                'total_amount' => $totalAmount,
                'paid_amount' => $isPaid ? $totalAmount : round($paidAmount * (1 + $taxRate), 2),
                'status' => $status,
                'issue_date' => $contract->created_at?->toDateString() ?? now()->subDays(15)->toDateString(),
                'due_date' => $contract->start_date ?? now()->addDays(30)->toDateString(),
                'paid_at' => $isPaid ? now()->subDays(3) : null,
                'payment_method' => $isPaid ? 'bank_transfer' : null,
                'transaction_reference' => $isPaid ? 'TXN-SC-' . str_pad($invoiceCount + 1, 3, '0', STR_PAD_LEFT) : null,
                'items' => $items,
                'notes' => "Invoice for " . ($package?->name ?? 'sponsorship') . " at " . ($event?->name ?? 'event'),
                'notes_ar' => "فاتورة " . ($package?->name_ar ?? 'رعاية') . " في " . ($event?->name_ar ?? 'فعالية'),
                'created_by' => '00000000-0000-0000-0000-000000000099',
            ]);

            $invoiceCount++;
            $this->command->info("Created sponsor invoice for: " . ($sponsor?->name ?? 'sponsor'));
        }

        $this->command->info("Created {$invoiceCount} invoices total.");
    }
}
