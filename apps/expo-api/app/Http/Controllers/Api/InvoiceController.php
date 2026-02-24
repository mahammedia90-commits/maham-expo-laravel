<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    use SafeOrderBy;

    /**
     * List user's own invoices
     */
    public function index(Request $request): JsonResponse
    {
        $query = Invoice::forUser($request->input('auth_user_id'));

        if ($status = $request->input('status')) {
            $query->ofStatus($status);
        }

        $this->applySafeOrder($query, $request, [
            'invoice_number', 'total_amount', 'status', 'due_date', 'created_at',
        ], 'created_at', 'desc');

        $perPage = min($request->input('per_page', 15), 50);

        return ApiResponse::paginated($query->paginate($perPage));
    }

    /**
     * Show own invoice details
     */
    public function show(Request $request, Invoice $invoice): JsonResponse
    {
        if ($invoice->user_id !== $request->input('auth_user_id')) {
            return ApiResponse::forbidden(__('messages.forbidden'));
        }

        $invoice->load('invoiceable');

        return ApiResponse::success($invoice);
    }
}
