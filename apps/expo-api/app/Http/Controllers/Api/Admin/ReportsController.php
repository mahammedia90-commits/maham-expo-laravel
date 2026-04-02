<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * ReportsController - Phase 3: Reporting Engine
 *
 * Provides financial, event, user, and audit reports with export capabilities.
 */
class ReportsController extends Controller
{
    /**
     * Get financial reports
     */
    public function financial(Request $request): JsonResponse
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $type = $request->get('type', 'summary'); // summary, detailed, by_category

        // Mock data - replace with actual DB queries
        $report = [
            'period' => [
                'start' => $startDate ?? date('Y-m-01'),
                'end' => $endDate ?? date('Y-m-d'),
            ],
            'summary' => [
                'total_revenue' => 2500000,
                'total_expenses' => 1200000,
                'profit' => 1300000,
                'profit_margin' => 52,
            ],
            'by_category' => [
                [
                    'category' => 'Ticket Sales',
                    'revenue' => 1500000,
                    'percentage' => 60,
                ],
                [
                    'category' => 'Sponsorship',
                    'revenue' => 700000,
                    'percentage' => 28,
                ],
                [
                    'category' => 'Merchandise',
                    'revenue' => 300000,
                    'percentage' => 12,
                ],
            ],
            'trends' => [
                ['date' => date('Y-m-d', strtotime('-30 days')), 'revenue' => 80000],
                ['date' => date('Y-m-d', strtotime('-20 days')), 'revenue' => 90000],
                ['date' => date('Y-m-d', strtotime('-10 days')), 'revenue' => 100000],
                ['date' => date('Y-m-d'), 'revenue' => 110000],
            ],
        ];

        return response()->json(['data' => $report]);
    }

    /**
     * Get event reports
     */
    public function events(Request $request): JsonResponse
    {
        $status = $request->get('status');
        $type = $request->get('type', 'summary');

        $report = [
            'total_events' => 15,
            'active_events' => 8,
            'completed_events' => 5,
            'cancelled_events' => 2,
            'events' => [
                [
                    'id' => 1,
                    'name' => 'Maham Expo 2026',
                    'status' => 'active',
                    'attendees' => 5000,
                    'revenue' => 500000,
                    'date' => '2026-05-15',
                ],
                [
                    'id' => 2,
                    'name' => 'Tech Summit',
                    'status' => 'completed',
                    'attendees' => 3000,
                    'revenue' => 300000,
                    'date' => '2026-04-10',
                ],
            ],
            'metrics' => [
                'avg_attendance' => 4000,
                'avg_revenue' => 450000,
                'total_attendees' => 60000,
            ],
        ];

        return response()->json(['data' => $report]);
    }

    /**
     * Get user reports
     */
    public function users(Request $request): JsonResponse
    {
        $role = $request->get('role');
        $status = $request->get('status');

        $report = [
            'total_users' => 250,
            'active_users' => 180,
            'inactive_users' => 70,
            'by_role' => [
                [
                    'role' => 'super_admin',
                    'count' => 3,
                    'percentage' => 1.2,
                ],
                [
                    'role' => 'department_manager',
                    'count' => 15,
                    'percentage' => 6,
                ],
                [
                    'role' => 'staff',
                    'count' => 232,
                    'percentage' => 92.8,
                ],
            ],
            'activity' => [
                'logged_in_today' => 120,
                'logged_in_this_week' => 170,
                'never_logged_in' => 5,
            ],
        ];

        return response()->json(['data' => $report]);
    }

    /**
     * Get audit logs
     */
    public function auditLogs(Request $request): JsonResponse
    {
        $action = $request->get('action');
        $userId = $request->get('user_id');
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 20);

        $logs = [
            [
                'id' => 101,
                'action' => 'create',
                'resource' => 'Event',
                'resource_id' => 5,
                'user_id' => 1,
                'user_name' => 'Ahmed Admin',
                'ip_address' => '192.168.1.100',
                'changes' => ['name' => 'Maham Expo 2026', 'status' => 'draft'],
                'timestamp' => date('Y-m-d H:i:s', strtotime('-1 hour')),
            ],
            [
                'id' => 100,
                'action' => 'update',
                'resource' => 'User',
                'resource_id' => 10,
                'user_id' => 1,
                'user_name' => 'Ahmed Admin',
                'ip_address' => '192.168.1.100',
                'changes' => ['role' => ['staff' => 'department_manager']],
                'timestamp' => date('Y-m-d H:i:s', strtotime('-2 hours')),
            ],
        ];

        return response()->json([
            'data' => $logs,
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => 250,
            ],
        ]);
    }

    /**
     * Export report to CSV/PDF
     */
    public function export(Request $request, string $type): JsonResponse
    {
        $format = $request->get('format', 'csv'); // csv, pdf, xlsx
        $dateRange = $request->get('date_range', '30'); // 7, 30, 90, 365

        // In production, generate actual file and return download URL
        // For now, return success response
        return response()->json([
            'success' => true,
            'message' => "Report exported as {$format}",
            'download_url' => "https://api.mahamexpo.sa/reports/exports/report_{$type}_" . date('Y-m-d') . ".{$format}",
            'file_name' => "report_{$type}_" . date('Y-m-d') . ".{$format}",
        ]);
    }
}
