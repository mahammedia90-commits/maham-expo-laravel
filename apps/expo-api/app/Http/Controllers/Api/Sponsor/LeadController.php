<?php

namespace App\Http\Controllers\Api\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use App\Models\SponsorLead;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    use SafeOrderBy;

    /**
     * عملائي المحتملون
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');
        $sponsorIds = Sponsor::forUser($userId)->pluck('id');

        $query = SponsorLead::whereIn('sponsor_id', $sponsorIds)
            ->with(['event']);

        if ($eventId = $request->input('event_id')) {
            $query->forEvent($eventId);
        }

        if ($interest = $request->input('interest_level')) {
            $query->ofInterest($interest);
        }

        if ($status = $request->input('status')) {
            $query->ofStatus($status);
        }

        if ($search = $this->sanitizeSearch($request->input('search'))) {
            $query->search($search);
        }

        $this->applySafeOrder($query, $request, [
            'company_name', 'interest_level', 'status', 'captured_at', 'created_at',
        ], 'created_at', 'desc');

        $perPage = min($request->input('per_page', 15), 50);

        return ApiResponse::paginated($query->paginate($perPage));
    }

    /**
     * تفاصيل عميل محتمل
     */
    public function show(Request $request, SponsorLead $sponsorLead): JsonResponse
    {
        $userId = $request->input('auth_user_id');
        $sponsorIds = Sponsor::forUser($userId)->pluck('id');

        if (!$sponsorIds->contains($sponsorLead->sponsor_id)) {
            return ApiResponse::error(__('messages.unauthorized'), 'unauthorized', 403);
        }

        $sponsorLead->load(['sponsor', 'event']);

        return ApiResponse::success($sponsorLead);
    }
}
