<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

/**
 * PipelineController - Phase 3: Revenue Engine
 *
 * Manages sales pipeline with stages, deals, and forecasting.
 */
class PipelineController extends Controller
{
    /**
     * Get pipeline with all stages and deals
     */
    public function index(): JsonResponse
    {
        $stages = \App\Models\PipelineStage::with(['deals' => function ($q) {
            $q->selectRaw('stage_id, COUNT(*) as deal_count, SUM(value) as total_value');
            $q->groupBy('stage_id');
        }])->orderBy('order')->get();

        $stagesWithCounts = $stages->map(function ($stage) {
            $dealsCount = \App\Models\Deal::where('stage_id', $stage->id)->count();
            $dealsValue = \App\Models\Deal::where('stage_id', $stage->id)->sum('value');

            return array_merge($stage->toArray(), [
                'deals_count' => $dealsCount,
                'deals_value' => $dealsValue,
            ]);
        });

        return response()->json(['data' => $stagesWithCounts]);
    }

    /**
     * Create deal
     */
    public function storeDeal(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->all()], 201);
    }

    /**
     * Move deal to different stage
     */
    public function moveStage($dealId, Request $request): JsonResponse
    {
        $stageId = $request->get('stage_id');

        return response()->json([
            'data' => [
                'deal_id' => $dealId,
                'stage_id' => $stageId,
                'moved_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Get at-risk deals
     */
    public function atRisk(): JsonResponse
    {
        $deals = [
            [
                'id' => 101,
                'lead_id' => 1,
                'stage_id' => 2,
                'value' => 75000,
                'close_probability' => 35,
                'assigned_to' => 1,
                'days_in_stage' => 8,
                'last_activity' => '2026-03-25',
                'is_overdue' => true,
                'is_at_risk' => true,
            ],
        ];

        return response()->json(['data' => $deals]);
    }

    /**
     * Log activity for deal
     */
    public function logActivity($dealId, Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'deal_id' => $dealId,
                'activity_type' => $request->get('type'),
                'logged_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Get followups list
     */
    public function followups(Request $request): JsonResponse
    {
        $followups = [
            [
                'id' => 1,
                'lead_id' => 1,
                'deal_id' => 101,
                'due_date' => '2026-04-15',
                'type' => 'call',
                'status' => 'pending',
                'outcome' => null,
                'assigned_to' => 1,
            ],
        ];

        return response()->json([
            'data' => $followups,
            'meta' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 20,
                'total' => 1,
            ],
        ]);
    }

    /**
     * Create followup
     */
    public function storeFollowup(Request $request): JsonResponse
    {
        return response()->json(['data' => $request->all()], 201);
    }

    /**
     * Mark followup as complete
     */
    public function completeFollowup($id, Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'id' => $id,
                'status' => 'completed',
                'outcome' => $request->get('outcome'),
                'completed_at' => now()->toIso8601String(),
            ],
        ]);
    }
}
