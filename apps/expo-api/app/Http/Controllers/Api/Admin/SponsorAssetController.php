<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SponsorAsset;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SponsorAssetController extends Controller
{
    /**
     * List sponsor assets
     */
    public function index(Request $request): JsonResponse
    {
        $query = SponsorAsset::with(['sponsor', 'event']);

        if ($sponsorId = $request->input('sponsor_id')) {
            $query->forSponsor($sponsorId);
        }

        if ($eventId = $request->input('event_id')) {
            $query->forEvent($eventId);
        }

        if ($type = $request->input('type')) {
            $query->ofType($type);
        }

        if ($request->has('is_approved')) {
            if ($request->boolean('is_approved')) {
                $query->approved();
            } else {
                $query->pendingApproval();
            }
        }

        $assets = $query->ordered()->paginate($request->input('per_page', 15));

        return ApiResponse::paginated($assets);
    }

    /**
     * Show an asset
     */
    public function show(SponsorAsset $sponsorAsset): JsonResponse
    {
        $sponsorAsset->load(['sponsor', 'event']);

        return ApiResponse::success($sponsorAsset);
    }

    /**
     * Approve an asset
     */
    public function approve(Request $request, SponsorAsset $sponsorAsset): JsonResponse
    {
        $approvedBy = $request->input('auth_user_id');
        $sponsorAsset->approve($approvedBy);

        return ApiResponse::success($sponsorAsset, __('messages.sponsor_asset.approved'));
    }

    /**
     * Reject an asset
     */
    public function reject(Request $request, SponsorAsset $sponsorAsset): JsonResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:2000'],
        ]);

        $sponsorAsset->reject($request->input('rejection_reason'));

        return ApiResponse::success($sponsorAsset, __('messages.sponsor_asset.rejected'));
    }
}
