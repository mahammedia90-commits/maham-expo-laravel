<?php

namespace App\Http\Controllers\Api\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use App\Models\SponsorBenefit;
use App\Models\SponsorContract;
use App\Models\SponsorExposureTracking;
use App\Models\SponsorPayment;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    /**
     * Sponsor statistics overview
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');
        $sponsorIds = Sponsor::forUser($userId)->pluck('id');

        // Contracts stats
        $totalContracts = SponsorContract::whereIn('sponsor_id', $sponsorIds)->count();
        $activeContracts = SponsorContract::whereIn('sponsor_id', $sponsorIds)->active()->count();

        // Financial stats
        $totalValue = SponsorContract::whereIn('sponsor_id', $sponsorIds)
            ->whereIn('status', ['active', 'completed'])
            ->sum('total_amount');
        $totalPaid = SponsorContract::whereIn('sponsor_id', $sponsorIds)
            ->whereIn('status', ['active', 'completed'])
            ->sum('paid_amount');

        // Payment stats
        $pendingPayments = SponsorPayment::whereHas('contract', fn($q) => $q->whereIn('sponsor_id', $sponsorIds))
            ->pending()
            ->count();
        $overduePayments = SponsorPayment::whereHas('contract', fn($q) => $q->whereIn('sponsor_id', $sponsorIds))
            ->pending()
            ->dueBefore(now()->toDateString())
            ->count();

        // Benefits stats
        $totalBenefits = SponsorBenefit::whereHas('contract', fn($q) => $q->whereIn('sponsor_id', $sponsorIds))
            ->count();
        $deliveredBenefits = SponsorBenefit::whereHas('contract', fn($q) => $q->whereIn('sponsor_id', $sponsorIds))
            ->delivered()
            ->count();

        // Exposure stats (last 30 days)
        $exposure = SponsorExposureTracking::whereIn('sponsor_id', $sponsorIds)
            ->inDateRange(now()->subDays(30)->toDateString(), now()->toDateString())
            ->selectRaw('SUM(impressions_count) as impressions, SUM(clicks_count) as clicks')
            ->first();

        // Events participated
        $eventsCount = SponsorContract::whereIn('sponsor_id', $sponsorIds)
            ->whereIn('status', ['active', 'completed'])
            ->distinct('event_id')
            ->count('event_id');

        return ApiResponse::success([
            'contracts' => [
                'total' => $totalContracts,
                'active' => $activeContracts,
            ],
            'financial' => [
                'total_value' => (float) $totalValue,
                'total_paid' => (float) $totalPaid,
                'total_remaining' => (float) ($totalValue - $totalPaid),
                'payment_progress' => $totalValue > 0
                    ? round(($totalPaid / $totalValue) * 100, 1)
                    : 0,
            ],
            'payments' => [
                'pending' => $pendingPayments,
                'overdue' => $overduePayments,
            ],
            'benefits' => [
                'total' => $totalBenefits,
                'delivered' => $deliveredBenefits,
                'delivery_rate' => $totalBenefits > 0
                    ? round(($deliveredBenefits / $totalBenefits) * 100, 1)
                    : 0,
            ],
            'exposure_30d' => [
                'impressions' => (int) ($exposure->impressions ?? 0),
                'clicks' => (int) ($exposure->clicks ?? 0),
                'ctr' => ($exposure->impressions ?? 0) > 0
                    ? round(($exposure->clicks / $exposure->impressions) * 100, 2)
                    : 0,
            ],
            'events_count' => $eventsCount,
        ]);
    }
}
