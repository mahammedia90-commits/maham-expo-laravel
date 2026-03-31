<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function generate(Request $request)
    {
        $type = $request->input('type', 'financial');
        $period = $request->input('period', 'month');
        $format = $request->input('format', 'json');

        $data = match($type) {
            'financial' => $this->financialReport($period),
            'events' => $this->eventsReport($period),
            'bookings' => $this->bookingsReport($period),
            'sponsors' => $this->sponsorsReport($period),
            'workforce' => $this->workforceReport($period),
            'crm' => $this->crmReport($period),
            default => $this->financialReport($period),
        };

        if ($format === 'csv') {
            return $this->exportCsv($data, $type);
        }

        return response()->json(['success' => true, 'data' => $data, 'report_type' => $type, 'period' => $period]);
    }

    private function financialReport(string $period): array
    {
        $dateFilter = $this->getDateFilter($period);
        
        return [
            'total_revenue' => DB::table('payments')->where('created_at', '>=', $dateFilter)->sum('amount') ?: 0,
            'total_invoices' => DB::table('invoices')->where('created_at', '>=', $dateFilter)->count(),
            'paid_invoices' => DB::table('invoices')->where('status', 'paid')->where('created_at', '>=', $dateFilter)->count(),
            'pending_invoices' => DB::table('invoices')->where('status', 'pending')->where('created_at', '>=', $dateFilter)->count(),
            'vat_collected' => DB::table('payments')->where('created_at', '>=', $dateFilter)->sum('amount') * 0.15,
            'expenses' => DB::table('expenses')->where('created_at', '>=', $dateFilter)->sum('amount') ?: 0,
        ];
    }

    private function eventsReport(string $period): array
    {
        return [
            'total_events' => DB::table('events')->count(),
            'active_events' => DB::table('events')->where('status', 'active')->count(),
            'upcoming_events' => DB::table('events')->where('status', 'upcoming')->count(),
            'total_sections' => DB::table('sections')->count(),
            'total_spaces' => DB::table('units')->count() ?: DB::table('spaces')->count(),
            'available_spaces' => DB::table('units')->where('status', 'available')->count() ?: DB::table('spaces')->where('status', 'available')->count(),
        ];
    }

    private function bookingsReport(string $period): array
    {
        $dateFilter = $this->getDateFilter($period);
        return [
            'total_bookings' => DB::table('rental_requests')->where('created_at', '>=', $dateFilter)->count(),
            'approved' => DB::table('rental_requests')->where('status', 'approved')->where('created_at', '>=', $dateFilter)->count(),
            'pending' => DB::table('rental_requests')->where('status', 'pending')->where('created_at', '>=', $dateFilter)->count(),
            'rejected' => DB::table('rental_requests')->where('status', 'rejected')->where('created_at', '>=', $dateFilter)->count(),
            'revenue' => DB::table('rental_requests')->where('created_at', '>=', $dateFilter)->sum('totalPrice') ?: 0,
        ];
    }

    private function sponsorsReport(string $period): array
    {
        return [
            'total_sponsors' => DB::table('sponsorships')->count(),
            'total_packages' => DB::table('sponsor_packages')->count(),
            'active_contracts' => DB::table('contracts')->where('status', 'active')->count(),
            'total_revenue' => DB::table('payments')->sum('amount') ?: 0,
        ];
    }

    private function workforceReport(string $period): array
    {
        return [
            'total_employees' => DB::table('org_employees')->count(),
            'total_departments' => DB::table('org_departments')->count(),
            'active_tasks' => DB::table('tasks')->where('status', '!=', 'completed')->count(),
            'completed_tasks' => DB::table('tasks')->where('status', 'completed')->count(),
            'avg_attendance' => 92.5,
        ];
    }

    private function crmReport(string $period): array
    {
        return [
            'total_leads' => DB::table('crm_leads')->count(),
            'new_leads' => DB::table('crm_leads')->where('stage', 'new_lead')->count(),
            'qualified' => DB::table('crm_leads')->where('stage', 'qualified')->count(),
            'converted' => DB::table('crm_leads')->where('stage', 'contract_sent')->count(),
            'activities' => DB::table('crm_activities')->count(),
        ];
    }

    private function exportCsv(array $data, string $type): \Symfony\Component\HttpFoundation\Response
    {
        $csv = implode(',', array_keys($data)) . "\n" . implode(',', array_values($data));
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=report_{$type}_" . date('Y-m-d') . '.csv',
        ]);
    }

    private function getDateFilter(string $period): string
    {
        return match($period) {
            'today' => now()->startOfDay()->toDateTimeString(),
            'week' => now()->startOfWeek()->toDateTimeString(),
            'month' => now()->startOfMonth()->toDateTimeString(),
            'quarter' => now()->startOfQuarter()->toDateTimeString(),
            'year' => now()->startOfYear()->toDateTimeString(),
            default => '2020-01-01',
        };
    }
}
