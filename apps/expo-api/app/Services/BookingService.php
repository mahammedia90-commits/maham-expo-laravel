<?php

namespace App\Services;

use App\Models\RentalRequest;
use App\Models\VisitRequest;
use App\Models\RentalContract;
use App\Models\Invoice;
use App\Models\Space;
use App\Models\Notification;
use Illuminate\Support\Str;

/**
 * Booking Service — handles all booking business logic
 * Controllers call this service, NOT direct model operations.
 */
class BookingService
{
    public function createVisitRequest(array $data, int $userId): VisitRequest
    {
        $data['user_id'] = $userId;
        $data['status'] = 'pending';
        $visit = VisitRequest::create($data);
        
        // Notify space investor
        $space = Space::find($data['space_id']);
        if ($space && $space->investor_id) {
            Notification::create([
                'user_id' => $space->investor_id,
                'title' => 'طلب زيارة جديد',
                'title_en' => 'New Visit Request',
                'body' => "طلب زيارة جديد للمساحة {$space->name}",
                'type' => 'visit_request',
                'data' => json_encode(['visit_request_id' => $visit->id]),
            ]);
        }
        
        return $visit;
    }

    public function createRentalRequest(array $data, int $userId): RentalRequest
    {
        $data['user_id'] = $userId;
        $data['status'] = 'pending';
        $rental = RentalRequest::create($data);
        
        return $rental;
    }

    public function approveRentalRequest(int $requestId, int $approvedBy): RentalRequest
    {
        $request = RentalRequest::findOrFail($requestId);
        $request->update([
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => now(),
        ]);

        // Generate contract
        $this->generateContract($request);
        
        // Generate invoice
        $this->generateInvoice($request);
        
        // Update space status
        Space::where('id', $request->space_id)->update(['status' => 'reserved']);
        
        // Notify merchant
        Notification::create([
            'user_id' => $request->user_id,
            'title' => 'تمت الموافقة على طلب الإيجار',
            'title_en' => 'Rental Request Approved',
            'body' => 'تمت الموافقة على طلبك. يرجى مراجعة العقد والفاتورة.',
            'type' => 'rental_approved',
            'data' => json_encode(['rental_request_id' => $request->id]),
        ]);
        
        return $request->fresh();
    }

    public function rejectRentalRequest(int $requestId, string $reason = null): RentalRequest
    {
        $request = RentalRequest::findOrFail($requestId);
        $request->update(['status' => 'rejected', 'rejection_reason' => $reason]);
        
        Notification::create([
            'user_id' => $request->user_id,
            'title' => 'تم رفض طلب الإيجار',
            'title_en' => 'Rental Request Rejected',
            'body' => $reason ?? 'تم رفض طلبك.',
            'type' => 'rental_rejected',
        ]);
        
        return $request->fresh();
    }

    private function generateContract(RentalRequest $request): RentalContract
    {
        return RentalContract::create([
            'rental_request_id' => $request->id,
            'investor_id' => $request->space->investor_id ?? null,
            'merchant_id' => $request->user_id,
            'space_id' => $request->space_id,
            'contract_number' => 'CTR-' . date('Y') . '-' . str_pad(RentalContract::count() + 1, 5, '0', STR_PAD_LEFT),
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_amount' => $request->space->price_total ?? 0,
            'status' => 'pending',
        ]);
    }

    private function generateInvoice(RentalRequest $request): Invoice
    {
        $subtotal = $request->space->price_total ?? 0;
        $vatRate = 15; // Saudi VAT
        $vatAmount = $subtotal * ($vatRate / 100);
        $total = $subtotal + $vatAmount;

        return Invoice::create([
            'user_id' => $request->user_id,
            'rental_request_id' => $request->id,
            'invoice_number' => 'INV-' . date('Y') . '-' . str_pad(Invoice::count() + 1, 6, '0', STR_PAD_LEFT),
            'subtotal' => $subtotal,
            'vat_rate' => $vatRate,
            'vat_amount' => $vatAmount,
            'total_amount' => $total,
            'currency' => 'SAR',
            'status' => 'issued',
            'due_date' => now()->addDays(30),
            'zatca_uuid' => (string) Str::uuid(),
        ]);
    }
}
