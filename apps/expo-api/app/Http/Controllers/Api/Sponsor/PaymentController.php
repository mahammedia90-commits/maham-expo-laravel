<?php

namespace App\Http\Controllers\Api\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use App\Models\SponsorPayment;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * List sponsor's own payments
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');
        $sponsorIds = Sponsor::forUser($userId)->pluck('id');

        $query = SponsorPayment::whereHas('contract', function ($q) use ($sponsorIds) {
            $q->whereIn('sponsor_id', $sponsorIds);
        })->with(['contract.sponsor', 'contract.event']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($contractId = $request->input('sponsor_contract_id')) {
            $query->forContract($contractId);
        }

        $payments = $query->orderBy('due_date', 'asc')
            ->paginate(min($request->input('per_page', 15), 50));

        return ApiResponse::paginated($payments);
    }

    /**
     * Show a specific payment (ownership verified)
     */
    public function show(Request $request, SponsorPayment $sponsorPayment): JsonResponse
    {
        $userId = $request->input('auth_user_id');
        $sponsorIds = Sponsor::forUser($userId)->pluck('id');

        $sponsorPayment->load('contract');

        if (!$sponsorIds->contains($sponsorPayment->contract->sponsor_id)) {
            return ApiResponse::error(
                __('messages.auth.permission_denied'),
                ApiErrorCode::PERMISSION_DENIED,
                403
            );
        }

        $sponsorPayment->load(['contract.sponsor', 'contract.event', 'contract.sponsorPackage']);

        return ApiResponse::success($sponsorPayment);
    }
}
