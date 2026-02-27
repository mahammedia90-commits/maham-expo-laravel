<?php

namespace App\Http\Controllers\Api\My;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\Favorite;
use App\Models\Notification;
use App\Models\RentalContract;
use App\Models\RentalRequest;
use App\Models\Space;
use App\Models\Sponsor;
use App\Models\SponsorAsset;
use App\Models\SponsorContract;
use App\Models\SupportTicket;
use App\Models\VisitRequest;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Unified User Dashboard Controller
 *
 * Returns dashboard data based on the user's permissions.
 * No hardcoded role checks — dynamically builds the response
 * based on what the authenticated user has access to.
 */
class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');
        $permissions = $request->input('auth_user_permissions', []);

        $data = [];

        // Profile info (any authenticated user)
        $profile = BusinessProfile::where('user_id', $userId)->first();
        if ($profile) {
            $data['profile'] = [
                'id' => $profile->id,
                'status' => $profile->status,
                'company_name' => $profile->company_name ?? null,
                'company_name_ar' => $profile->company_name_ar ?? null,
            ];
        }

        // My visit requests (merchant/user)
        if (in_array('visit-requests.view', $permissions)) {
            $data['visit_requests'] = [
                'total' => VisitRequest::where('user_id', $userId)->count(),
                'pending' => VisitRequest::where('user_id', $userId)->where('status', 'pending')->count(),
                'approved' => VisitRequest::where('user_id', $userId)->where('status', 'approved')->count(),
                'rejected' => VisitRequest::where('user_id', $userId)->where('status', 'rejected')->count(),
            ];
        }

        // My rental requests (merchant)
        if (in_array('rental-requests.view', $permissions)) {
            $data['rental_requests'] = [
                'total' => RentalRequest::where('user_id', $userId)->count(),
                'pending' => RentalRequest::where('user_id', $userId)->where('status', 'pending')->count(),
                'approved' => RentalRequest::where('user_id', $userId)->where('status', 'approved')->count(),
                'rejected' => RentalRequest::where('user_id', $userId)->where('status', 'rejected')->count(),
            ];
        }

        // My spaces & received requests (investor)
        if (in_array('spaces.create', $permissions)) {
            $spacesQuery = Space::where('investor_id', $userId);
            $spaceIds = (clone $spacesQuery)->pluck('id');

            $data['spaces'] = [
                'total' => $spacesQuery->count(),
                'available' => (clone $spacesQuery)->where('status', 'available')->count(),
                'rented' => (clone $spacesQuery)->where('status', 'rented')->count(),
            ];

            if ($spaceIds->isNotEmpty()) {
                $data['received_rental_requests'] = [
                    'total' => RentalRequest::whereIn('space_id', $spaceIds)->count(),
                    'pending' => RentalRequest::whereIn('space_id', $spaceIds)->where('status', 'pending')->count(),
                ];

                $data['received_visit_requests'] = [
                    'total' => VisitRequest::whereHas('event', function ($q) use ($spaceIds) {
                        $q->whereHas('spaces', fn($sq) => $sq->whereIn('spaces.id', $spaceIds));
                    })->count(),
                ];
            }
        }

        // Rental contracts (merchant/investor)
        if (in_array('rental-contracts.view', $permissions)) {
            $contractQuery = RentalContract::where('merchant_id', $userId)
                ->orWhere('investor_id', $userId);

            $data['rental_contracts'] = [
                'total' => (clone $contractQuery)->count(),
                'active' => (clone $contractQuery)->where('status', 'active')->count(),
                'pending_signature' => (clone $contractQuery)
                    ->where('status', 'approved')
                    ->where(function ($q) use ($userId) {
                        $q->where(function ($sq) use ($userId) {
                            $sq->where('merchant_id', $userId)->whereNull('signed_by_merchant');
                        })->orWhere(function ($sq) use ($userId) {
                            $sq->where('investor_id', $userId)->whereNull('signed_by_investor');
                        });
                    })->count(),
            ];
        }

        // Sponsorship data
        if (in_array('sponsor-contracts.view', $permissions)) {
            $sponsorIds = Sponsor::where('user_id', $userId)->pluck('id');
            if ($sponsorIds->isNotEmpty()) {
                $data['sponsorship'] = [
                    'contracts' => SponsorContract::whereIn('sponsor_id', $sponsorIds)->count(),
                    'active_contracts' => SponsorContract::whereIn('sponsor_id', $sponsorIds)
                        ->where('status', 'active')->count(),
                    'assets' => SponsorAsset::whereIn('sponsor_id', $sponsorIds)->count(),
                    'pending_assets' => SponsorAsset::whereIn('sponsor_id', $sponsorIds)
                        ->where('status', 'pending')->count(),
                ];
            }
        }

        // Favorites
        if (in_array('favorites.view', $permissions)) {
            $data['favorites_count'] = Favorite::where('user_id', $userId)->count();
        }

        // Unread notifications
        if (in_array('notifications.view', $permissions)) {
            $data['unread_notifications'] = Notification::where('user_id', $userId)
                ->whereNull('read_at')->count();
        }

        // Support tickets
        if (in_array('support-tickets.view', $permissions)) {
            $data['support_tickets'] = [
                'open' => SupportTicket::where('user_id', $userId)
                    ->whereIn('status', ['open', 'in_progress'])->count(),
                'total' => SupportTicket::where('user_id', $userId)->count(),
            ];
        }

        return ApiResponse::success($data);
    }
}
