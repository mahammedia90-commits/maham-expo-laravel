<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractParty;
use App\Models\ContractSignature;
use App\Enums\UnifiedContractStatus;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ContractSigningController extends Controller
{
    /**
     * Get contract for signing via token
     * Validates the token and returns contract + party info
     */
    public function getContractForSigning(string $token): JsonResponse
    {
        try {
            $party = ContractParty::where('signing_token', $token)
                ->where('signing_token_expires_at', '>', now())
                ->where('is_signer', true)
                ->first();

            if (!$party) {
                return ApiResponse::error(
                    __('messages.contract.invalid_or_expired_token'),
                    ApiErrorCode::VALIDATION_FAILED,
                    404
                );
            }

            $contract = $party->contract()->with(['event', 'parties'])->first();

            if (!$contract || $contract->status !== UnifiedContractStatus::SENT_FOR_SIGNATURE) {
                return ApiResponse::error(
                    __('messages.contract.not_available_for_signing'),
                    ApiErrorCode::VALIDATION_FAILED,
                    422
                );
            }

            return ApiResponse::success([
                'contract' => [
                    'id' => $contract->id,
                    'contract_number' => $contract->contract_number,
                    'title' => $contract->title,
                    'title_ar' => $contract->title_ar,
                    'type' => $contract->type,
                    'content_html' => $contract->content_html,
                    'content_html_ar' => $contract->content_html_ar,
                    'terms_and_conditions' => $contract->terms_and_conditions,
                    'special_conditions' => $contract->special_conditions,
                    'total_amount' => $contract->total_amount,
                    'currency' => $contract->currency,
                    'start_date' => $contract->start_date,
                    'end_date' => $contract->end_date,
                    'event' => $contract->event,
                ],
                'party' => [
                    'id' => $party->id,
                    'party_type' => $party->party_type,
                    'party_role' => $party->party_role,
                    'company_name' => $party->company_name,
                    'company_name_ar' => $party->company_name_ar,
                    'contact_name' => $party->contact_name,
                    'contact_name_ar' => $party->contact_name_ar,
                    'email' => $party->email,
                    'has_signed' => $party->has_signed,
                ],
                'other_parties' => $contract->parties->map(fn($p) => [
                    'party_role' => $p->party_role,
                    'company_name' => $p->company_name,
                    'company_name_ar' => $p->company_name_ar,
                    'has_signed' => $p->has_signed,
                ]),
                'requires_otp' => true,
            ]);
        } catch (\Throwable $e) {
            Log::error('ContractSigning getContractForSigning error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Verify OTP before signing
     */
    public function verifyOtp(Request $request, string $token): JsonResponse
    {
        try {
            $party = ContractParty::where('signing_token', $token)
                ->where('signing_token_expires_at', '>', now())
                ->where('is_signer', true)
                ->first();

            if (!$party) {
                return ApiResponse::error(
                    __('messages.contract.invalid_or_expired_token'),
                    ApiErrorCode::VALIDATION_FAILED,
                    404
                );
            }

            $request->validate([
                'otp' => ['required', 'string', 'size:6'],
            ]);

            $cacheKey = "contract_signing_otp:{$party->id}";
            $storedOtp = Cache::get($cacheKey);

            if (!$storedOtp || $storedOtp !== $request->input('otp')) {
                return ApiResponse::error(
                    __('messages.contract.invalid_otp'),
                    ApiErrorCode::VALIDATION_FAILED,
                    422
                );
            }

            // Mark OTP as verified
            Cache::put("contract_signing_verified:{$party->id}", true, now()->addMinutes(15));
            Cache::forget($cacheKey);

            return ApiResponse::success([
                'verified' => true,
                'expires_in_minutes' => 15,
            ], __('messages.contract.otp_verified'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());
        } catch (\Throwable $e) {
            Log::error('ContractSigning verifyOtp error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Process signature
     */
    public function sign(Request $request, string $token): JsonResponse
    {
        try {
            $party = ContractParty::where('signing_token', $token)
                ->where('signing_token_expires_at', '>', now())
                ->where('is_signer', true)
                ->first();

            if (!$party) {
                return ApiResponse::error(
                    __('messages.contract.invalid_or_expired_token'),
                    ApiErrorCode::VALIDATION_FAILED,
                    404
                );
            }

            if ($party->has_signed) {
                return ApiResponse::error(
                    __('messages.contract.already_signed'),
                    ApiErrorCode::VALIDATION_FAILED,
                    422
                );
            }

            $contract = $party->contract;
            if (!$contract || $contract->status !== UnifiedContractStatus::SENT_FOR_SIGNATURE) {
                return ApiResponse::error(
                    __('messages.contract.not_available_for_signing'),
                    ApiErrorCode::VALIDATION_FAILED,
                    422
                );
            }

            // Verify OTP was completed
            $isVerified = Cache::get("contract_signing_verified:{$party->id}");
            if (!$isVerified) {
                return ApiResponse::error(
                    __('messages.contract.otp_not_verified'),
                    ApiErrorCode::VALIDATION_FAILED,
                    422
                );
            }

            $request->validate([
                'signature_data' => ['required', 'string'],
                'signature_type' => ['required', 'string', 'in:draw,type,upload'],
            ]);

            DB::transaction(function () use ($contract, $party, $request) {
                // Record signature on party
                $party->update([
                    'has_signed' => true,
                    'signed_at' => now(),
                    'signature_data' => $request->input('signature_data'),
                    'signature_ip' => $request->ip(),
                    'signature_device' => $request->userAgent(),
                ]);

                // Create signature record
                ContractSignature::create([
                    'contract_id' => $contract->id,
                    'party_id' => $party->id,
                    'signature_type' => $request->input('signature_type'),
                    'signature_data' => $request->input('signature_data'),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'verification_method' => 'otp',
                ]);

                // Invalidate signing token
                $party->update([
                    'signing_token' => null,
                    'signing_token_expires_at' => null,
                ]);

                // Clear OTP verification
                Cache::forget("contract_signing_verified:{$party->id}");

                // Check if all signers have signed
                $allSigned = $contract->parties()
                    ->where('is_signer', true)
                    ->where('has_signed', false)
                    ->doesntExist();

                if ($allSigned) {
                    $contract->update([
                        'is_fully_signed' => true,
                        'signed_at' => now(),
                        'signature_method' => 'electronic',
                        'status' => UnifiedContractStatus::SIGNED->value,
                    ]);

                    // Log status change
                    $contract->statusLogs()->create([
                        'from_status' => UnifiedContractStatus::SENT_FOR_SIGNATURE->value,
                        'to_status' => UnifiedContractStatus::SIGNED->value,
                        'performed_by' => $party->user_id,
                        'description' => 'All parties have signed',
                        'ip_address' => $request->ip(),
                    ]);
                }
            });

            return ApiResponse::success([
                'signed' => true,
                'party_id' => $party->id,
                'all_signed' => $contract->fresh()->is_fully_signed,
            ], __('messages.contract.signed'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());
        } catch (\Throwable $e) {
            Log::error('ContractSigning sign error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }
}
