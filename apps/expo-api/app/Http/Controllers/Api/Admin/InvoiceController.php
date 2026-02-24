<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InvoiceController extends Controller
{
    use SafeOrderBy;

    public function index(Request $request): JsonResponse
    {
        $query = Invoice::query();

        if ($userId = $request->input('user_id')) {
            $query->forUser($userId);
        }

        if ($status = $request->input('status')) {
            $query->ofStatus($status);
        }

        if ($request->boolean('overdue_only')) {
            $query->overdue();
        }

        if ($search = $this->sanitizeSearch($request->input('search'))) {
            $query->search($search);
        }

        $this->applySafeOrder($query, $request, [
            'invoice_number', 'total_amount', 'status', 'issue_date', 'due_date', 'created_at',
        ], 'created_at', 'desc');

        $perPage = min($request->input('per_page', 15), 50);

        return ApiResponse::paginated($query->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'string', 'max:36'],
            'invoiceable_type' => ['required', 'string', 'in:rental_contract,sponsor_contract'],
            'invoiceable_id' => ['required', 'uuid'],
            'title' => ['required', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'tax_amount' => ['sometimes', 'numeric', 'min:0'],
            'discount_amount' => ['sometimes', 'numeric', 'min:0'],
            'total_amount' => ['required', 'numeric', 'min:0'],
            'issue_date' => ['required', 'date'],
            'due_date' => ['required', 'date', 'after_or_equal:issue_date'],
            'items' => ['nullable', 'array'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'notes_ar' => ['nullable', 'string', 'max:2000'],
        ]);

        $invoiceableType = match($validated['invoiceable_type']) {
            'rental_contract' => 'App\\Models\\RentalContract',
            'sponsor_contract' => 'App\\Models\\SponsorContract',
        };
        $validated['invoiceable_type'] = $invoiceableType;
        $validated['created_by'] = $request->input('auth_user_id');
        $validated['status'] = 'draft';

        $invoice = Invoice::create($validated);

        return ApiResponse::created($invoice, __('messages.invoice.created'));
    }

    public function show(Invoice $invoice): JsonResponse
    {
        $invoice->load('invoiceable');
        return ApiResponse::success($invoice);
    }

    public function update(Request $request, Invoice $invoice): JsonResponse
    {
        if (!in_array($invoice->status->value, ['draft', 'issued'])) {
            return ApiResponse::error(
                __('messages.invoice.cannot_be_modified'),
                ApiErrorCode::VALIDATION_FAILED,
                403
            );
        }

        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'subtotal' => ['sometimes', 'numeric', 'min:0'],
            'tax_amount' => ['sometimes', 'numeric', 'min:0'],
            'discount_amount' => ['sometimes', 'numeric', 'min:0'],
            'total_amount' => ['sometimes', 'numeric', 'min:0'],
            'due_date' => ['sometimes', 'date'],
            'items' => ['nullable', 'array'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'notes_ar' => ['nullable', 'string', 'max:2000'],
        ]);

        $invoice->update($validated);

        return ApiResponse::success($invoice, __('messages.invoice.updated'));
    }

    public function issue(Invoice $invoice): JsonResponse
    {
        $invoice->issue();
        return ApiResponse::success($invoice, __('messages.invoice.issued'));
    }

    public function markPaid(Request $request, Invoice $invoice): JsonResponse
    {
        $request->validate([
            'payment_method' => ['required', 'string', 'max:50'],
            'transaction_reference' => ['nullable', 'string', 'max:255'],
        ]);

        $invoice->markAsPaid(
            $request->input('payment_method'),
            $request->input('transaction_reference')
        );

        return ApiResponse::success($invoice, __('messages.invoice.marked_paid'));
    }

    public function cancel(Invoice $invoice): JsonResponse
    {
        $invoice->cancel();
        return ApiResponse::success($invoice, __('messages.invoice.cancelled'));
    }
}
