<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvestorProfile;
use App\Models\Contract;
use App\Models\Document;
use Illuminate\Http\JsonResponse;

class InvestorPortalController extends Controller
{
    /**
     * Investor portal dashboard with real database metrics
     */
    public function dashboard(): JsonResponse
    {
        $totalInvestors = InvestorProfile::count();
        $totalPortfolioValue = InvestorProfile::sum('portfolio_value') ?? 0;

        // Calculate average ROI from all investors
        $avgRoi = $totalInvestors > 0
            ? round(InvestorProfile::avg('roi_percentage'), 2)
            : 0;

        // Calculate growth rate from active vs pending
        $activeInvestors = InvestorProfile::where('status', 'active')->count();
        $growthRate = $totalInvestors > 0
            ? round(($activeInvestors / $totalInvestors) * 100, 2)
            : 0;

        return response()->json([
            'data' => [
                'total_investors' => $totalInvestors,
                'portfolio_value' => (float)$totalPortfolioValue,
                'roi' => $avgRoi,
                'growth_rate' => $growthRate,
                'active_investors' => $activeInvestors,
                'pending_investors' => $totalInvestors - $activeInvestors,
            ]
        ]);
    }

    /**
     * Get portfolio composition by sector
     */
    public function portfolio(): JsonResponse
    {
        $portfolios = InvestorProfile::select('name', 'name_en', 'sector', 'investment_amount', 'portfolio_value', 'roi_percentage', 'status')
            ->get()
            ->map(fn($investor) => [
                'investor_id' => $investor->id,
                'name' => $investor->name,
                'name_en' => $investor->name_en,
                'sector' => $investor->sector,
                'investment' => (float)$investor->investment_amount,
                'portfolio_value' => (float)$investor->portfolio_value,
                'roi' => (float)$investor->roi_percentage,
                'status' => $investor->status,
            ]);

        // Group by sector
        $bySector = $portfolios->groupBy('sector')->map(function($group) {
            return [
                'sector' => $group[0]['sector'],
                'count' => count($group),
                'total_value' => $group->sum('portfolio_value'),
                'avg_roi' => round($group->avg('roi'), 2),
                'investors' => $group->all(),
            ];
        })->values();

        return response()->json([
            'data' => [
                'total_portfolio' => $portfolios->sum('portfolio_value'),
                'by_sector' => $bySector,
                'all_investors' => $portfolios,
            ]
        ]);
    }

    /**
     * List all investors - paginated
     */
    public function investors(): JsonResponse
    {
        $investors = InvestorProfile::paginate(20);

        return response()->json([
            'data' => $investors->items(),
            'meta' => [
                'current_page' => $investors->currentPage(),
                'last_page' => $investors->lastPage(),
                'per_page' => $investors->perPage(),
                'total' => $investors->total(),
            ]
        ]);
    }

    /**
     * Get investment opportunities - contracts
     */
    public function opportunities(): JsonResponse
    {
        try {
            $contracts = Contract::where('type', 'sponsorship')
                ->orWhere('type', 'investment')
                ->with('investor')
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
     * Get investment contracts
     */
    public function contracts(): JsonResponse
    {
        try {
            $contracts = Contract::where('type', 'investment')
                ->orWhere('type', 'sponsorship')
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
     * Get investor messages/notifications
     */
    public function messages(): JsonResponse
    {
        // Get recent notifications (if available) or empty array
        $notifications = [];

        return response()->json([
            'data' => $notifications,
            'meta' => [
                'total' => 0,
                'unread' => 0,
            ]
        ]);
    }

    /**
     * Get due diligence documents from database
     */
    public function dueDiligence(): JsonResponse
    {
        $documents = Document::where('related_type', 'investment')
            ->orWhere('related_type', 'due_diligence')
            ->select('id', 'name', 'type', 'file_path', 'status', 'created_at')
            ->get()
            ->map(fn($doc) => [
                'id' => $doc->id,
                'title' => $doc->name,
                'type' => $doc->type,
                'status' => $doc->status,
                'uploaded_date' => $doc->created_at?->toDateString(),
            ]);

        return response()->json([
            'data' => $documents,
            'meta' => [
                'total' => $documents->count(),
            ]
        ]);
    }

    /**
     * Get risk management data from investors with low ROI
     */
    public function riskManagement(): JsonResponse
    {
        // Investors with below-average ROI are flagged as at-risk
        $avgRoi = InvestorProfile::avg('roi_percentage') ?? 0;

        $atRiskInvestors = InvestorProfile::where('roi_percentage', '<', $avgRoi)
            ->select('id', 'name', 'roi_percentage', 'portfolio_value', 'status')
            ->get()
            ->map(fn($investor) => [
                'investor_id' => $investor->id,
                'name' => $investor->name,
                'risk_type' => 'Low ROI Performance',
                'current_roi' => (float)$investor->roi_percentage,
                'average_roi' => round($avgRoi, 2),
                'level' => $investor->roi_percentage < $avgRoi * 0.7 ? 'high' : 'medium',
                'mitigation' => 'Portfolio rebalancing recommended',
                'portfolio_value' => (float)$investor->portfolio_value,
            ]);

        return response()->json([
            'data' => $atRiskInvestors,
            'meta' => [
                'total_at_risk' => $atRiskInvestors->count(),
                'average_roi' => round($avgRoi, 2),
            ]
        ]);
    }
}
