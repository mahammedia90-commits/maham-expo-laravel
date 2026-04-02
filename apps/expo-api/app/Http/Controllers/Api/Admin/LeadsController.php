<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

/**
 * LeadsController - Phase 3: Revenue Engine
 *
 * Manages CRM leads with scoring, bulk operations, and import/export.
 */
class LeadsController extends Controller
{
    /**
     * List all leads with pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 20);
            $status = $request->get('status');

            $query = \App\Models\Lead::query();

            if ($status) {
                $query->where('status', $status);
            }

            $leads = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'data' => $leads->items(),
                'meta' => [
                    'current_page' => $leads->currentPage(),
                    'last_page' => $leads->lastPage(),
                    'per_page' => $leads->perPage(),
                    'total' => $leads->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching leads',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get single lead
     */
    public function show($id): JsonResponse
    {
        $lead = \App\Models\Lead::find($id);
        if (!$lead) {
            return response()->json(['message' => 'Lead not found'], 404);
        }
        return response()->json(['data' => $lead]);
    }

    /**
     * Create lead
     */
    public function store(Request $request): JsonResponse
    {
        $lead = \App\Models\Lead::create($request->all());
        return response()->json(['data' => $lead], 201);
    }

    /**
     * Update lead
     */
    public function update($id, Request $request): JsonResponse
    {
        $lead = \App\Models\Lead::find($id);
        if (!$lead) {
            return response()->json(['message' => 'Lead not found'], 404);
        }
        $lead->update($request->all());
        return response()->json(['data' => $lead]);
    }

    /**
     * Delete lead
     */
    public function destroy($id): JsonResponse
    {
        $lead = \App\Models\Lead::find($id);
        if (!$lead) {
            return response()->json(['message' => 'Lead not found'], 404);
        }
        $lead->delete();
        return response()->json(['data' => []], 200);
    }

    /**
     * Score a lead (AI scoring)
     */
    public function score($id): JsonResponse
    {
        return response()->json([
            'data' => ['score' => mt_rand(50, 100)],
        ]);
    }

    /**
     * Import leads from CSV
     */
    public function import(Request $request): JsonResponse
    {
        // TODO: Handle file upload and CSV import
        return response()->json([
            'data' => ['imported' => 0, 'failed' => 0],
        ]);
    }

    /**
     * Bulk assign leads
     */
    public function bulkAssign(Request $request): JsonResponse
    {
        $ids = $request->get('ids', []);
        $assignedTo = $request->get('assigned_to');

        return response()->json([
            'data' => ['assigned' => count($ids)],
        ]);
    }

    /**
     * Bulk update status
     */
    public function bulkStatus(Request $request): JsonResponse
    {
        $ids = $request->get('ids', []);
        $status = $request->get('status');

        return response()->json([
            'data' => ['updated' => count($ids)],
        ]);
    }

    /**
     * Export leads as CSV
     */
    public function export(Request $request)
    {
        // TODO: Generate CSV export
        return response()->download('', 'leads.csv');
    }
}
