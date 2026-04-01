<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractAttachment;
use App\Models\ContractParty;
use App\Models\ContractPaymentPlan;
use App\Enums\ContractType;
use App\Enums\UnifiedContractStatus;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UnifiedContractController extends Controller
{
    use SafeOrderBy;

    // ──────────────────────────────────────────
    // CRUD
    // ──────────────────────────────────────────

    /**
     * List contracts with filters and pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Contract::with(['event', 'parties']);

            if ($type = $request->input('type')) {
                $query->byType($type);
            }

            if ($status = $request->input('status')) {
                $query->where('status', $status);
            }

            if ($paymentStatus = $request->input('payment_status')) {
                $query->where('payment_status', $paymentStatus);
            }

            if ($eventId = $request->input('event_id')) {
                $query->where('event_id', $eventId);
            }

            if ($search = $this->sanitizeSearch($request->input('search'))) {
                $query->where(function ($q) use ($search) {
                    $q->where('contract_number', 'like', "%{$search}%")
                      ->orWhere('title', 'like', "%{$search}%")
                      ->orWhere('title_ar', 'like', "%{$search}%");
                });
            }

            $this->applySafeOrder($query, $request, [
                'contract_number', 'title', 'type', 'status', 'total_amount',
                'start_date', 'end_date', 'created_at', 'updated_at',
            ], 'created_at', 'desc');

            $perPage = min($request->input('per_page', 15), 50);

            return ApiResponse::paginated($query->paginate($perPage));
        } catch (\Throwable $e) {
            Log::error('Contract index error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Create a new contract
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'type' => ['required', Rule::enum(ContractType::class)],
                'sub_type' => ['nullable', 'string', 'max:100'],
                'category' => ['nullable', 'string', 'max:100'],
                'title' => ['required', 'string', 'max:500'],
                'title_ar' => ['nullable', 'string', 'max:500'],
                'description' => ['nullable', 'string', 'max:5000'],
                'description_ar' => ['nullable', 'string', 'max:5000'],
                'content_html' => ['nullable', 'string'],
                'content_html_ar' => ['nullable', 'string'],
                'terms_and_conditions' => ['nullable', 'array'],
                'special_conditions' => ['nullable', 'array'],
                'event_id' => ['nullable', 'uuid', 'exists:events,id'],
                'space_id' => ['nullable', 'uuid'],
                'section_id' => ['nullable', 'uuid'],
                'sponsor_package_id' => ['nullable', 'uuid'],
                'template_id' => ['nullable', 'uuid'],
                'currency' => ['sometimes', 'string', 'max:3'],
                'subtotal' => ['nullable', 'numeric', 'min:0'],
                'discount_amount' => ['nullable', 'numeric', 'min:0'],
                'discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
                'vat_rate' => ['nullable', 'numeric', 'min:0'],
                'total_amount' => ['required', 'numeric', 'min:0'],
                'payment_method' => ['nullable', 'string', 'max:50'],
                'installments_count' => ['nullable', 'integer', 'min:1'],
                'payment_terms_days' => ['nullable', 'integer', 'min:0'],
                'start_date' => ['required', 'date'],
                'end_date' => ['required', 'date', 'after_or_equal:start_date'],
                'signing_deadline' => ['nullable', 'date'],
                'is_renewable' => ['nullable', 'boolean'],
                'renewal_reminder_days' => ['nullable', 'integer', 'min:1'],
                'auto_renew' => ['nullable', 'boolean'],
                'internal_notes' => ['nullable', 'string', 'max:2000'],
                'admin_notes' => ['nullable', 'string', 'max:2000'],
                'metadata' => ['nullable', 'array'],
                // Parties inline
                'parties' => ['nullable', 'array'],
                'parties.*.party_type' => ['required_with:parties', 'string'],
                'parties.*.party_role' => ['required_with:parties', 'string'],
                'parties.*.user_id' => ['nullable', 'uuid'],
                'parties.*.company_name' => ['nullable', 'string', 'max:255'],
                'parties.*.company_name_ar' => ['nullable', 'string', 'max:255'],
                'parties.*.contact_name' => ['nullable', 'string', 'max:255'],
                'parties.*.contact_name_ar' => ['nullable', 'string', 'max:255'],
                'parties.*.email' => ['nullable', 'email'],
                'parties.*.phone' => ['nullable', 'string', 'max:20'],
                'parties.*.is_signer' => ['nullable', 'boolean'],
                'parties.*.signing_order' => ['nullable', 'integer'],
            ]);

            $userId = $request->input('auth_user_id');
            $validated['created_by'] = $userId;
            $validated['status'] = UnifiedContractStatus::DRAFT->value;
            $validated['payment_status'] = 'unpaid';

            // Calculate VAT if not provided
            if (isset($validated['vat_rate']) && !isset($validated['vat_amount'])) {
                $taxable = $validated['subtotal'] ?? $validated['total_amount'];
                $validated['vat_amount'] = round($taxable * ($validated['vat_rate'] / 100), 2);
                $validated['taxable_amount'] = $taxable;
            }

            $parties = $validated['parties'] ?? [];
            unset($validated['parties']);

            $contract = DB::transaction(function () use ($validated, $parties) {
                $contract = Contract::create($validated);

                foreach ($parties as $party) {
                    $party['contract_id'] = $contract->id;
                    ContractParty::create($party);
                }

                return $contract;
            });

            $contract->load(['event', 'parties']);

            return ApiResponse::created($contract, __('messages.contract.created'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());
        } catch (\Throwable $e) {
            Log::error('Contract store error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Show contract details
     */
    public function show(string $id): JsonResponse
    {
        try {
            $contract = Contract::with([
                'parties',
                'paymentPlan' => fn($q) => $q->orderBy('due_date'),
                'versions' => fn($q) => $q->orderByDesc('version_number'),
                'attachments',
                'statusLogs' => fn($q) => $q->orderByDesc('created_at'),
                'event',
            ])->findOrFail($id);

            return ApiResponse::success($contract);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Throwable $e) {
            Log::error('Contract show error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Update contract (draft only)
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);

            if (!$contract->status->canBeModified()) {
                return ApiResponse::error(
                    __('messages.contract.cannot_be_modified'),
                    ApiErrorCode::VALIDATION_FAILED,
                    403
                );
            }

            $validated = $request->validate([
                'type' => ['sometimes', Rule::enum(ContractType::class)],
                'sub_type' => ['nullable', 'string', 'max:100'],
                'category' => ['nullable', 'string', 'max:100'],
                'title' => ['sometimes', 'string', 'max:500'],
                'title_ar' => ['nullable', 'string', 'max:500'],
                'description' => ['nullable', 'string', 'max:5000'],
                'description_ar' => ['nullable', 'string', 'max:5000'],
                'content_html' => ['nullable', 'string'],
                'content_html_ar' => ['nullable', 'string'],
                'terms_and_conditions' => ['nullable', 'array'],
                'special_conditions' => ['nullable', 'array'],
                'event_id' => ['nullable', 'uuid', 'exists:events,id'],
                'space_id' => ['nullable', 'uuid'],
                'section_id' => ['nullable', 'uuid'],
                'sponsor_package_id' => ['nullable', 'uuid'],
                'template_id' => ['nullable', 'uuid'],
                'currency' => ['sometimes', 'string', 'max:3'],
                'subtotal' => ['nullable', 'numeric', 'min:0'],
                'discount_amount' => ['nullable', 'numeric', 'min:0'],
                'discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
                'vat_rate' => ['nullable', 'numeric', 'min:0'],
                'total_amount' => ['sometimes', 'numeric', 'min:0'],
                'payment_method' => ['nullable', 'string', 'max:50'],
                'installments_count' => ['nullable', 'integer', 'min:1'],
                'payment_terms_days' => ['nullable', 'integer', 'min:0'],
                'start_date' => ['sometimes', 'date'],
                'end_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
                'signing_deadline' => ['nullable', 'date'],
                'is_renewable' => ['nullable', 'boolean'],
                'renewal_reminder_days' => ['nullable', 'integer', 'min:1'],
                'auto_renew' => ['nullable', 'boolean'],
                'internal_notes' => ['nullable', 'string', 'max:2000'],
                'admin_notes' => ['nullable', 'string', 'max:2000'],
                'metadata' => ['nullable', 'array'],
            ]);

            $validated['updated_by'] = $request->input('auth_user_id');

            $contract->update($validated);
            $contract->load(['event', 'parties']);

            return ApiResponse::success($contract, __('messages.contract.updated'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());
        } catch (\Throwable $e) {
            Log::error('Contract update error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Soft delete contract (draft only)
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);

            if ($contract->status !== UnifiedContractStatus::DRAFT) {
                return ApiResponse::error(
                    __('messages.contract.only_draft_can_be_deleted'),
                    ApiErrorCode::VALIDATION_FAILED,
                    403
                );
            }

            $contract->delete();

            return ApiResponse::success(null, __('messages.contract.deleted'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Throwable $e) {
            Log::error('Contract destroy error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    // ──────────────────────────────────────────
    // Lifecycle Actions
    // ──────────────────────────────────────────

    /**
     * Submit contract for review
     */
    public function submitForReview(Request $request, string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);

            if ($contract->status !== UnifiedContractStatus::DRAFT) {
                return ApiResponse::error(
                    __('messages.contract.invalid_status_transition'),
                    ApiErrorCode::VALIDATION_FAILED,
                    422
                );
            }

            $this->transitionStatus($contract, UnifiedContractStatus::UNDER_REVIEW, $request->input('auth_user_id'));

            return ApiResponse::success($contract->fresh(), __('messages.contract.submitted_for_review'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Throwable $e) {
            Log::error('Contract submitForReview error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Legal approval
     */
    public function approveLegal(Request $request, string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);

            if (!$contract->status->canBeReviewed()) {
                return ApiResponse::error(
                    __('messages.contract.invalid_status_transition'),
                    ApiErrorCode::VALIDATION_FAILED,
                    422
                );
            }

            $request->validate([
                'notes' => ['nullable', 'string', 'max:2000'],
            ]);

            $userId = $request->input('auth_user_id');

            $contract->update([
                'legal_approved' => true,
                'legal_approved_by' => $userId,
                'legal_approved_at' => now(),
                'legal_notes' => $request->input('notes'),
            ]);

            $this->logStatusChange($contract, 'legal_approved', $userId, $request->input('notes'));

            // Auto-approve if both legal and finance approved
            $this->checkAndApproveIfReady($contract, $userId);

            return ApiResponse::success($contract->fresh(), __('messages.contract.legal_approved'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Throwable $e) {
            Log::error('Contract approveLegal error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Finance approval
     */
    public function approveFinance(Request $request, string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);

            if (!$contract->status->canBeReviewed()) {
                return ApiResponse::error(
                    __('messages.contract.invalid_status_transition'),
                    ApiErrorCode::VALIDATION_FAILED,
                    422
                );
            }

            $request->validate([
                'notes' => ['nullable', 'string', 'max:2000'],
            ]);

            $userId = $request->input('auth_user_id');

            $contract->update([
                'finance_approved' => true,
                'finance_approved_by' => $userId,
                'finance_approved_at' => now(),
                'finance_notes' => $request->input('notes'),
            ]);

            $this->logStatusChange($contract, 'finance_approved', $userId, $request->input('notes'));

            // Auto-approve if both legal and finance approved
            $this->checkAndApproveIfReady($contract, $userId);

            return ApiResponse::success($contract->fresh(), __('messages.contract.finance_approved'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Throwable $e) {
            Log::error('Contract approveFinance error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Final approval
     */
    public function approveFinal(Request $request, string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);

            if (!$contract->status->canBeReviewed()) {
                return ApiResponse::error(
                    __('messages.contract.invalid_status_transition'),
                    ApiErrorCode::VALIDATION_FAILED,
                    422
                );
            }

            $userId = $request->input('auth_user_id');

            $contract->update([
                'final_approved_by' => $userId,
                'final_approved_at' => now(),
            ]);

            $this->transitionStatus($contract, UnifiedContractStatus::APPROVED, $userId, 'Final approval granted');

            return ApiResponse::success($contract->fresh(), __('messages.contract.approved'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Throwable $e) {
            Log::error('Contract approveFinal error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Reject contract
     */
    public function reject(Request $request, string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);

            if (!$contract->status->canBeReviewed()) {
                return ApiResponse::error(
                    __('messages.contract.invalid_status_transition'),
                    ApiErrorCode::VALIDATION_FAILED,
                    422
                );
            }

            $request->validate([
                'rejection_reason' => ['required', 'string', 'max:2000'],
            ]);

            $userId = $request->input('auth_user_id');

            $contract->update([
                'rejection_reason' => $request->input('rejection_reason'),
                'rejected_by' => $userId,
                'rejected_at' => now(),
            ]);

            $this->transitionStatus($contract, UnifiedContractStatus::REJECTED, $userId, $request->input('rejection_reason'));

            return ApiResponse::success($contract->fresh(), __('messages.contract.rejected'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());
        } catch (\Throwable $e) {
            Log::error('Contract reject error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Request changes on contract — revert to draft
     */
    public function requestChanges(Request $request, string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);

            if (!$contract->status->canBeReviewed()) {
                return ApiResponse::error(
                    __('messages.contract.invalid_status_transition'),
                    ApiErrorCode::VALIDATION_FAILED,
                    422
                );
            }

            $request->validate([
                'notes' => ['required', 'string', 'max:2000'],
            ]);

            $userId = $request->input('auth_user_id');

            // Reset approvals
            $contract->update([
                'legal_approved' => false,
                'legal_approved_by' => null,
                'legal_approved_at' => null,
                'finance_approved' => false,
                'finance_approved_by' => null,
                'finance_approved_at' => null,
                'final_approved_by' => null,
                'final_approved_at' => null,
            ]);

            $this->transitionStatus($contract, UnifiedContractStatus::DRAFT, $userId, $request->input('notes'));

            return ApiResponse::success($contract->fresh(), __('messages.contract.changes_requested'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());
        } catch (\Throwable $e) {
            Log::error('Contract requestChanges error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Send contract for signature
     */
    public function sendForSignature(Request $request, string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);

            if (!$contract->status->canBeSentForSignature()) {
                return ApiResponse::error(
                    __('messages.contract.invalid_status_transition'),
                    ApiErrorCode::VALIDATION_FAILED,
                    422
                );
            }

            $userId = $request->input('auth_user_id');

            // Generate signing tokens for each signer party
            $signerParties = $contract->parties()->where('is_signer', true)->get();

            if ($signerParties->isEmpty()) {
                return ApiResponse::error(
                    __('messages.contract.no_signer_parties'),
                    ApiErrorCode::VALIDATION_FAILED,
                    422
                );
            }

            foreach ($signerParties as $party) {
                $party->update([
                    'signing_token' => Str::random(64),
                    'signing_token_expires_at' => now()->addDays(
                        $contract->signing_deadline
                            ? now()->diffInDays($contract->signing_deadline)
                            : 14
                    ),
                ]);
            }

            $this->transitionStatus($contract, UnifiedContractStatus::SENT_FOR_SIGNATURE, $userId);

            return ApiResponse::success($contract->fresh()->load('parties'), __('messages.contract.sent_for_signature'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Throwable $e) {
            Log::error('Contract sendForSignature error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Activate contract
     */
    public function activate(Request $request, string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);

            if (!$contract->status->canBeActivated()) {
                return ApiResponse::error(
                    __('messages.contract.invalid_status_transition'),
                    ApiErrorCode::VALIDATION_FAILED,
                    422
                );
            }

            $userId = $request->input('auth_user_id');

            $this->transitionStatus($contract, UnifiedContractStatus::ACTIVE, $userId);

            return ApiResponse::success($contract->fresh(), __('messages.contract.activated'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Throwable $e) {
            Log::error('Contract activate error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Suspend contract
     */
    public function suspend(Request $request, string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);

            if (!$contract->status->canBeSuspended()) {
                return ApiResponse::error(
                    __('messages.contract.invalid_status_transition'),
                    ApiErrorCode::VALIDATION_FAILED,
                    422
                );
            }

            $request->validate([
                'reason' => ['required', 'string', 'max:2000'],
            ]);

            $userId = $request->input('auth_user_id');

            $contract->update([
                'suspension_reason' => $request->input('reason'),
                'suspended_by' => $userId,
                'suspended_at' => now(),
            ]);

            $this->transitionStatus($contract, UnifiedContractStatus::SUSPENDED, $userId, $request->input('reason'));

            return ApiResponse::success($contract->fresh(), __('messages.contract.suspended'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());
        } catch (\Throwable $e) {
            Log::error('Contract suspend error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Reactivate a suspended contract
     */
    public function reactivate(Request $request, string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);

            if ($contract->status !== UnifiedContractStatus::SUSPENDED) {
                return ApiResponse::error(
                    __('messages.contract.invalid_status_transition'),
                    ApiErrorCode::VALIDATION_FAILED,
                    422
                );
            }

            $userId = $request->input('auth_user_id');

            $contract->update([
                'suspension_reason' => null,
                'suspended_by' => null,
                'suspended_at' => null,
            ]);

            $this->transitionStatus($contract, UnifiedContractStatus::ACTIVE, $userId, 'Reactivated');

            return ApiResponse::success($contract->fresh(), __('messages.contract.reactivated'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Throwable $e) {
            Log::error('Contract reactivate error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Complete contract
     */
    public function complete(Request $request, string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);

            if (!$contract->status->canBeCompleted()) {
                return ApiResponse::error(
                    __('messages.contract.invalid_status_transition'),
                    ApiErrorCode::VALIDATION_FAILED,
                    422
                );
            }

            $userId = $request->input('auth_user_id');

            $this->transitionStatus($contract, UnifiedContractStatus::COMPLETED, $userId);

            return ApiResponse::success($contract->fresh(), __('messages.contract.completed'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Throwable $e) {
            Log::error('Contract complete error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Cancel contract
     */
    public function cancel(Request $request, string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);

            if (!$contract->status->canBeCancelled()) {
                return ApiResponse::error(
                    __('messages.contract.invalid_status_transition'),
                    ApiErrorCode::VALIDATION_FAILED,
                    422
                );
            }

            $request->validate([
                'reason' => ['required', 'string', 'max:2000'],
            ]);

            $userId = $request->input('auth_user_id');

            $contract->update([
                'cancellation_reason' => $request->input('reason'),
                'cancelled_by' => $userId,
                'cancelled_at' => now(),
            ]);

            $this->transitionStatus($contract, UnifiedContractStatus::CANCELLED, $userId, $request->input('reason'));

            return ApiResponse::success($contract->fresh(), __('messages.contract.cancelled'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());
        } catch (\Throwable $e) {
            Log::error('Contract cancel error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Terminate contract
     */
    public function terminate(Request $request, string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);

            if (!$contract->status->canBeTerminated()) {
                return ApiResponse::error(
                    __('messages.contract.invalid_status_transition'),
                    ApiErrorCode::VALIDATION_FAILED,
                    422
                );
            }

            $request->validate([
                'reason' => ['required', 'string', 'max:2000'],
            ]);

            $userId = $request->input('auth_user_id');

            $contract->update([
                'termination_reason' => $request->input('reason'),
                'terminated_by' => $userId,
                'terminated_at' => now(),
            ]);

            $this->transitionStatus($contract, UnifiedContractStatus::TERMINATED, $userId, $request->input('reason'));

            return ApiResponse::success($contract->fresh(), __('messages.contract.terminated'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());
        } catch (\Throwable $e) {
            Log::error('Contract terminate error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    // ──────────────────────────────────────────
    // Financial
    // ──────────────────────────────────────────

    /**
     * Get payment plan for a contract
     */
    public function getPaymentPlan(string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);
            $plan = $contract->paymentPlan()->orderBy('due_date')->get();

            return ApiResponse::success([
                'contract_id' => $contract->id,
                'total_amount' => $contract->total_amount,
                'paid_amount' => $contract->paid_amount,
                'remaining_amount' => $contract->remaining_amount,
                'payment_status' => $contract->payment_status,
                'installments' => $plan,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Throwable $e) {
            Log::error('Contract getPaymentPlan error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Update payment plan
     */
    public function updatePaymentPlan(Request $request, string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);

            $request->validate([
                'installments' => ['required', 'array', 'min:1'],
                'installments.*.amount' => ['required', 'numeric', 'min:0'],
                'installments.*.due_date' => ['required', 'date'],
                'installments.*.description' => ['nullable', 'string', 'max:500'],
                'installments.*.description_ar' => ['nullable', 'string', 'max:500'],
            ]);

            DB::transaction(function () use ($contract, $request) {
                // Remove old unpaid installments
                $contract->paymentPlan()
                    ->where('status', 'pending')
                    ->delete();

                foreach ($request->input('installments') as $index => $installment) {
                    ContractPaymentPlan::create([
                        'contract_id' => $contract->id,
                        'installment_number' => $index + 1,
                        'amount' => $installment['amount'],
                        'due_date' => $installment['due_date'],
                        'description' => $installment['description'] ?? null,
                        'description_ar' => $installment['description_ar'] ?? null,
                        'status' => 'pending',
                    ]);
                }
            });

            return ApiResponse::success(
                $contract->fresh()->paymentPlan()->orderBy('due_date')->get(),
                __('messages.contract.payment_plan_updated')
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());
        } catch (\Throwable $e) {
            Log::error('Contract updatePaymentPlan error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Record a payment against a contract
     */
    public function recordPayment(Request $request, string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);

            $request->validate([
                'amount' => ['required', 'numeric', 'min:0.01'],
                'payment_method' => ['required', 'string', 'max:50'],
                'payment_date' => ['required', 'date'],
                'reference_number' => ['nullable', 'string', 'max:100'],
                'notes' => ['nullable', 'string', 'max:500'],
                'installment_id' => ['nullable', 'uuid'],
            ]);

            $userId = $request->input('auth_user_id');
            $amount = $request->input('amount');

            DB::transaction(function () use ($contract, $request, $amount, $userId) {
                // If specific installment, mark it paid
                if ($installmentId = $request->input('installment_id')) {
                    $installment = $contract->paymentPlan()->findOrFail($installmentId);
                    $installment->update([
                        'status' => 'paid',
                        'paid_amount' => $amount,
                        'paid_at' => $request->input('payment_date'),
                        'payment_method' => $request->input('payment_method'),
                        'payment_reference' => $request->input('reference_number'),
                    ]);
                }

                // Update contract paid amount
                $newPaid = (float) $contract->paid_amount + $amount;
                $total = (float) $contract->total_amount;

                $paymentStatus = 'unpaid';
                if ($newPaid >= $total) {
                    $paymentStatus = 'paid';
                } elseif ($newPaid > 0) {
                    $paymentStatus = 'partial';
                }

                $contract->update([
                    'paid_amount' => $newPaid,
                    'payment_status' => $paymentStatus,
                ]);

                $this->logStatusChange($contract, 'payment_recorded', $userId, "Amount: {$amount}");
            });

            return ApiResponse::success($contract->fresh(), __('messages.contract.payment_recorded'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());
        } catch (\Throwable $e) {
            Log::error('Contract recordPayment error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Generate invoices for contract installments
     */
    public function generateInvoices(Request $request, string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);
            $userId = $request->input('auth_user_id');

            $pendingInstallments = $contract->paymentPlan()
                ->where('status', 'pending')
                ->whereNull('invoice_id')
                ->orderBy('due_date')
                ->get();

            if ($pendingInstallments->isEmpty()) {
                return ApiResponse::error(
                    __('messages.contract.no_pending_installments'),
                    ApiErrorCode::VALIDATION_FAILED,
                    422
                );
            }

            // Mark installments as invoiced (actual invoice creation depends on InvoiceService)
            foreach ($pendingInstallments as $installment) {
                $installment->update(['status' => 'invoiced']);
            }

            $this->logStatusChange($contract, 'invoices_generated', $userId,
                "Generated invoices for {$pendingInstallments->count()} installments"
            );

            return ApiResponse::success([
                'invoiced_count' => $pendingInstallments->count(),
                'installments' => $pendingInstallments,
            ], __('messages.contract.invoices_generated'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Throwable $e) {
            Log::error('Contract generateInvoices error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    // ──────────────────────────────────────────
    // Versions
    // ──────────────────────────────────────────

    /**
     * Get contract versions
     */
    public function getVersions(string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);
            $versions = $contract->versions()->orderByDesc('version_number')->get();

            return ApiResponse::success($versions);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Throwable $e) {
            Log::error('Contract getVersions error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    // ──────────────────────────────────────────
    // Parties
    // ──────────────────────────────────────────

    /**
     * Get contract parties
     */
    public function getParties(string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);
            $parties = $contract->parties()->get();

            return ApiResponse::success($parties);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Throwable $e) {
            Log::error('Contract getParties error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Add a party to contract
     */
    public function addParty(Request $request, string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);

            if (!$contract->status->canBeModified()) {
                return ApiResponse::error(
                    __('messages.contract.cannot_be_modified'),
                    ApiErrorCode::VALIDATION_FAILED,
                    403
                );
            }

            $validated = $request->validate([
                'party_type' => ['required', 'string', 'max:50'],
                'party_role' => ['required', 'string', 'max:50'],
                'user_id' => ['nullable', 'uuid'],
                'company_name' => ['nullable', 'string', 'max:255'],
                'company_name_ar' => ['nullable', 'string', 'max:255'],
                'contact_name' => ['nullable', 'string', 'max:255'],
                'contact_name_ar' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'email'],
                'phone' => ['nullable', 'string', 'max:20'],
                'national_id' => ['nullable', 'string', 'max:20'],
                'commercial_reg' => ['nullable', 'string', 'max:50'],
                'vat_number' => ['nullable', 'string', 'max:50'],
                'address' => ['nullable', 'string', 'max:500'],
                'address_ar' => ['nullable', 'string', 'max:500'],
                'is_signer' => ['nullable', 'boolean'],
                'signing_order' => ['nullable', 'integer'],
                'metadata' => ['nullable', 'array'],
            ]);

            $validated['contract_id'] = $contract->id;
            $party = ContractParty::create($validated);

            return ApiResponse::created($party, __('messages.contract.party_added'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());
        } catch (\Throwable $e) {
            Log::error('Contract addParty error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Update a party on contract
     */
    public function updateParty(Request $request, string $id, string $partyId): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);

            if (!$contract->status->canBeModified()) {
                return ApiResponse::error(
                    __('messages.contract.cannot_be_modified'),
                    ApiErrorCode::VALIDATION_FAILED,
                    403
                );
            }

            $party = $contract->parties()->findOrFail($partyId);

            $validated = $request->validate([
                'party_type' => ['sometimes', 'string', 'max:50'],
                'party_role' => ['sometimes', 'string', 'max:50'],
                'user_id' => ['nullable', 'uuid'],
                'company_name' => ['nullable', 'string', 'max:255'],
                'company_name_ar' => ['nullable', 'string', 'max:255'],
                'contact_name' => ['nullable', 'string', 'max:255'],
                'contact_name_ar' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'email'],
                'phone' => ['nullable', 'string', 'max:20'],
                'national_id' => ['nullable', 'string', 'max:20'],
                'commercial_reg' => ['nullable', 'string', 'max:50'],
                'vat_number' => ['nullable', 'string', 'max:50'],
                'address' => ['nullable', 'string', 'max:500'],
                'address_ar' => ['nullable', 'string', 'max:500'],
                'is_signer' => ['nullable', 'boolean'],
                'signing_order' => ['nullable', 'integer'],
                'metadata' => ['nullable', 'array'],
            ]);

            $party->update($validated);

            return ApiResponse::success($party, __('messages.contract.party_updated'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());
        } catch (\Throwable $e) {
            Log::error('Contract updateParty error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Remove a party from contract
     */
    public function removeParty(string $id, string $partyId): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);

            if (!$contract->status->canBeModified()) {
                return ApiResponse::error(
                    __('messages.contract.cannot_be_modified'),
                    ApiErrorCode::VALIDATION_FAILED,
                    403
                );
            }

            $party = $contract->parties()->findOrFail($partyId);
            $party->delete();

            return ApiResponse::success(null, __('messages.contract.party_removed'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Throwable $e) {
            Log::error('Contract removeParty error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    // ──────────────────────────────────────────
    // Attachments
    // ──────────────────────────────────────────

    /**
     * Get contract attachments
     */
    public function getAttachments(string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);
            $attachments = $contract->attachments()->orderByDesc('created_at')->get();

            return ApiResponse::success($attachments);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Throwable $e) {
            Log::error('Contract getAttachments error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Upload an attachment
     */
    public function uploadAttachment(Request $request, string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);

            $request->validate([
                'file' => ['required', 'file', 'max:10240'], // 10MB max
                'type' => ['nullable', 'string', 'max:50'],
                'title' => ['nullable', 'string', 'max:255'],
                'title_ar' => ['nullable', 'string', 'max:255'],
                'description' => ['nullable', 'string', 'max:500'],
            ]);

            $file = $request->file('file');
            $path = $file->store("contracts/{$contract->id}/attachments", 'public');

            $attachment = ContractAttachment::create([
                'contract_id' => $contract->id,
                'type' => $request->input('type', 'document'),
                'title' => $request->input('title', $file->getClientOriginalName()),
                'title_ar' => $request->input('title_ar'),
                'description' => $request->input('description'),
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'uploaded_by' => $request->input('auth_user_id'),
            ]);

            return ApiResponse::created($attachment, __('messages.contract.attachment_uploaded'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return ApiResponse::validationError($e->errors());
        } catch (\Throwable $e) {
            Log::error('Contract uploadAttachment error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Delete an attachment
     */
    public function deleteAttachment(string $id, string $attachmentId): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);
            $attachment = $contract->attachments()->findOrFail($attachmentId);

            // Delete file from storage
            if ($attachment->file_path) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            $attachment->delete();

            return ApiResponse::success(null, __('messages.contract.attachment_deleted'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Throwable $e) {
            Log::error('Contract deleteAttachment error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    // ──────────────────────────────────────────
    // Activity Log
    // ──────────────────────────────────────────

    /**
     * Get contract activity log
     */
    public function getActivityLog(string $id): JsonResponse
    {
        try {
            $contract = Contract::findOrFail($id);
            $logs = $contract->statusLogs()->orderByDesc('created_at')->get();

            return ApiResponse::success($logs);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Throwable $e) {
            Log::error('Contract getActivityLog error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    // ──────────────────────────────────────────
    // Statistics & Pipeline
    // ──────────────────────────────────────────

    /**
     * Contract statistics
     */
    public function stats(Request $request): JsonResponse
    {
        try {
            $query = Contract::query();

            if ($eventId = $request->input('event_id')) {
                $query->where('event_id', $eventId);
            }

            if ($type = $request->input('type')) {
                $query->byType($type);
            }

            $total = (clone $query)->count();
            $totalValue = (clone $query)->sum('total_amount');
            $totalPaid = (clone $query)->sum('paid_amount');

            $byStatus = (clone $query)
                ->selectRaw('status, COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total_value')
                ->groupBy('status')
                ->get()
                ->keyBy('status');

            $byType = (clone $query)
                ->selectRaw('type, COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total_value')
                ->groupBy('type')
                ->get()
                ->keyBy('type');

            $byPaymentStatus = (clone $query)
                ->selectRaw('payment_status, COUNT(*) as count')
                ->groupBy('payment_status')
                ->get()
                ->keyBy('payment_status');

            return ApiResponse::success([
                'total' => $total,
                'total_value' => (float) $totalValue,
                'total_paid' => (float) $totalPaid,
                'total_outstanding' => (float) ($totalValue - $totalPaid),
                'by_status' => $byStatus,
                'by_type' => $byType,
                'by_payment_status' => $byPaymentStatus,
            ]);
        } catch (\Throwable $e) {
            Log::error('Contract stats error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Pipeline view — contracts grouped by status
     */
    public function pipeline(Request $request): JsonResponse
    {
        try {
            $query = Contract::query();

            if ($eventId = $request->input('event_id')) {
                $query->where('event_id', $eventId);
            }

            if ($type = $request->input('type')) {
                $query->byType($type);
            }

            $pipeline = $query
                ->selectRaw('status, COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total_value, COALESCE(SUM(paid_amount), 0) as paid_value')
                ->groupBy('status')
                ->get()
                ->keyBy('status');

            $statuses = UnifiedContractStatus::values();
            $stages = [];
            foreach ($statuses as $status) {
                $item = $pipeline->get($status);
                $stages[] = [
                    'status'      => $status,
                    'count'       => (int) ($item?->count ?? 0),
                    'total_value' => (float) ($item?->total_value ?? 0),
                    'paid_value'  => (float) ($item?->paid_value ?? 0),
                ];
            }

            return ApiResponse::success([
                'stages'      => $stages,
                'total_count' => (int) $pipeline->sum('count'),
                'total_value' => (float) $pipeline->sum('total_value'),
                'total_paid'  => (float) $pipeline->sum('paid_value'),
            ]);
        } catch (\Throwable $e) {
            Log::error('Contract pipeline error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Expiring contracts (default: 30 days)
     */
    public function expiring(Request $request): JsonResponse
    {
        try {
            $days = (int) $request->input('days', 30);
            $query = Contract::expiring($days)->with(['event', 'parties']);

            if ($type = $request->input('type')) {
                $query->byType($type);
            }

            $this->applySafeOrder($query, $request, [
                'end_date', 'total_amount', 'contract_number', 'created_at',
            ], 'end_date', 'asc');

            $perPage = min($request->input('per_page', 15), 50);

            return ApiResponse::paginated($query->paginate($perPage));
        } catch (\Throwable $e) {
            Log::error('Contract expiring error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    // ──────────────────────────────────────────
    // Private Helpers
    // ──────────────────────────────────────────

    /**
     * Transition contract status and log the change
     */
    private function transitionStatus(Contract $contract, UnifiedContractStatus $newStatus, ?string $userId = null, ?string $notes = null): void
    {
        $oldStatus = $contract->status->value;

        $contract->update([
            'status' => $newStatus->value,
            'updated_by' => $userId,
        ]);

        $this->logStatusChange($contract, $newStatus->value, $userId, $notes, $oldStatus);
    }

    /**
     * Log a status change
     */
    private function logStatusChange(Contract $contract, string $action, ?string $userId = null, ?string $notes = null, ?string $fromStatus = null): void
    {
        try {
            $contract->statusLogs()->create([
                'from_status' => $fromStatus ?? $contract->status->value,
                'to_status' => $action,
                'changed_by' => $userId,
                'notes' => $notes,
                'ip_address' => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to log contract status change', [
                'contract_id' => $contract->id,
                'action' => $action,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Check if both legal and finance are approved, and auto-approve if ready
     */
    private function checkAndApproveIfReady(Contract $contract, string $userId): void
    {
        $contract->refresh();

        if ($contract->legal_approved && $contract->finance_approved) {
            $contract->update([
                'final_approved_by' => $userId,
                'final_approved_at' => now(),
            ]);

            $this->transitionStatus($contract, UnifiedContractStatus::APPROVED, $userId, 'Auto-approved: legal and finance both approved');
        }
    }
}
