<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SponsorPayment;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SponsorPaymentController extends Controller
{
    use SafeOrderBy;

    /**
     * List all sponsor payments
     */
    public function index(Request $request): JsonResponse
    {
        $query = SponsorPayment::with(['contract.sponsor', 'contract.event']);

        if ($contractId = $request->input('sponsor_contract_id')) {
            $query->forContract($contractId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter overdue
        if ($request->boolean('overdue_only')) {
            $query->pending()->dueBefore(now()->toDateString());
        }

        $this->applySafeOrder($query, $request, [
            'payment_number', 'amount', 'due_date', 'paid_at', 'status', 'created_at',
        ], 'due_date', 'asc');

        $perPage = min($request->input('per_page', 15), 50);
        $payments = $query->paginate($perPage);

        return ApiResponse::paginated($payments);
    }

    /**
     * Create a payment schedule entry
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sponsor_contract_id' => ['required', 'uuid', 'exists:sponsor_contracts,id'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999999'],
            'due_date' => ['required', 'date'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $payment = SponsorPayment::create($validated);
        $payment->load('contract.sponsor');

        return ApiResponse::created($payment, __('messages.sponsor_payment.created'));
    }

    /**
     * Show a payment
     */
    public function show(SponsorPayment $sponsorPayment): JsonResponse
    {
        $sponsorPayment->load(['contract.sponsor', 'contract.event', 'contract.sponsorPackage']);

        return ApiResponse::success($sponsorPayment);
    }

    /**
     * Update a payment
     */
    public function update(Request $request, SponsorPayment $sponsorPayment): JsonResponse
    {
        $validated = $request->validate([
            'amount' => ['sometimes', 'numeric', 'min:0.01', 'max:9999999999'],
            'due_date' => ['sometimes', 'date'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $sponsorPayment->update($validated);

        return ApiResponse::success($sponsorPayment, __('messages.sponsor_payment.updated'));
    }

    /**
     * Mark a payment as paid
     */
    public function markPaid(Request $request, SponsorPayment $sponsorPayment): JsonResponse
    {
        if ($sponsorPayment->status === \App\Enums\SponsorPaymentStatus::PAID) {
            return ApiResponse::error(
                __('messages.sponsor_payment.already_paid'),
                ApiErrorCode::SPONSOR_PAYMENT_ALREADY_PAID,
                409
            );
        }

        $request->validate([
            'payment_method' => ['nullable', 'string', 'max:50'],
            'transaction_reference' => ['nullable', 'string', 'max:255'],
        ]);

        $sponsorPayment->markAsPaid(
            $request->input('payment_method'),
            $request->input('transaction_reference')
        );

        return ApiResponse::success($sponsorPayment, __('messages.sponsor_payment.marked_paid'));
    }
}
