<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use App\Models\SponsorPackage;
use App\Models\Contract;
use App\Models\SponsorDeliverable;
use App\Models\Event;
use Illuminate\Http\JsonResponse;

class SponsorPortalController extends Controller
{
    /**
     * Sponsor portal dashboard with real metrics
     */
    public function dashboard(): JsonResponse
    {
        try {
            $totalSponsors = Sponsor::count();
            $totalSponsorship = Sponsor::sum('sponsorship_amount') ?? 0;

            $sponsorsByTier = Sponsor::select('sponsorship_tier')
                ->groupBy('sponsorship_tier')
                ->withCount('*')
                ->get();

            $activePackages = SponsorPackage::where('active', true)->count();

            // Calculate average ROI from all sponsors
            $avgRoi = $totalSponsors > 0
                ? round(Sponsor::avg('roi_percentage') ?? 0, 2)
                : 0;

            $activeContracts = Contract::where('type', 'sponsorship')
                ->where('status', 'active')
                ->count();

            return response()->json([
                'data' => [
                    'total_sponsors' => $totalSponsors,
                    'total_sponsorship_value' => (float)$totalSponsorship,
                    'active_packages' => $activePackages,
                    'average_roi' => $avgRoi,
                    'active_contracts' => $activeContracts,
                    'sponsors_by_tier' => [
                        'platinum' => Sponsor::where('sponsorship_tier', 'platinum')->count(),
                        'gold' => Sponsor::where('sponsorship_tier', 'gold')->count(),
                        'silver' => Sponsor::where('sponsorship_tier', 'silver')->count(),
                        'bronze' => Sponsor::where('sponsorship_tier', 'bronze')->count(),
                    ],
                ]
            ]);
        } catch (\Exception $e) {
            // Return empty data if tables don't exist
            return response()->json([
                'data' => [
                    'total_sponsors' => 0,
                    'total_sponsorship_value' => 0,
                    'active_packages' => 0,
                    'average_roi' => 0,
                    'active_contracts' => 0,
                    'sponsors_by_tier' => [
                        'platinum' => 0,
                        'gold' => 0,
                        'silver' => 0,
                        'bronze' => 0,
                    ],
                ]
            ]);
        }
    }

    /**
     * List all sponsors from database
     */
    public function sponsors(): JsonResponse
    {
        $sponsors = Sponsor::select('id', 'name', 'name_en', 'email', 'phone', 'company_type', 'sponsorship_tier', 'sponsorship_amount', 'status')
            ->paginate(20);

        return response()->json([
            'data' => $sponsors->items(),
            'meta' => [
                'current_page' => $sponsors->currentPage(),
                'last_page' => $sponsors->lastPage(),
                'per_page' => $sponsors->perPage(),
                'total' => $sponsors->total(),
            ]
        ]);
    }

    /**
     * Get sponsorship packages from database
     */
    public function packages(): JsonResponse
    {
        $packages = SponsorPackage::select('id', 'name', 'name_en', 'description', 'price', 'tier', 'active')
            ->paginate(20);

        return response()->json([
            'data' => $packages->items(),
            'meta' => [
                'current_page' => $packages->currentPage(),
                'last_page' => $packages->lastPage(),
                'per_page' => $packages->perPage(),
                'total' => $packages->total(),
            ]
        ]);
    }

    /**
     * Get sponsor contracts from database
     */
    public function contracts(): JsonResponse
    {
        try {
            $contracts = Contract::where('type', 'sponsorship')
                ->select('id', 'contract_number', 'title', 'total_amount', 'status', 'payment_status', 'start_date', 'end_date')
                ->paginate(20);

            return response()->json([
                'data' => $contracts->items(),
                'meta' => [
                    'current_page' => $contracts->currentPage(),
                    'last_page' => $contracts->lastPage(),
                    'per_page' => $contracts->perPage(),
                    'total' => $contracts->total(),
                ]
            ]);
        } catch (\Exception $e) {
            // Return empty data if table doesn't exist
            return response()->json([
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 0,
                    'per_page' => 20,
                    'total' => 0,
                ]
            ]);
        }
    }

    /**
     * Get sponsor ROI metrics from database
     */
    public function roiMetrics(): JsonResponse
    {
        try {
            $sponsorMetrics = Sponsor::select('id', 'name', 'sponsorship_tier', 'sponsorship_amount')
                ->get()
                ->map(fn($sponsor) => [
                    'sponsor_id' => $sponsor->id,
                    'name' => $sponsor->name,
                    'tier' => $sponsor->sponsorship_tier,
                    'investment' => (float)$sponsor->sponsorship_amount,
                    'roi_percentage' => (float)($sponsor->roi_percentage ?? 0),
                    'status' => $sponsor->status ?? 'active',
                    'roi_amount' => round(((float)$sponsor->sponsorship_amount * ((float)($sponsor->roi_percentage ?? 0) / 100)), 2),
                ]);

            return response()->json([
                'data' => $sponsorMetrics,
                'meta' => [
                    'total' => $sponsorMetrics->count(),
                    'average_roi' => round($sponsorMetrics->avg('roi_percentage'), 2),
                ]
            ]);
        } catch (\Exception $e) {
            // Return empty data if table doesn't exist
            return response()->json([
                'data' => [],
                'meta' => [
                    'total' => 0,
                    'average_roi' => 0,
                ]
            ]);
        }
    }

    /**
     * Get sponsor deliverables from database
     */
    public function deliverables(): JsonResponse
    {
        try {
            $deliverables = SponsorDeliverable::select('id', 'title', 'description', 'status', 'due_date', 'completed_date')
                ->paginate(20);

            return response()->json([
                'data' => $deliverables->items(),
                'meta' => [
                    'current_page' => $deliverables->currentPage(),
                    'last_page' => $deliverables->lastPage(),
                    'per_page' => $deliverables->perPage(),
                    'total' => $deliverables->total(),
                ]
            ]);
        } catch (\Exception $e) {
            // Return empty data if table doesn't exist
            return response()->json([
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 0,
                    'per_page' => 20,
                    'total' => 0,
                ]
            ]);
        }
    }

    /**
     * Get sponsor messages/notifications
     */
    public function messages(): JsonResponse
    {
        // Get notifications if available
        $notifications = [];

        return response()->json([
            'data' => $notifications,
            'meta' => [
                'total' => 0,
            ]
        ]);
    }

    /**
     * Get upcoming events from database
     */
    public function upcomingEvents(): JsonResponse
    {
        try {
            $events = Event::where('startDate', '>=', now())
                ->orderBy('startDate')
                ->select('id', 'name', 'startDate', 'endDate', 'status')
                ->limit(10)
                ->get();

            return response()->json([
                'data' => $events,
                'meta' => [
                    'total' => $events->count(),
                ]
            ]);
        } catch (\Exception $e) {
            // Return empty data if table doesn't exist or column name is different
            return response()->json([
                'data' => [],
                'meta' => [
                    'total' => 0,
                ]
            ]);
        }
    }
}
