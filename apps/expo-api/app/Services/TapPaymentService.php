<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TapPaymentService
{
    protected string $baseUrl;
    protected string $secretKey;
    protected string $currency;
    protected bool $threeDSecure;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('tap.base_url'), '/');
        $this->secretKey = config('tap.secret_key');
        $this->currency = config('tap.currency', 'SAR');
        $this->threeDSecure = config('tap.three_d_secure', true);
    }

    /**
     * Create a token from card details
     */
    public function createToken(array $cardData): array
    {
        try {
            $payload = [
                'card' => [
                    'number' => $cardData['card_number'],
                    'exp_month' => (int) $cardData['exp_month'],
                    'exp_year' => (int) $cardData['exp_year'],
                    'cvc' => (int) $cardData['cvc'],
                    'name' => $cardData['card_holder_name'],
                ],
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/tokens/', $payload);

            $data = $response->json();

            if ($response->successful() && isset($data['id'])) {
                return [
                    'success' => true,
                    'token_id' => $data['id'],
                    'card' => [
                        'brand' => $data['card']['brand'] ?? null,
                        'last_four' => $data['card']['last_four'] ?? null,
                        'first_six' => $data['card']['first_six'] ?? null,
                    ],
                ];
            }

            Log::error('Tap token creation failed', ['response' => $data]);

            return [
                'success' => false,
                'message' => $data['errors'][0]['description'] ?? 'فشل التحقق من بيانات البطاقة',
                'error_code' => $data['errors'][0]['code'] ?? 'UNKNOWN',
            ];
        } catch (\Exception $e) {
            Log::error('Tap token exception', ['error' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => 'خطأ في الاتصال ببوابة الدفع',
            ];
        }
    }

    /**
     * Create a charge using a token (direct card payment from backend)
     */
    public function createCharge(Payment $payment, string $tokenId, array $customer = []): array
    {
        try {
            $payload = [
                'amount' => (float) $payment->amount,
                'currency' => $payment->currency ?? $this->currency,
                'customer_initiated' => true,
                'threeDSecure' => $this->threeDSecure,
                'save_card' => false,
                'description' => "Payment {$payment->payment_number}",
                'metadata' => [
                    'payment_id' => $payment->id,
                    'payment_number' => $payment->payment_number,
                ],
                'reference' => [
                    'transaction' => $payment->payment_number,
                    'order' => $payment->payment_number,
                ],
                'receipt' => [
                    'email' => true,
                    'sms' => true,
                ],
                'customer' => [
                    'first_name' => $customer['first_name'] ?? 'Customer',
                    'last_name' => $customer['last_name'] ?? '',
                    'email' => $customer['email'] ?? '',
                    'phone' => [
                        'country_code' => $customer['phone_country_code'] ?? '966',
                        'number' => $customer['phone_number'] ?? '',
                    ],
                ],
                'source' => [
                    'id' => $tokenId,
                ],
                'redirect' => [
                    'url' => config('tap.redirect_url') . '?payment_id=' . $payment->id,
                ],
                'post' => [
                    'url' => config('tap.webhook_url'),
                ],
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/charges/', $payload);

            $data = $response->json();

            if ($response->successful() && isset($data['id'])) {
                $status = strtolower($data['status'] ?? Payment::STATUS_INITIATED);
                $transactionUrl = $data['transaction']['url'] ?? null;
                $requiresRedirect = in_array($status, ['initiated', 'pending']) && $transactionUrl;

                // Update payment with Tap charge data
                $payment->update([
                    'charge_id' => $data['id'],
                    'status' => $status,
                    'payment_method' => $data['source']['payment_method'] ?? null,
                    'source' => $data['source']['type'] ?? null,
                    'gateway_reference' => $data['reference']['gateway'] ?? null,
                    'payment_reference' => $data['reference']['payment'] ?? null,
                    'track_id' => $data['reference']['track'] ?? null,
                    'transaction_url' => $transactionUrl,
                    'tap_response' => $data,
                    'paid_at' => $status === 'captured' ? now() : null,
                ]);

                // If captured directly (no 3D Secure needed)
                if ($status === 'captured') {
                    $this->updatePayableStatus($payment);
                }

                return [
                    'success' => true,
                    'charge_id' => $data['id'],
                    'status' => strtoupper($status),
                    'requires_redirect' => $requiresRedirect,
                    'transaction_url' => $requiresRedirect ? $transactionUrl : null,
                    'receipt' => !$requiresRedirect ? [
                        'gateway_reference' => $data['reference']['gateway'] ?? null,
                        'payment_method' => $data['source']['payment_method'] ?? null,
                        'card_brand' => $data['source']['payment_type'] ?? null,
                        'card_last_four' => $data['card']['last_four'] ?? null,
                    ] : null,
                ];
            }

            Log::error('Tap charge creation failed', [
                'payment_id' => $payment->id,
                'response' => $data,
            ]);

            $payment->update([
                'status' => Payment::STATUS_FAILED,
                'error_code' => $data['errors'][0]['code'] ?? 'UNKNOWN',
                'error_message' => $data['errors'][0]['description'] ?? 'Unknown error',
                'tap_response' => $data,
            ]);

            return [
                'success' => false,
                'message' => $data['errors'][0]['description'] ?? 'فشل إنشاء عملية الدفع',
            ];
        } catch (\Exception $e) {
            Log::error('Tap charge exception', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'خطأ في الاتصال ببوابة الدفع',
            ];
        }
    }

    /**
     * Full direct payment flow: create token from card + create charge
     */
    public function processDirectPayment(Payment $payment, array $cardData, array $customer = []): array
    {
        // Step 1: Create token from card details
        $tokenResult = $this->createToken($cardData);

        if (!$tokenResult['success']) {
            $payment->update([
                'status' => Payment::STATUS_FAILED,
                'error_code' => $tokenResult['error_code'] ?? 'TOKEN_FAILED',
                'error_message' => $tokenResult['message'],
            ]);

            return $tokenResult;
        }

        // Step 2: Create charge using the token
        $chargeResult = $this->createCharge($payment, $tokenResult['token_id'], $customer);

        // Add card info to the result
        if ($chargeResult['success'] && isset($tokenResult['card'])) {
            $chargeResult['card_brand'] = $tokenResult['card']['brand'] ?? null;
            $chargeResult['card_last_four'] = $tokenResult['card']['last_four'] ?? null;
        }

        return $chargeResult;
    }

    /**
     * Retrieve a charge from Tap
     */
    public function retrieveCharge(string $chargeId): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Accept' => 'application/json',
            ])->get($this->baseUrl . '/charges/' . $chargeId);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Tap retrieve charge failed', [
                'charge_id' => $chargeId,
                'response' => $response->json(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Tap retrieve charge exception', [
                'charge_id' => $chargeId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Create a refund on Tap
     */
    public function createRefund(Payment $payment, ?float $amount = null, string $reason = ''): array
    {
        try {
            $refundAmount = $amount ?? (float) $payment->amount;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($this->baseUrl . '/refunds/', [
                'charge_id' => $payment->charge_id,
                'amount' => $refundAmount,
                'currency' => $payment->currency,
                'reason' => $reason ?: "Refund for {$payment->payment_number}",
                'metadata' => [
                    'payment_id' => $payment->id,
                    'payment_number' => $payment->payment_number,
                ],
            ]);

            $data = $response->json();

            if ($response->successful() && isset($data['id'])) {
                $payment->update([
                    'status' => Payment::STATUS_REFUNDED,
                    'refunded_at' => now(),
                ]);

                return [
                    'success' => true,
                    'refund_id' => $data['id'],
                    'status' => $data['status'] ?? 'REFUNDED',
                ];
            }

            return [
                'success' => false,
                'message' => $data['errors'][0]['description'] ?? 'فشل عملية الاسترداد',
            ];
        } catch (\Exception $e) {
            Log::error('Tap refund exception', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'خطأ في الاتصال ببوابة الدفع',
            ];
        }
    }

    /**
     * Validate the webhook hashstring from Tap
     */
    public function validateWebhookHash(array $data, string $hashstring): bool
    {
        $id = $data['id'] ?? '';
        $amount = $this->formatAmount($data['amount'] ?? 0, $data['currency'] ?? $this->currency);
        $currency = $data['currency'] ?? $this->currency;
        $gatewayReference = $data['reference']['gateway'] ?? '';
        $paymentReference = $data['reference']['payment'] ?? '';
        $status = $data['status'] ?? '';
        $created = $data['transaction']['created'] ?? '';

        $toBeHashed = 'x_id' . $id
            . 'x_amount' . $amount
            . 'x_currency' . $currency
            . 'x_gateway_reference' . $gatewayReference
            . 'x_payment_reference' . $paymentReference
            . 'x_status' . $status
            . 'x_created' . $created
            . '';

        $computed = hash_hmac('sha256', $toBeHashed, $this->secretKey);

        return hash_equals($computed, $hashstring);
    }

    /**
     * Format amount per currency ISO standard
     */
    protected function formatAmount(float $amount, string $currency): string
    {
        // 3-decimal currencies
        $threeDecimal = ['BHD', 'KWD', 'OMR', 'JOD'];

        if (in_array(strtoupper($currency), $threeDecimal)) {
            return number_format($amount, 3, '.', '');
        }

        return number_format($amount, 2, '.', '');
    }

    /**
     * Process the payment result after redirect or webhook
     */
    public function processPaymentResult(Payment $payment, array $chargeData): void
    {
        $status = strtoupper($chargeData['status'] ?? '');

        if ($status === 'CAPTURED') {
            $payment->markCaptured($chargeData);
            $this->updatePayableStatus($payment);
        } elseif (in_array($status, ['FAILED', 'DECLINED', 'RESTRICTED', 'VOID', 'TIMEDOUT'])) {
            $payment->markFailed($chargeData);
        } elseif ($status === 'CANCELLED') {
            $payment->update([
                'status' => Payment::STATUS_CANCELLED,
                'tap_response' => $chargeData,
            ]);
        } elseif ($status === 'ABANDONED') {
            $payment->update([
                'status' => Payment::STATUS_ABANDONED,
                'tap_response' => $chargeData,
            ]);
        }
        // INITIATED status — no action needed (still in progress)
    }

    /**
     * After successful payment, update the related payable (Invoice, etc.)
     */
    protected function updatePayableStatus(Payment $payment): void
    {
        $payable = $payment->payable;

        if (!$payable) {
            return;
        }

        if ($payable instanceof Invoice) {
            $payable->recordPayment(
                (float) $payment->amount,
                $payment->payment_method ?? 'tap',
                $payment->charge_id
            );
        }

        // Add more payable types here as needed
        // if ($payable instanceof RentalContract) { ... }
    }
}
