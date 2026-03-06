<?php

namespace App\Http\Controllers\Api\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\TapPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TapWebhookController extends Controller
{
    public function __construct(
        protected TapPaymentService $tapService
    ) {}

    /**
     * Handle Tap payment webhook (POST from Tap servers)
     *
     * This endpoint receives server-to-server callbacks from Tap
     * when a payment status changes (CAPTURED, FAILED, etc.)
     */
    public function handle(Request $request): JsonResponse
    {
        $data = $request->all();

        Log::info('Tap webhook received', [
            'charge_id' => $data['id'] ?? 'unknown',
            'status' => $data['status'] ?? 'unknown',
        ]);

        // Validate hashstring
        $hashstring = $request->header('hashstring', '');

        if ($hashstring && !$this->tapService->validateWebhookHash($data, $hashstring)) {
            Log::warning('Tap webhook hash validation failed', [
                'charge_id' => $data['id'] ?? 'unknown',
            ]);

            return response()->json(['status' => 'invalid_hash'], 403);
        }

        // Find payment by charge_id
        $chargeId = $data['id'] ?? null;

        if (!$chargeId) {
            Log::warning('Tap webhook missing charge_id');
            return response()->json(['status' => 'missing_charge_id'], 400);
        }

        $payment = Payment::where('charge_id', $chargeId)->first();

        if (!$payment) {
            // Try finding by metadata
            $paymentId = $data['metadata']['payment_id'] ?? null;
            if ($paymentId) {
                $payment = Payment::find($paymentId);
            }
        }

        if (!$payment) {
            Log::warning('Tap webhook: payment not found', [
                'charge_id' => $chargeId,
            ]);

            return response()->json(['status' => 'payment_not_found'], 404);
        }

        // Skip if already processed
        if ($payment->isCaptured()) {
            Log::info('Tap webhook: payment already captured', [
                'payment_id' => $payment->id,
            ]);

            return response()->json(['status' => 'already_processed']);
        }

        // Process the payment result
        $this->tapService->processPaymentResult($payment, $data);

        // Update webhook status
        $payment->update([
            'webhook_status' => $data['status'] ?? 'unknown',
        ]);

        Log::info('Tap webhook processed', [
            'payment_id' => $payment->id,
            'status' => $data['status'] ?? 'unknown',
        ]);

        return response()->json(['status' => 'success']);
    }
}
