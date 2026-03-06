<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\TapPaymentService;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentGatewayController extends Controller
{
    use SafeOrderBy;

    public function __construct(
        protected TapPaymentService $tapService
    ) {}

    /**
     * List user's payments
     */
    public function index(Request $request): JsonResponse
    {
        $query = Payment::forUser($request->input('auth_user_id'));

        if ($status = $request->input('status')) {
            $query->ofStatus($status);
        }

        $this->applySafeOrder($query, $request, [
            'payment_number', 'amount', 'status', 'created_at', 'paid_at',
        ], 'created_at', 'desc');

        $perPage = min($request->input('per_page', 15), 50);

        return ApiResponse::paginated($query->paginate($perPage));
    }

    /**
     * Show payment details
     */
    public function show(Request $request, Payment $payment): JsonResponse
    {
        if ($payment->user_id !== $request->input('auth_user_id')) {
            return ApiResponse::forbidden(__('messages.forbidden'));
        }

        $payment->load('payable');

        return ApiResponse::success($payment);
    }

    /**
     * Initiate payment for an invoice
     */
    public function payInvoice(Request $request): JsonResponse
    {
        $request->validate([
            'invoice_id' => ['required', 'uuid'],
            'amount' => ['nullable', 'numeric', 'min:0.01'],
            'first_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone_country_code' => ['nullable', 'string', 'max:5'],
            'phone_number' => ['nullable', 'string', 'max:20'],
        ]);

        $userId = $request->input('auth_user_id');
        $invoice = Invoice::find($request->invoice_id);

        if (!$invoice) {
            return ApiResponse::notFound(__('messages.resource_not_found'));
        }

        // Only allow payment for own invoices
        if ($invoice->user_id !== $userId) {
            return ApiResponse::forbidden(__('messages.forbidden'));
        }

        // Check invoice is payable
        if (in_array($invoice->status->value, ['paid', 'cancelled', 'refunded'])) {
            return ApiResponse::error(
                'هذه الفاتورة لا يمكن دفعها',
                ApiErrorCode::VALIDATION_FAILED,
                422
            );
        }

        $amount = $request->input('amount', $invoice->remaining_amount);

        if ($amount > $invoice->remaining_amount) {
            return ApiResponse::error(
                'المبلغ أكبر من المبلغ المتبقي',
                ApiErrorCode::VALIDATION_FAILED,
                422
            );
        }

        // Create payment record
        $payment = Payment::create([
            'user_id' => $userId,
            'payable_type' => Invoice::class,
            'payable_id' => $invoice->id,
            'amount' => $amount,
            'currency' => config('tap.currency', 'SAR'),
            'status' => Payment::STATUS_INITIATED,
            'three_d_secure' => config('tap.three_d_secure', true),
        ]);

        // Create Tap charge
        $result = $this->tapService->createCharge($payment, [
            'first_name' => $request->input('first_name', 'Customer'),
            'last_name' => $request->input('last_name', ''),
            'email' => $request->input('email', ''),
            'phone_country_code' => $request->input('phone_country_code', '966'),
            'phone_number' => $request->input('phone_number', ''),
        ]);

        if (!$result['success']) {
            return ApiResponse::error(
                $result['message'] ?? 'فشل إنشاء عملية الدفع',
                ApiErrorCode::INTERNAL_SERVER_ERROR,
                500
            );
        }

        return ApiResponse::success([
            'payment_id' => $payment->id,
            'payment_number' => $payment->payment_number,
            'charge_id' => $result['charge_id'],
            'transaction_url' => $result['transaction_url'],
            'amount' => $payment->amount,
            'currency' => $payment->currency,
        ], 'تم إنشاء عملية الدفع بنجاح');
    }

    /**
     * Check payment status (after redirect)
     */
    public function checkStatus(Request $request, Payment $payment): JsonResponse
    {
        if ($payment->user_id !== $request->input('auth_user_id')) {
            return ApiResponse::forbidden(__('messages.forbidden'));
        }

        // If payment is already final, return current status
        if (in_array($payment->status, [Payment::STATUS_CAPTURED, Payment::STATUS_FAILED, Payment::STATUS_CANCELLED])) {
            return ApiResponse::success([
                'payment_id' => $payment->id,
                'payment_number' => $payment->payment_number,
                'status' => $payment->status,
                'amount' => $payment->amount,
                'paid_at' => $payment->paid_at,
            ]);
        }

        // Retrieve latest status from Tap
        if ($payment->charge_id) {
            $chargeData = $this->tapService->retrieveCharge($payment->charge_id);

            if ($chargeData) {
                $this->tapService->processPaymentResult($payment, $chargeData);
                $payment->refresh();
            }
        }

        return ApiResponse::success([
            'payment_id' => $payment->id,
            'payment_number' => $payment->payment_number,
            'status' => $payment->status,
            'amount' => $payment->amount,
            'paid_at' => $payment->paid_at,
        ]);
    }
}
