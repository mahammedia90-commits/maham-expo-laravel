<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\RentalContract;
use App\Models\RentalRequest;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RentalContractController extends Controller
{
    use SafeOrderBy;

    public function index(Request $request): JsonResponse
    {
        $query = RentalContract::with(['event', 'rentalRequest']);

        if ($eventId = $request->input('event_id')) {
            $query->forEvent($eventId);
        }

        if ($status = $request->input('status')) {
            $query->ofStatus($status);
        }

        if ($merchantId = $request->input('merchant_id')) {
            $query->forMerchant($merchantId);
        }

        if ($investorId = $request->input('investor_id')) {
            $query->forInvestor($investorId);
        }

        if ($search = $this->sanitizeSearch($request->input('search'))) {
            $query->search($search);
        }

        $this->applySafeOrder($query, $request, [
            'contract_number', 'start_date', 'end_date', 'total_amount', 'status', 'created_at',
        ], 'created_at', 'desc');

        $perPage = min($request->input('per_page', 15), 50);

        return ApiResponse::paginated($query->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'rental_request_id' => ['required', 'uuid', 'exists:rental_requests,id'],
            'terms' => ['nullable', 'string', 'max:10000'],
            'terms_ar' => ['nullable', 'string', 'max:10000'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $rentalRequest = RentalRequest::findOrFail($validated['rental_request_id']);

        $contract = RentalContract::createFromRentalRequest($rentalRequest, [
            'terms' => $validated['terms'] ?? null,
            'terms_ar' => $validated['terms_ar'] ?? null,
            'admin_notes' => $validated['admin_notes'] ?? null,
        ]);

        $contract->load(['event', 'rentalRequest']);

        return ApiResponse::created($contract, __('messages.rental_contract.created'));
    }

    public function show(RentalContract $rentalContract): JsonResponse
    {
        $rentalContract->load(['event', 'rentalRequest', 'invoices']);
        return ApiResponse::success($rentalContract);
    }

    public function update(Request $request, RentalContract $rentalContract): JsonResponse
    {
        if (!$rentalContract->status->canBeModified()) {
            return ApiResponse::error(
                __('messages.rental_contract.cannot_be_modified'),
                ApiErrorCode::VALIDATION_FAILED,
                403
            );
        }

        $validated = $request->validate([
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after:start_date'],
            'total_amount' => ['sometimes', 'numeric', 'min:0'],
            'terms' => ['nullable', 'string', 'max:10000'],
            'terms_ar' => ['nullable', 'string', 'max:10000'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $rentalContract->update($validated);
        $rentalContract->load(['event', 'rentalRequest']);

        return ApiResponse::success($rentalContract, __('messages.rental_contract.updated'));
    }

    public function approve(Request $request, RentalContract $rentalContract): JsonResponse
    {
        if ($rentalContract->status !== \App\Enums\ContractStatus::PENDING) {
            return ApiResponse::error(
                __('messages.rental_contract.not_pending'),
                ApiErrorCode::VALIDATION_FAILED,
                422
            );
        }

        $rentalContract->approve($request->input('auth_user_id'));

        return ApiResponse::success($rentalContract, __('messages.rental_contract.approved'));
    }

    public function reject(Request $request, RentalContract $rentalContract): JsonResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:500'],
        ]);

        $rentalContract->reject(
            $request->input('rejection_reason'),
            $request->input('auth_user_id')
        );

        return ApiResponse::success($rentalContract, __('messages.rental_contract.rejected'));
    }

    public function terminate(Request $request, RentalContract $rentalContract): JsonResponse
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $rentalContract->terminate($request->input('reason'));

        return ApiResponse::success($rentalContract, __('messages.rental_contract.terminated'));
    }

    /**
     * توقيع العقد من قبل المستثمر
     */
    public function signByInvestor(Request $request, RentalContract $rentalContract): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        if ($rentalContract->investor_id !== $userId) {
            return ApiResponse::error(
                __('messages.auth.permission_denied'),
                ApiErrorCode::PERMISSION_DENIED,
                403
            );
        }

        $rentalContract->signByInvestor();

        return ApiResponse::success($rentalContract->fresh(), __('messages.rental_contract.signed'));
    }

    /**
     * توقيع العقد من قبل التاجر
     */
    public function signByMerchant(Request $request, RentalContract $rentalContract): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        if ($rentalContract->merchant_id !== $userId) {
            return ApiResponse::error(
                __('messages.auth.permission_denied'),
                ApiErrorCode::PERMISSION_DENIED,
                403
            );
        }

        $rentalContract->signByMerchant();

        return ApiResponse::success($rentalContract->fresh(), __('messages.rental_contract.signed'));
    }
}
