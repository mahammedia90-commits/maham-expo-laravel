<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

/**
 * WorkflowController - Phase 3: Revenue Engine
 *
 * Manages automated workflows triggered by events.
 */
class WorkflowController extends Controller
{
    /**
     * List all workflows
     */
    public function index(): JsonResponse
    {
        $workflows = [
            [
                'id' => 1,
                'name' => 'New Lead Notification',
                'trigger_type' => 'lead_created',
                'steps' => 3,
                'is_active' => true,
            ],
            [
                'id' => 2,
                'name' => 'Payment Overdue Warning',
                'trigger_type' => 'payment_overdue',
                'steps' => 2,
                'is_active' => true,
            ],
        ];

        return response()->json(['data' => $workflows]);
    }

    /**
     * Create workflow
     */
    public function store(Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'id' => 3,
                'name' => $request->get('name'),
                'trigger_type' => $request->get('trigger_type'),
                'is_active' => true,
            ],
        ], 201);
    }

    /**
     * Trigger workflow manually
     */
    public function trigger($id, Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'workflow_id' => $id,
                'triggered_at' => now()->toIso8601String(),
                'status' => 'running',
            ],
        ]);
    }

    /**
     * Get workflow logs
     */
    public function logs($id): JsonResponse
    {
        $logs = [
            [
                'id' => 1,
                'workflow_id' => $id,
                'event' => 'triggered',
                'status' => 'success',
                'timestamp' => '2026-04-02T10:00:00Z',
            ],
        ];

        return response()->json(['data' => $logs]);
    }
}
