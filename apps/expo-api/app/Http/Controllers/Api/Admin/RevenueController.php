<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

/**
 * RevenueController - Phase 3: Revenue Engine
 *
 * Manages revenue dashboard, forecasting, and breakdown.
 */
class RevenueController extends Controller
{
    /**
     * Get revenue dashboard with key metrics
     */
    public function dashboard(): JsonResponse
    {
        $stats = [
            'today' => 15000,
            'this_month' => 450000,
            'this_quarter' => 1200000,
            'this_year' => 2500000,
            'monthly_target' => 500000,
            'by_type' => [
                'investor' => [
                    'amount' => 1500000,
                    'percentage' => 60,
                ],
                'merchant' => [
                    'amount' => 700000,
                    'percentage' => 28,
                ],
                'sponsor' => [
                    'amount' => 300000,
                    'percentage' => 12,
                ],
            ],
            'pipeline_value' => 750000,
            'forecast_30d' => 500000,
            'forecast_90d' => 1200000,
        ];

        return response()->json(['data' => $stats]);
    }

    /**
     * Get revenue forecast
     */
    public function forecast(): JsonResponse
    {
        return response()->json([
            'data' => [
                'forecast_30d' => 500000,
                'forecast_90d' => 1200000,
                'confidence' => 0.85,
            ],
        ]);
    }

    /**
     * Get revenue breakdown by type
     */
    public function byType(): JsonResponse
    {
        $breakdown = [
            [
                'type' => 'investor',
                'amount' => 1500000,
                'percentage' => 60,
                'count' => 12,
            ],
            [
                'type' => 'merchant',
                'amount' => 700000,
                'percentage' => 28,
                'count' => 35,
            ],
            [
                'type' => 'sponsor',
                'amount' => 300000,
                'percentage' => 12,
                'count' => 8,
            ],
        ];

        return response()->json(['data' => $breakdown]);
    }
}
