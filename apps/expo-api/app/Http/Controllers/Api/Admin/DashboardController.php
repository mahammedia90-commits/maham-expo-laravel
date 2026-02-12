<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\RentalRequest;
use App\Models\Space;
use App\Models\VisitRequest;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * Get admin dashboard statistics
     */
    public function index(Request $request): JsonResponse
    {
        $eventId = $request->input('event_id');
        $spacesPeriod = $request->input('spaces_period', 'all');
        $revenuePeriod = $request->input('revenue_period', 'all');

        return ApiResponse::success([
            'overview' => $this->getOverview($eventId),
            'spaces' => $this->getSpacesStats($spacesPeriod, $eventId),
            'revenue' => $this->getRevenueStats($revenuePeriod, $eventId),
            'visit_requests' => $this->getVisitRequestsStats($eventId),
            'rental_requests' => $this->getRentalRequestsStats($eventId),
            'recent_activity' => $this->getRecentActivity(),
        ]);
    }

    /**
     * Overview statistics
     */
    private function getOverview(?string $eventId): array
    {
        $spacesQuery = Space::query();
        $visitQuery = VisitRequest::query();
        $rentalQuery = RentalRequest::query();

        if ($eventId) {
            $spacesQuery->where('event_id', $eventId);
            $visitQuery->where('event_id', $eventId);
            $rentalQuery->whereHas('space', fn($q) => $q->where('event_id', $eventId));
        }

        return [
            'total_revenue' => (float) $rentalQuery->clone()->sum('paid_amount'),
            'total_spaces' => $spacesQuery->count(),
            'total_visit_requests' => $visitQuery->count(),
            'total_rental_requests' => $rentalQuery->count(),
        ];
    }

    /**
     * Spaces grouped by status with percentages
     */
    private function getSpacesStats(string $period, ?string $eventId): array
    {
        $query = Space::query();

        if ($eventId) {
            $query->where('event_id', $eventId);
        }

        $this->applyPeriodFilter($query, $period);

        $statusCounts = $query->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $total = $statusCounts->sum();

        $statusLabels = [
            'rented' => ['ar' => 'المؤجّرة', 'en' => 'Rented'],
            'available' => ['ar' => 'المتاحة', 'en' => 'Available'],
            'reserved' => ['ar' => 'قيد المراجعة', 'en' => 'Reserved'],
        ];

        $byStatus = [];
        foreach ($statusLabels as $status => $labels) {
            $count = $statusCounts->get($status, 0);
            $byStatus[] = [
                'status' => $status,
                'label' => $labels['ar'],
                'label_en' => $labels['en'],
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100) : 0,
            ];
        }

        return [
            'total' => $total,
            'by_status' => $byStatus,
        ];
    }

    /**
     * Revenue grouped by payment status
     */
    private function getRevenueStats(string $period, ?string $eventId): array
    {
        $query = RentalRequest::query();

        if ($eventId) {
            $query->whereHas('space', fn($q) => $q->where('event_id', $eventId));
        }

        $this->applyPeriodFilter($query, $period);

        $stats = $query->selectRaw('payment_status, count(*) as count, coalesce(sum(paid_amount), 0) as amount')
            ->groupBy('payment_status')
            ->get()
            ->keyBy('payment_status');

        $totalAmount = $stats->sum('amount');
        $totalCount = $stats->sum('count');

        $statusLabels = [
            'paid' => ['ar' => 'المكتملة', 'en' => 'Completed'],
            'pending' => ['ar' => 'المعلّقة', 'en' => 'Pending'],
            'partial' => ['ar' => 'قيد المعالجة', 'en' => 'Processing'],
        ];

        $byPaymentStatus = [];
        foreach ($statusLabels as $status => $labels) {
            $item = $stats->get($status);
            $count = $item?->count ?? 0;
            $amount = (float) ($item?->amount ?? 0);
            $byPaymentStatus[] = [
                'status' => $status,
                'label' => $labels['ar'],
                'label_en' => $labels['en'],
                'count' => $count,
                'amount' => $amount,
                'percentage' => $totalCount > 0 ? round(($count / $totalCount) * 100) : 0,
            ];
        }

        return [
            'total' => $totalAmount,
            'by_payment_status' => $byPaymentStatus,
        ];
    }

    /**
     * Visit requests summary
     */
    private function getVisitRequestsStats(?string $eventId): array
    {
        $query = VisitRequest::query();

        if ($eventId) {
            $query->where('event_id', $eventId);
        }

        $counts = $query->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return [
            'total' => $counts->sum(),
            'pending' => $counts->get('pending', 0),
            'approved' => $counts->get('approved', 0),
            'rejected' => $counts->get('rejected', 0),
            'completed' => $counts->get('completed', 0),
            'cancelled' => $counts->get('cancelled', 0),
        ];
    }

    /**
     * Rental requests summary
     */
    private function getRentalRequestsStats(?string $eventId): array
    {
        $query = RentalRequest::query();

        if ($eventId) {
            $query->whereHas('space', fn($q) => $q->where('event_id', $eventId));
        }

        $counts = $query->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return [
            'total' => $counts->sum(),
            'pending' => $counts->get('pending', 0),
            'approved' => $counts->get('approved', 0),
            'rejected' => $counts->get('rejected', 0),
            'completed' => $counts->get('completed', 0),
            'cancelled' => $counts->get('cancelled', 0),
        ];
    }

    /**
     * Recent activity counts
     */
    private function getRecentActivity(): array
    {
        $lastWeek = Carbon::now()->subWeek();

        return [
            'latest_visit_requests' => VisitRequest::where('created_at', '>=', $lastWeek)->count(),
            'latest_rental_requests' => RentalRequest::where('created_at', '>=', $lastWeek)->count(),
            'latest_profiles_pending' => BusinessProfile::where('status', 'pending')->count(),
        ];
    }

    /**
     * Apply period filter to query
     */
    private function applyPeriodFilter($query, string $period): void
    {
        match ($period) {
            'today' => $query->where('created_at', '>=', Carbon::today()),
            'week' => $query->where('created_at', '>=', Carbon::now()->startOfWeek()),
            'month' => $query->where('created_at', '>=', Carbon::now()->startOfMonth()),
            'year' => $query->where('created_at', '>=', Carbon::now()->startOfYear()),
            default => null,
        };
    }
}
