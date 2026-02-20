<?php

namespace App\Http\Controllers\Api\Investor;

use App\Http\Controllers\Controller;
use App\Models\PageView;
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
     * Get investor dashboard statistics
     * Shows stats only for investor's own spaces
     */
    public function index(Request $request): JsonResponse
    {
        $investorId = $request->input('auth_user_id');
        $period = $request->input('period', 'all');

        return ApiResponse::success([
            'overview' => $this->getOverview($investorId),
            'spaces' => $this->getSpacesStats($investorId, $period),
            'revenue' => $this->getRevenueStats($investorId, $period),
            'requests' => $this->getRequestsStats($investorId),
            'analytics' => $this->getAnalytics($investorId, $period),
            'recent_activity' => $this->getRecentActivity($investorId),
        ]);
    }

    /**
     * Overview statistics for investor's spaces
     */
    private function getOverview(string $investorId): array
    {
        $spacesQuery = Space::where('investor_id', $investorId);
        $spaceIds = $spacesQuery->pluck('id');

        $rentalQuery = RentalRequest::whereIn('space_id', $spaceIds);

        return [
            'total_revenue' => (float) $rentalQuery->clone()->sum('paid_amount'),
            'pending_revenue' => (float) $rentalQuery->clone()
                ->whereIn('status', ['approved', 'completed'])
                ->selectRaw('SUM(total_price - paid_amount) as pending')
                ->value('pending') ?? 0,
            'total_spaces' => $spacesQuery->count(),
            'available_spaces' => $spacesQuery->clone()->where('status', 'available')->count(),
            'rented_spaces' => $spacesQuery->clone()->where('status', 'rented')->count(),
            'pending_requests' => $rentalQuery->clone()->where('status', 'pending')->count(),
        ];
    }

    /**
     * Spaces grouped by status for investor
     */
    private function getSpacesStats(string $investorId, string $period): array
    {
        $query = Space::where('investor_id', $investorId);

        $this->applyPeriodFilter($query, $period);

        $statusCounts = $query->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $total = $statusCounts->sum();

        $statusLabels = [
            'rented' => ['ar' => 'المؤجّرة', 'en' => 'Rented'],
            'available' => ['ar' => 'المتاحة', 'en' => 'Available'],
            'reserved' => ['ar' => 'محجوزة', 'en' => 'Reserved'],
            'unavailable' => ['ar' => 'غير متاحة', 'en' => 'Unavailable'],
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
     * Revenue stats for investor's spaces
     */
    private function getRevenueStats(string $investorId, string $period): array
    {
        $spaceIds = Space::where('investor_id', $investorId)->pluck('id');
        $query = RentalRequest::whereIn('space_id', $spaceIds);

        $this->applyPeriodFilter($query, $period);

        $stats = $query->selectRaw('payment_status, count(*) as count, coalesce(sum(paid_amount), 0) as amount, coalesce(sum(total_price), 0) as total_price')
            ->groupBy('payment_status')
            ->get()
            ->keyBy('payment_status');

        $totalPaid = $stats->sum('amount');
        $totalExpected = $stats->sum('total_price');

        $statusLabels = [
            'paid' => ['ar' => 'مكتملة الدفع', 'en' => 'Fully Paid'],
            'partial' => ['ar' => 'دفع جزئي', 'en' => 'Partial Payment'],
            'pending' => ['ar' => 'بانتظار الدفع', 'en' => 'Pending Payment'],
        ];

        $byPaymentStatus = [];
        foreach ($statusLabels as $status => $labels) {
            $item = $stats->get($status);
            $byPaymentStatus[] = [
                'status' => $status,
                'label' => $labels['ar'],
                'label_en' => $labels['en'],
                'count' => $item?->count ?? 0,
                'amount' => (float) ($item?->amount ?? 0),
            ];
        }

        return [
            'total_paid' => $totalPaid,
            'total_expected' => $totalExpected,
            'pending_amount' => $totalExpected - $totalPaid,
            'by_payment_status' => $byPaymentStatus,
        ];
    }

    /**
     * Request stats for investor's spaces
     */
    private function getRequestsStats(string $investorId): array
    {
        $spaceIds = Space::where('investor_id', $investorId)->pluck('id');

        // Rental requests needing investor approval
        $rentalPending = RentalRequest::whereIn('space_id', $spaceIds)
            ->where('investor_status', 'pending')
            ->count();

        // Visit requests for investor's spaces (if space_id exists)
        $visitPending = 0;
        $visitTotal = 0;

        return [
            'rental' => [
                'pending_approval' => $rentalPending,
                'total' => RentalRequest::whereIn('space_id', $spaceIds)->count(),
            ],
            'visits' => [
                'pending_approval' => $visitPending,
                'total' => $visitTotal,
            ],
        ];
    }

    /**
     * Analytics for investor's spaces
     */
    private function getAnalytics(string $investorId, string $period): array
    {
        $spaceIds = Space::where('investor_id', $investorId)->pluck('id');

        $viewsQuery = PageView::where('viewable_type', Space::class)
            ->whereIn('viewable_id', $spaceIds);

        $this->applyPeriodFilter($viewsQuery, $period);

        // Views by platform
        $viewsByPlatform = $viewsQuery->clone()
            ->selectRaw('platform, count(*) as count')
            ->groupBy('platform')
            ->pluck('count', 'platform');

        // Top viewed spaces
        $topSpaces = $viewsQuery->clone()
            ->selectRaw('viewable_id, count(*) as views')
            ->groupBy('viewable_id')
            ->orderByDesc('views')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $space = Space::find($item->viewable_id);
                return [
                    'space_id' => $item->viewable_id,
                    'space_name' => $space?->name,
                    'views' => $item->views,
                ];
            });

        return [
            'total_views' => $viewsQuery->count(),
            'by_platform' => [
                'web' => $viewsByPlatform->get('web', 0),
                'mobile' => $viewsByPlatform->get('mobile', 0),
                'api' => $viewsByPlatform->get('api', 0),
            ],
            'top_spaces' => $topSpaces,
        ];
    }

    /**
     * Recent activity for investor
     */
    private function getRecentActivity(string $investorId): array
    {
        $spaceIds = Space::where('investor_id', $investorId)->pluck('id');

        return [
            'latest_rentals' => RentalRequest::whereIn('space_id', $spaceIds)
                ->with(['space:id,name,name_ar', 'businessProfile:id,company_name,company_name_ar'])
                ->latest()
                ->limit(5)
                ->get()
                ->map(fn($r) => [
                    'id' => $r->id,
                    'request_number' => $r->request_number,
                    'space' => $r->space?->name,
                    'company' => $r->businessProfile?->company_name,
                    'status' => $r->status->value,
                    'investor_status' => $r->investor_status ?? 'pending',
                    'total_price' => $r->total_price,
                    'created_at' => $r->created_at->toISOString(),
                ]),
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
