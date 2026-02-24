<?php

namespace App\Http\Controllers\Api\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use App\Models\SponsorContract;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    /**
     * List sponsor's own contracts
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');
        $sponsorIds = Sponsor::forUser($userId)->pluck('id');

        $query = SponsorContract::whereIn('sponsor_id', $sponsorIds)
            ->with(['sponsor', 'sponsorPackage', 'event']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($eventId = $request->input('event_id')) {
            $query->forEvent($eventId);
        }

        $contracts = $query->orderBy('created_at', 'desc')
            ->paginate(min($request->input('per_page', 15), 50));

        return ApiResponse::paginated($contracts);
    }

    /**
     * Show a specific contract (ownership verified)
     */
    public function show(Request $request, SponsorContract $sponsorContract): JsonResponse
    {
        $userId = $request->input('auth_user_id');
        $sponsorIds = Sponsor::forUser($userId)->pluck('id');

        if (!$sponsorIds->contains($sponsorContract->sponsor_id)) {
            return ApiResponse::error(
                __('messages.auth.permission_denied'),
                ApiErrorCode::PERMISSION_DENIED,
                403
            );
        }

        $sponsorContract->load([
            'sponsor',
            'sponsorPackage',
            'event',
            'payments' => fn($q) => $q->orderBy('due_date'),
            'benefits',
        ]);

        return ApiResponse::success($sponsorContract);
    }
}
