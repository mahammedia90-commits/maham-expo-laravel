<?php

namespace App\Http\Controllers\Api\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use App\Models\SponsorContract;
use App\Models\SponsorExposureTracking;
use App\Models\SponsorPayment;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Sponsor dashboard overview
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        // Get sponsor records for this user
        $sponsorIds = Sponsor::forUser($userId)->pluck('id');

        if ($sponsorIds->isEmpty()) {
            return ApiResponse::success([
                'has_sponsor_profile' => false,
                'message' => 'No sponsor profile found',
            ]);
        }

        // Active contracts
        $activeContracts = SponsorContract::whereIn('sponsor_id', $sponsorIds)
            ->active()
            ->with(['event', 'sponsorPackage'])
            ->get();

        // Financial summary
        $totalContractValue = SponsorContract::whereIn('sponsor_id', $sponsorIds)
            ->whereIn('status', ['active', 'completed'])
            ->sum('total_amount');

        $totalPaid = SponsorContract::whereIn('sponsor_id', $sponsorIds)
            ->whereIn('status', ['active', 'completed'])
            ->sum('paid_amount');

        // Upcoming payments
        $upcomingPayments = SponsorPayment::whereHas('contract', function ($q) use ($sponsorIds) {
            $q->whereIn('sponsor_id', $sponsorIds);
        })
            ->pending()
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        // Overdue payments count
        $overdueCount = SponsorPayment::whereHas('contract', function ($q) use ($sponsorIds) {
            $q->whereIn('sponsor_id', $sponsorIds);
        })
            ->pending()
            ->dueBefore(now()->toDateString())
            ->count();

        // Exposure summary (last 30 days)
        $exposureSummary = SponsorExposureTracking::whereIn('sponsor_id', $sponsorIds)
            ->inDateRange(now()->subDays(30)->toDateString(), now()->toDateString())
            ->selectRaw('SUM(impressions_count) as total_impressions, SUM(clicks_count) as total_clicks')
            ->first();

        // Contracts by status
        $contractsByStatus = SponsorContract::whereIn('sponsor_id', $sponsorIds)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return ApiResponse::success([
            'has_sponsor_profile' => true,
            'sponsors_count' => $sponsorIds->count(),
            'active_contracts' => $activeContracts,
            'financial' => [
                'total_contract_value' => (float) $totalContractValue,
                'total_paid' => (float) $totalPaid,
                'total_remaining' => (float) ($totalContractValue - $totalPaid),
            ],
            'upcoming_payments' => $upcomingPayments,
            'overdue_payments_count' => $overdueCount,
            'exposure' => [
                'total_impressions' => (int) ($exposureSummary->total_impressions ?? 0),
                'total_clicks' => (int) ($exposureSummary->total_clicks ?? 0),
                'ctr' => $exposureSummary->total_impressions > 0
                    ? round(($exposureSummary->total_clicks / $exposureSummary->total_impressions) * 100, 2)
                    : 0,
            ],
            'contracts_by_status' => $contractsByStatus,
        ]);
    }
}
