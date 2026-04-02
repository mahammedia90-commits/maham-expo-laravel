<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

/**
 * EnforcementController - Phase 3: Revenue Engine
 *
 * Manages idle detection and enforcement warnings.
 */
class EnforcementController extends Controller
{
    /**
     * Get idle users
     */
    public function idle(): JsonResponse
    {
        $idle = [
            [
                'user_id' => 5,
                'name' => 'Inactive User',
                'last_activity' => '2026-03-25',
                'days_idle' => 8,
                'last_action' => 'login',
            ],
        ];

        return response()->json(['data' => $idle]);
    }

    /**
     * Issue warning to user
     */
    public function issueWarning($userId, Request $request): JsonResponse
    {
        return response()->json([
            'data' => [
                'user_id' => $userId,
                'warning_type' => $request->get('type', 'idle'),
                'issued_at' => now()->toIso8601String(),
            ],
        ], 201);
    }

    /**
     * Get all warnings
     */
    public function warnings(): JsonResponse
    {
        $warnings = [
            [
                'id' => 1,
                'user_id' => 5,
                'type' => 'idle',
                'issued_at' => '2026-04-01T10:00:00Z',
                'acknowledged' => false,
            ],
        ];

        return response()->json(['data' => $warnings]);
    }

    /**
     * Get task queue
     */
    public function taskQueue(): JsonResponse
    {
        $tasks = [
            [
                'id' => 1,
                'assigned_to' => 1,
                'title' => 'Follow up on lead',
                'status' => 'pending',
                'due_date' => '2026-04-05',
                'priority' => 'high',
            ],
        ];

        return response()->json(['data' => $tasks]);
    }
}
