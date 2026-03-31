<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\ContractWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractWorkflowController extends Controller
{
    protected ContractWorkflowService $workflow;

    public function __construct(ContractWorkflowService $workflow)
    {
        $this->workflow = $workflow;
    }

    /**
     * Approve booking → auto-create contract
     */
    public function approveBooking(Request $request, $bookingId)
    {
        // Update booking status
        DB::table('bookings')->where('id', $bookingId)->update([
            'status' => 'approved',
            'updatedAt' => now()->getTimestamp() * 1000,
        ]);

        // Auto-create contract
        $contractId = $this->workflow->onBookingApproved((int) $bookingId);

        return response()->json([
            'success' => true,
            'message' => 'تمت الموافقة على الحجز وإنشاء العقد',
            'data' => [
                'booking_id' => (int) $bookingId,
                'contract_id' => $contractId,
                'next_step' => 'توقيع العقد من قبل التاجر',
            ],
        ]);
    }

    /**
     * Sign contract → auto-generate invoice
     */
    public function signContract(Request $request, $contractId)
    {
        $request->validate([
            'signature_data' => 'nullable|string',
        ]);

        // Generate invoice
        $invoiceId = $this->workflow->onContractSigned((int) $contractId);

        return response()->json([
            'success' => true,
            'message' => 'تم توقيع العقد وإصدار الفاتورة',
            'data' => [
                'contract_id' => (int) $contractId,
                'invoice_id' => $invoiceId,
                'next_step' => 'سداد الفاتورة',
            ],
        ]);
    }

    /**
     * Record payment → confirm booking
     */
    public function recordPayment(Request $request, string $invoiceId)
    {
        $request->validate([
            'payment_method' => 'nullable|string',
            'transaction_ref' => 'nullable|string',
        ]);

        $success = $this->workflow->onPaymentReceived(
            $invoiceId,
            $request->payment_method ?? 'manual',
            $request->transaction_ref ?? ''
        );

        return response()->json([
            'success' => $success,
            'message' => $success ? 'تم تأكيد الدفع والحجز بنجاح' : 'فشل في تسجيل الدفع',
            'data' => [
                'invoice_id' => $invoiceId,
                'status' => $success ? 'confirmed' : 'failed',
                'next_step' => $success ? 'الحجز مؤكد - جاهز للفعالية' : 'يرجى المحاولة مرة أخرى',
            ],
        ]);
    }

    /**
     * Get complete workflow status for a booking
     */
    public function workflowStatus($bookingId)
    {
        $booking = DB::table('bookings')->find($bookingId);
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'الحجز غير موجود'], 404);
        }

        $contract = DB::table('contracts')->where('bookingId', $bookingId)->first();
        $invoice = $contract ? DB::table('invoices')->where('invoiceable_id', (string) $contract->id)->first() : null;
        $payment = $invoice ? DB::table('payments')->where('user_id', $booking->userId)->orderBy('created_at', 'desc')->first() : null;

        $steps = [
            ['step' => 1, 'name' => 'طلب الحجز', 'nameEn' => 'Booking Request', 'status' => 'completed', 'date' => $booking->createdAt],
            ['step' => 2, 'name' => 'الموافقة', 'nameEn' => 'Approval', 'status' => in_array($booking->status, ['approved','contract_pending','pending_payment','confirmed']) ? 'completed' : 'pending'],
            ['step' => 3, 'name' => 'إنشاء العقد', 'nameEn' => 'Contract Created', 'status' => $contract ? 'completed' : 'pending', 'contract_id' => ($contract ? $contract->id : null)],
            ['step' => 4, 'name' => 'التوقيع', 'nameEn' => 'Signing', 'status' => ($contract && in_array($contract->status, ['signed','active'])) ? 'completed' : 'pending'],
            ['step' => 5, 'name' => 'إصدار الفاتورة', 'nameEn' => 'Invoice Issued', 'status' => $invoice ? 'completed' : 'pending', 'invoice_id' => ($invoice ? $invoice->id : null)],
            ['step' => 6, 'name' => 'الدفع', 'nameEn' => 'Payment', 'status' => ($invoice && $invoice->status === 'paid') ? 'completed' : 'pending'],
            ['step' => 7, 'name' => 'تأكيد الحجز', 'nameEn' => 'Booking Confirmed', 'status' => $booking->status === 'confirmed' ? 'completed' : 'pending'],
        ];

        $currentStep = 1;
        foreach ($steps as $s) {
            if ($s['status'] === 'completed') $currentStep = $s['step'] + 1;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'booking' => $booking,
                'contract' => $contract,
                'invoice' => $invoice,
                'steps' => $steps,
                'current_step' => min($currentStep, 7),
                'is_complete' => $booking->status === 'confirmed',
            ],
        ]);
    }

    /**
     * Get all contracts with their workflow status
     */
    public function listWithWorkflow(Request $request)
    {
        $contracts = DB::table('contracts')
            ->leftJoin('bookings', 'contracts.bookingId', '=', 'bookings.id')
            ->leftJoin('events', 'bookings.eventId', '=', 'events.id')
            ->leftJoin('units', 'bookings.unitId', '=', 'units.id')
            ->select(
                'contracts.*',
                'bookings.companyName',
                'bookings.contactPerson',
                'bookings.contactPhone',
                'bookings.status as bookingStatus',
                'events.name as eventName',
                'events.nameAr as eventNameAr',
                'units.name as unitName',
                'units.code as unitCode'
            )
            ->orderBy('contracts.createdAt', 'desc')
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $contracts]);
    }
}
