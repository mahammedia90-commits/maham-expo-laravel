<?php

namespace App\Http\Controllers\Api\My;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\ContractParty;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MyContractController extends Controller
{
    use SafeOrderBy;

    /**
     * List contracts where the authenticated user is a party
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $userId = $request->input('auth_user_id');

            $contractIds = ContractParty::where('user_id', $userId)
                ->pluck('contract_id');

            $query = Contract::with(['event', 'parties'])
                ->whereIn('id', $contractIds);

            if ($type = $request->input('type')) {
                $query->byType($type);
            }

            if ($status = $request->input('status')) {
                $query->where('status', $status);
            }

            if ($search = $this->sanitizeSearch($request->input('search'))) {
                $query->where(function ($q) use ($search) {
                    $q->where('contract_number', 'like', "%{$search}%")
                      ->orWhere('title', 'like', "%{$search}%")
                      ->orWhere('title_ar', 'like', "%{$search}%");
                });
            }

            $this->applySafeOrder($query, $request, [
                'contract_number', 'title', 'type', 'status',
                'total_amount', 'start_date', 'end_date', 'created_at',
            ], 'created_at', 'desc');

            $perPage = min($request->input('per_page', 15), 50);

            return ApiResponse::paginated($query->paginate($perPage));
        } catch (\Throwable $e) {
            Log::error('MyContract index error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Show contract details (only if user is a party)
     */
    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $userId = $request->input('auth_user_id');

            $contract = Contract::with([
                'parties',
                'paymentPlan' => fn($q) => $q->orderBy('due_date'),
                'versions' => fn($q) => $q->orderByDesc('version_number'),
                'attachments',
                'event',
            ])->findOrFail($id);

            // Verify ownership — user must be a party
            $isParty = $contract->parties()->where('user_id', $userId)->exists();
            if (!$isParty) {
                return ApiResponse::error(
                    __('messages.auth.permission_denied'),
                    ApiErrorCode::PERMISSION_DENIED,
                    403
                );
            }

            return ApiResponse::success($contract);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Throwable $e) {
            Log::error('MyContract show error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Get payment plan for a contract (only if user is a party)
     */
    public function getPaymentPlan(Request $request, string $id): JsonResponse
    {
        try {
            $userId = $request->input('auth_user_id');
            $contract = Contract::findOrFail($id);

            $isParty = $contract->parties()->where('user_id', $userId)->exists();
            if (!$isParty) {
                return ApiResponse::error(
                    __('messages.auth.permission_denied'),
                    ApiErrorCode::PERMISSION_DENIED,
                    403
                );
            }

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
            Log::error('MyContract getPaymentPlan error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Get contract versions (only if user is a party)
     */
    public function getVersions(Request $request, string $id): JsonResponse
    {
        try {
            $userId = $request->input('auth_user_id');
            $contract = Contract::findOrFail($id);

            $isParty = $contract->parties()->where('user_id', $userId)->exists();
            if (!$isParty) {
                return ApiResponse::error(
                    __('messages.auth.permission_denied'),
                    ApiErrorCode::PERMISSION_DENIED,
                    403
                );
            }

            $versions = $contract->versions()->orderByDesc('version_number')->get();

            return ApiResponse::success($versions);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Throwable $e) {
            Log::error('MyContract getVersions error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }

    /**
     * Get contract attachments (only if user is a party)
     */
    public function getAttachments(Request $request, string $id): JsonResponse
    {
        try {
            $userId = $request->input('auth_user_id');
            $contract = Contract::findOrFail($id);

            $isParty = $contract->parties()->where('user_id', $userId)->exists();
            if (!$isParty) {
                return ApiResponse::error(
                    __('messages.auth.permission_denied'),
                    ApiErrorCode::PERMISSION_DENIED,
                    403
                );
            }

            $attachments = $contract->attachments()->orderByDesc('created_at')->get();

            return ApiResponse::success($attachments);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponse::notFound(__('messages.contract.not_found'));
        } catch (\Throwable $e) {
            Log::error('MyContract getAttachments error', ['error' => $e->getMessage()]);
            return ApiResponse::serverError(__('messages.server_error'), $e);
        }
    }
}
