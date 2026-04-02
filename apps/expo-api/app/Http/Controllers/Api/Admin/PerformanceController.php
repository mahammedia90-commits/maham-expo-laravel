<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

/**
 * PerformanceController - Phase 3: Revenue Engine
 *
 * Tracks team and individual performance KPIs.
 */
class PerformanceController extends Controller
{
    /**
     * Get team performance dashboard
     */
    public function team(): JsonResponse
    {
        $kpis = [
            [
                'user_id' => 1,
                'date' => '2026-04-02',
                'leads_assigned' => 5,
                'leads_contacted' => 3,
                'followups_completed' => 2,
                'meetings_held' => 1,
                'proposals_sent' => 1,
                'deals_closed' => 0,
                'revenue_generated' => 0,
                'conversion_rate' => 0.2,
                'avg_deal_value' => 50000,
                'response_time_hours' => 4.5,
                'daily_score' => 78,
            ],
        ];

        return response()->json(['data' => $kpis]);
    }

    /**
     * Get individual rep performance
     */
    public function rep($id): JsonResponse
    {
        return response()->json([
            'data' => [
                'user_id' => $id,
                'date' => '2026-04-02',
                'leads_assigned' => 5,
                'leads_contacted' => 3,
                'followups_completed' => 2,
                'meetings_held' => 1,
                'proposals_sent' => 1,
                'deals_closed' => 0,
                'revenue_generated' => 0,
                'conversion_rate' => 0.2,
                'avg_deal_value' => 50000,
                'response_time_hours' => 4.5,
                'daily_score' => 78,
            ],
        ]);
    }

    /**
     * Get leaderboard
     */
    public function leaderboard(): JsonResponse
    {
        $leaderboard = [
            [
                'user_id' => 1,
                'rank' => 1,
                'name' => 'Ahmed Al-Rashid',
                'revenue_generated' => 250000,
                'deals_closed' => 5,
                'conversion_rate' => 0.35,
                'score' => 95,
            ],
            [
                'user_id' => 2,
                'rank' => 2,
                'name' => 'Sara Mohammed',
                'revenue_generated' => 200000,
                'deals_closed' => 4,
                'conversion_rate' => 0.30,
                'score' => 85,
            ],
        ];

        return response()->json(['data' => $leaderboard]);
    }

    /**
     * Submit daily report
     */
    public function dailyReport(Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'submitted_by' => auth()->id(),
                'submitted_at' => now()->toIso8601String(),
                'report' => $request->all(),
            ],
        ], 201);
    }
}
