<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Contract Workflow Service
 * Handles the complete lifecycle:
 * Booking Approved → Auto-Create Contract → Send for Signing → 
 * Generate ZATCA Invoice → Process Payment → Confirm Booking
 */
class ContractWorkflowService
{
    /**
     * Step 1: When a booking is approved, auto-create a contract
     */
    public function onBookingApproved(int $bookingId): ?int
    {
        $booking = DB::table('bookings')->find($bookingId);
        if (!$booking) {
            Log::warning("ContractWorkflow: Booking {$bookingId} not found");
            return null;
        }

        // Check if contract already exists for this booking
        $existing = DB::table('contracts')->where('bookingId', $bookingId)->first();
        if ($existing) {
            Log::info("ContractWorkflow: Contract already exists for booking {$bookingId}");
            return $existing->id;
        }

        // Get unit/event info
        $unit = DB::table('units')->find($booking->unitId ?? 0);
        $event = DB::table('events')->find($booking->eventId ?? 0);

        // Calculate VAT
        $baseAmount = (float) ($booking->totalPrice ?? 0);
        $vatRate = 0.15;
        $vatAmount = round($baseAmount * $vatRate, 2);
        $totalWithVat = $baseAmount + $vatAmount;

        // Create contract
        $contractId = DB::table('contracts')->insertGetId([
            'bookingId' => $bookingId,
            'userId' => $booking->userId ?? null,
            'type' => 'rental',
            'title' => 'عقد إيجار ' . ($unit->name ?? 'وحدة') . ' - ' . ($event->name ?? 'فعالية'),
            'totalAmount' => $totalWithVat,
            'status' => 'pending_signature',
            'terms' => $this->generateContractTerms($booking, $unit, $event, $baseAmount, $vatAmount, $totalWithVat),
            'startDate' => $event->startDate ?? null,
            'endDate' => $event->endDate ?? null,
            'createdAt' => now()->getTimestamp() * 1000,
            'updatedAt' => now()->getTimestamp() * 1000,
        ]);

        // Update booking status
        DB::table('bookings')->where('id', $bookingId)->update([
            'status' => 'contract_pending',
            'updatedAt' => now()->getTimestamp() * 1000,
        ]);

        // Create notification
        $this->createNotification(
            $booking->userId,
            'عقد جديد بانتظار التوقيع',
            "تم إنشاء عقد لحجزك #{$bookingId}. يرجى مراجعة البنود والتوقيع.",
            'contract_created',
            ['contract_id' => $contractId, 'booking_id' => $bookingId]
        );

        Log::info("ContractWorkflow: Contract {$contractId} created for booking {$bookingId}");
        return $contractId;
    }

    /**
     * Step 2: When contract is signed, generate ZATCA invoice
     */
    public function onContractSigned(int $contractId): ?int
    {
        $contract = DB::table('contracts')->find($contractId);
        if (!$contract) return null;

        // Update contract status
        DB::table('contracts')->where('id', $contractId)->update([
            'status' => 'signed',
            'signedAt' => now()->getTimestamp() * 1000,
            'updatedAt' => now()->getTimestamp() * 1000,
        ]);

        // Generate invoice
        $baseAmount = round((float) $contract->totalAmount / 1.15, 2);
        $vatAmount = round((float) $contract->totalAmount - $baseAmount, 2);

        $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $invoiceId = DB::table('invoices')->insertGetId([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'invoice_number' => $invoiceNumber,
            'user_id' => (string) ($contract->userId ?? ''),
            'invoiceable_type' => 'contract',
            'invoiceable_id' => (string) $contractId,
            'title' => 'فاتورة عقد #' . $contractId,
            'title_ar' => 'فاتورة عقد #' . $contractId,
            'subtotal' => $baseAmount,
            'tax_amount' => $vatAmount,
            'total_amount' => $contract->totalAmount,
            'status' => 'issued',
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'paid_at' => null,
            'items' => json_encode([['description' => $contract->title, 'amount' => $baseAmount, 'vat' => $vatAmount]]),
            'created_at' => now(),
            'updated_at' => now(),
        ], 'id');

        // Update booking
        if ($contract->bookingId) {
            DB::table('bookings')->where('id', $contract->bookingId)->update([
                'status' => 'pending_payment',
                'updatedAt' => now()->getTimestamp() * 1000,
            ]);
        }

        // Notify
        $this->createNotification(
            $contract->userId,
            'فاتورة جديدة',
            "تم إصدار فاتورة بمبلغ " . number_format($contract->totalAmount, 2) . " ر.س لعقدك #{$contractId}",
            'invoice_created',
            ['invoice_id' => $invoiceId, 'contract_id' => $contractId]
        );

        Log::info("ContractWorkflow: Invoice {$invoiceId} created for contract {$contractId}");
        return $invoiceId;
    }

    /**
     * Step 3: When payment received, confirm booking
     */
    public function onPaymentReceived(string $invoiceId, string $paymentMethod = 'tap', string $transactionRef = ''): bool
    {
        $invoice = DB::table('invoices')->find($invoiceId);
        if (!$invoice) return false;

        // Update invoice
        DB::table('invoices')->where('id', $invoiceId)->update([
            'status' => 'paid',
            'paid_at' => now(),
            'paid_amount' => ($invoice->total_amount ?? 0),
            'payment_method' => $paymentMethod,
            'transaction_reference' => $transactionRef,
            'updated_at' => now(),
        ]);

        // Record payment
        DB::table('payments')->insert([
            'user_id' => $invoice->user_id,
            'amount' => ($invoice->total_amount ?? 0),
            'payment_method' => $paymentMethod, 'payment_number' => 'PAY-' . date('Y') . '-' . rand(1000,9999), 'id' => (string) \Illuminate\Support\Str::uuid(),
            'status' => 'completed',
            'created_at' => now(),
        ]);

        // Update contract
        if ($invoice->invoiceable_id) {
            DB::table('contracts')->where('id', $invoice->invoiceable_id)->update([
                'status' => 'active',
                'updatedAt' => now()->getTimestamp() * 1000,
            ]);

            $contract = DB::table('contracts')->find($invoice->invoiceable_id);

            // Confirm booking
            if ($contract && $contract->bookingId) {
                DB::table('bookings')->where('id', $contract->bookingId)->update([
                    'status' => 'confirmed',
                    'paymentStatus' => 'paid',
                    'paidAmount' => ($invoice->total_amount ?? 0),
                    'updatedAt' => now()->getTimestamp() * 1000,
                ]);

                // Update unit status
                $booking = DB::table('bookings')->find($contract->bookingId);
                if ($booking && $booking->unitId) {
                    DB::table('units')->where('id', $booking->unitId)->update([
                        'status' => 'reserved',
                        'updatedAt' => now()->getTimestamp() * 1000,
                    ]);
                }
            }
        }

        // Notify
        $this->createNotification(
            $invoice->user_id,
            'تم تأكيد الدفع',
            'تم استلام المبلغ وتأكيد حجزك بنجاح. شكراً لك!',
            'payment_confirmed',
            ['invoice_id' => $invoiceId]
        );

        Log::info("ContractWorkflow: Payment received for invoice {$invoiceId}");
        return true;
    }

    /**
     * Generate contract terms in Arabic
     */
    private function generateContractTerms($booking, $unit, $event, float $base, float $vat, float $total): string
    {
        $companyName = $booking->companyName ?? 'الطرف الثاني';
        $unitName = $unit->name ?? 'الوحدة';
        $eventName = $event->name ?? 'الفعالية';
        $startDate = $event->startDate ?? '-';
        $endDate = $event->endDate ?? '-';

        return "بسم الله الرحمن الرحيم\n\n"
            . "عقد إيجار مساحة عرض\n"
            . "================================\n\n"
            . "الطرف الأول: شركة مهام إكسبو للمعارض والمؤتمرات\n"
            . "الطرف الثاني: {$companyName}\n\n"
            . "البند الأول: موضوع العقد\n"
            . "يتعهد الطرف الأول بتأجير المساحة ({$unitName}) ضمن فعالية ({$eventName}) للطرف الثاني.\n\n"
            . "البند الثاني: مدة العقد\n"
            . "من {$startDate} إلى {$endDate}\n\n"
            . "البند الثالث: المقابل المالي\n"
            . "المبلغ الأساسي: " . number_format($base, 2) . " ر.س\n"
            . "ضريبة القيمة المضافة (15%): " . number_format($vat, 2) . " ر.س\n"
            . "الإجمالي: " . number_format($total, 2) . " ر.س\n\n"
            . "البند الرابع: شروط الدفع\n"
            . "يلتزم الطرف الثاني بسداد كامل المبلغ خلال 14 يوم عمل من تاريخ توقيع العقد.\n\n"
            . "البند الخامس: الالتزامات\n"
            . "1. الالتزام بأنظمة ولوائح المعرض\n"
            . "2. عدم التنازل عن المساحة لطرف ثالث\n"
            . "3. إخلاء المساحة عند انتهاء العقد\n"
            . "4. المحافظة على نظافة وسلامة المساحة\n\n"
            . "البند السادس: الإلغاء\n"
            . "في حال إلغاء العقد قبل 30 يوم: استرداد 80%\n"
            . "في حال إلغاء العقد قبل 15 يوم: استرداد 50%\n"
            . "في حال إلغاء العقد قبل 7 أيام: لا استرداد\n";
    }

    /**
     * Create notification
     */
    private function createNotification($userId, string $title, string $body, string $type, array $data = []): void
    {
        if (!$userId) return;

        DB::table('notifications')->insert([
            'user_id' => $userId,
            'title' => $title,
            'body' => $body,
            'type' => $type,
            'channel' => 'system',
            'status' => 'unread',
            'data' => json_encode($data),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
