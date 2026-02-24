<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SponsorContract;
use App\Models\SponsorPackage;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SponsorContractController extends Controller
{
    use SafeOrderBy;

    /**
     * List all sponsor contracts
     */
    public function index(Request $request): JsonResponse
    {
        $query = SponsorContract::with(['sponsor', 'sponsorPackage', 'event']);

        if ($sponsorId = $request->input('sponsor_id')) {
            $query->forSponsor($sponsorId);
        }

        if ($eventId = $request->input('event_id')) {
            $query->forEvent($eventId);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($paymentStatus = $request->input('payment_status')) {
            $query->where('payment_status', $paymentStatus);
        }

        $this->applySafeOrder($query, $request, [
            'contract_number', 'total_amount', 'paid_amount', 'status',
            'payment_status', 'start_date', 'end_date', 'created_at',
        ], 'created_at', 'desc');

        $perPage = min($request->input('per_page', 15), 50);
        $contracts = $query->paginate($perPage);

        return ApiResponse::paginated($contracts);
    }

    /**
     * Create a new sponsor contract
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sponsor_id' => ['required', 'uuid', 'exists:sponsors,id'],
            'sponsor_package_id' => ['required', 'uuid', 'exists:sponsor_packages,id'],
            'event_id' => ['required', 'uuid', 'exists:events,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'total_amount' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'terms' => ['nullable', 'string'],
            'terms_ar' => ['nullable', 'string'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'status' => ['sometimes', Rule::enum(\App\Enums\SponsorContractStatus::class)],
        ]);

        // Check package availability
        $package = SponsorPackage::findOrFail($validated['sponsor_package_id']);
        if (!$package->is_available) {
            return ApiResponse::error(
                __('messages.sponsor_package.full'),
                ApiErrorCode::SPONSOR_PACKAGE_FULL,
                409
            );
        }

        $contract = SponsorContract::create($validated);
        $contract->load(['sponsor', 'sponsorPackage', 'event']);

        return ApiResponse::created($contract, __('messages.sponsor_contract.created'));
    }

    /**
     * Show a sponsor contract
     */
    public function show(SponsorContract $sponsorContract): JsonResponse
    {
        $sponsorContract->load([
            'sponsor',
            'sponsorPackage',
            'event',
            'payments' => fn($q) => $q->orderBy('due_date'),
            'benefits',
        ]);

        return ApiResponse::success($sponsorContract);
    }

    /**
     * Update a sponsor contract
     */
    public function update(Request $request, SponsorContract $sponsorContract): JsonResponse
    {
        if (!$sponsorContract->can_be_modified) {
            return ApiResponse::error(
                __('messages.sponsor_contract.cannot_be_modified'),
                ApiErrorCode::SPONSOR_CONTRACT_CANNOT_BE_MODIFIED,
                403
            );
        }

        $validated = $request->validate([
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
            'total_amount' => ['sometimes', 'numeric', 'min:0', 'max:9999999999'],
            'terms' => ['nullable', 'string'],
            'terms_ar' => ['nullable', 'string'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $sponsorContract->update($validated);
        $sponsorContract->load(['sponsor', 'sponsorPackage', 'event']);

        return ApiResponse::success($sponsorContract, __('messages.sponsor_contract.updated'));
    }

    /**
     * Approve a sponsor contract
     */
    public function approve(Request $request, SponsorContract $sponsorContract): JsonResponse
    {
        if ($sponsorContract->status !== \App\Enums\SponsorContractStatus::PENDING) {
            return ApiResponse::error(
                __('messages.sponsor_contract.cannot_be_modified'),
                ApiErrorCode::SPONSOR_CONTRACT_CANNOT_BE_MODIFIED,
                403
            );
        }

        $request->validate([
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $reviewerId = $request->input('auth_user_id');
        $sponsorContract->approve($reviewerId, $request->input('notes'));

        return ApiResponse::success($sponsorContract, __('messages.sponsor_contract.approved'));
    }

    /**
     * Reject a sponsor contract
     */
    public function reject(Request $request, SponsorContract $sponsorContract): JsonResponse
    {
        if ($sponsorContract->status !== \App\Enums\SponsorContractStatus::PENDING) {
            return ApiResponse::error(
                __('messages.sponsor_contract.cannot_be_modified'),
                ApiErrorCode::SPONSOR_CONTRACT_CANNOT_BE_MODIFIED,
                403
            );
        }

        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $reviewerId = $request->input('auth_user_id');
        $sponsorContract->reject($reviewerId, $request->input('rejection_reason'), $request->input('notes'));

        return ApiResponse::success($sponsorContract, __('messages.sponsor_contract.rejected'));
    }

    /**
     * Complete a sponsor contract
     */
    public function complete(SponsorContract $sponsorContract): JsonResponse
    {
        if ($sponsorContract->status !== \App\Enums\SponsorContractStatus::ACTIVE) {
            return ApiResponse::error(
                __('messages.sponsor_contract.cannot_be_modified'),
                ApiErrorCode::SPONSOR_CONTRACT_CANNOT_BE_MODIFIED,
                403
            );
        }

        $sponsorContract->complete();

        return ApiResponse::success($sponsorContract, __('messages.sponsor_contract.completed'));
    }
}
