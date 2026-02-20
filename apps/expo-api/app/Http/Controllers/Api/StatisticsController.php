<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\City;
use App\Models\Event;
use App\Models\RentalRequest;
use App\Models\Service;
use App\Models\Space;
use App\Models\VisitRequest;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class StatisticsController extends Controller
{
    /**
     * Platform overview statistics (public)
     *
     * GET /api/v1/statistics
     */
    public function index(): JsonResponse
    {
        return ApiResponse::success([
            'platform' => $this->getPlatformStats(),
            'requests' => $this->getRequestsStats(),
            'events_by_category' => $this->getEventsByCategory(),
            'events_by_city' => $this->getEventsByCity(),
            'spaces_by_status' => $this->getSpacesByStatus(),
        ], 'إحصائيات المنصة');
    }

    /**
     * Event statistics (public)
     *
     * GET /api/v1/statistics/events
     */
    public function events(): JsonResponse
    {
        return ApiResponse::success([
            'total' => Event::published()->count(),
            'active' => Event::active()->count(),
            'upcoming' => Event::upcoming()->count(),
            'ongoing' => Event::ongoing()->count(),
            'by_category' => $this->getEventsByCategory(),
            'by_city' => $this->getEventsByCity(),
            'recent' => Event::published()
                ->select('id', 'name', 'name_ar', 'start_date', 'end_date', 'status')
                ->latest('start_date')
                ->limit(10)
                ->get()
                ->map(fn($e) => [
                    'id' => $e->id,
                    'name' => $e->name,
                    'name_ar' => $e->name_ar,
                    'start_date' => $e->start_date?->toDateString(),
                    'end_date' => $e->end_date?->toDateString(),
                ]),
        ], 'إحصائيات الفعاليات');
    }

    /**
     * Space statistics (public)
     *
     * GET /api/v1/statistics/spaces
     */
    public function spaces(): JsonResponse
    {
        $total = Space::count();

        $byStatus = Space::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $statusLabels = [
            'available' => ['ar' => 'المتاحة', 'en' => 'Available'],
            'rented' => ['ar' => 'المؤجّرة', 'en' => 'Rented'],
            'reserved' => ['ar' => 'محجوزة', 'en' => 'Reserved'],
            'unavailable' => ['ar' => 'غير متاحة', 'en' => 'Unavailable'],
        ];

        $statusData = [];
        foreach ($statusLabels as $status => $labels) {
            $count = $byStatus->get($status, 0);
            $statusData[] = [
                'status' => $status,
                'label' => $labels['ar'],
                'label_en' => $labels['en'],
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100) : 0,
            ];
        }

        $byType = Space::selectRaw('space_type, count(*) as count')
            ->whereNotNull('space_type')
            ->groupBy('space_type')
            ->pluck('count', 'space_type');

        $byDuration = Space::selectRaw('rental_duration, count(*) as count')
            ->whereNotNull('rental_duration')
            ->groupBy('rental_duration')
            ->pluck('count', 'rental_duration');

        return ApiResponse::success([
            'total' => $total,
            'available' => $byStatus->get('available', 0),
            'rented' => $byStatus->get('rented', 0),
            'reserved' => $byStatus->get('reserved', 0),
            'by_status' => $statusData,
            'by_type' => $byType,
            'by_rental_duration' => $byDuration,
            'price_range' => [
                'min' => (float) (Space::where('status', 'available')->min('price_total') ?? 0),
                'max' => (float) (Space::where('status', 'available')->max('price_total') ?? 0),
                'avg' => round((float) (Space::where('status', 'available')->avg('price_total') ?? 0), 2),
            ],
        ], 'إحصائيات المساحات');
    }

    /**
     * Platform aggregate stats
     */
    private function getPlatformStats(): array
    {
        return [
            'total_events' => Event::published()->count(),
            'active_events' => Event::active()->count(),
            'upcoming_events' => Event::upcoming()->count(),
            'total_spaces' => Space::count(),
            'available_spaces' => Space::where('status', 'available')->count(),
            'total_categories' => Category::active()->count(),
            'total_cities' => City::active()->count(),
            'total_services' => Service::active()->count(),
        ];
    }

    /**
     * Request aggregate stats (no sensitive data)
     */
    private function getRequestsStats(): array
    {
        return [
            'total_visit_requests' => VisitRequest::count(),
            'total_rental_requests' => RentalRequest::count(),
            'completed_visits' => VisitRequest::where('status', 'completed')->count(),
            'completed_rentals' => RentalRequest::where('status', 'completed')->count(),
        ];
    }

    /**
     * Events grouped by category
     */
    private function getEventsByCategory(): array
    {
        return Category::active()
            ->withCount(['events' => fn($q) => $q->where('status', 'published')])
            ->having('events_count', '>', 0)
            ->orderByDesc('events_count')
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'name_ar' => $c->name_ar,
                'events_count' => $c->events_count,
            ])
            ->toArray();
    }

    /**
     * Events grouped by city
     */
    private function getEventsByCity(): array
    {
        return City::active()
            ->withCount(['events' => fn($q) => $q->where('status', 'published')])
            ->having('events_count', '>', 0)
            ->orderByDesc('events_count')
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'name_ar' => $c->name_ar,
                'events_count' => $c->events_count,
            ])
            ->toArray();
    }

    /**
     * Spaces grouped by status
     */
    private function getSpacesByStatus(): array
    {
        $statusCounts = Space::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $total = $statusCounts->sum();

        $statusLabels = [
            'available' => ['ar' => 'المتاحة', 'en' => 'Available'],
            'rented' => ['ar' => 'المؤجّرة', 'en' => 'Rented'],
            'reserved' => ['ar' => 'محجوزة', 'en' => 'Reserved'],
            'unavailable' => ['ar' => 'غير متاحة', 'en' => 'Unavailable'],
        ];

        $result = [];
        foreach ($statusLabels as $status => $labels) {
            $count = $statusCounts->get($status, 0);
            $result[] = [
                'status' => $status,
                'label' => $labels['ar'],
                'label_en' => $labels['en'],
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100) : 0,
            ];
        }

        return $result;
    }
}
