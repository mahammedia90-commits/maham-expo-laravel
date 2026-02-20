<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Api\Admin\DashboardController as AdminDashboardController;
use App\Models\BusinessProfile;
use App\Models\Category;
use App\Models\City;
use App\Models\Event;
use App\Models\PageView;
use App\Models\Space;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * SuperAdmin Dashboard Controller
 *
 * SuperAdmin has access to all admin dashboard stats
 * plus system-level stats (categories, cities, analytics)
 */
class DashboardController extends AdminDashboardController
{
    /**
     * Get super admin dashboard statistics
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
            'system' => $this->getSystemStats(),
            'analytics' => $this->getAnalytics($request),
        ]);
    }

    /**
     * System-level statistics
     */
    private function getSystemStats(): array
    {
        return [
            'total_categories' => Category::count(),
            'active_categories' => Category::where('is_active', true)->count(),
            'total_cities' => City::count(),
            'active_cities' => City::where('is_active', true)->count(),
            'total_events' => Event::count(),
            'published_events' => Event::where('status', 'published')->count(),
            'total_profiles' => BusinessProfile::count(),
            'pending_profiles' => BusinessProfile::where('status', 'pending')->count(),
            'approved_profiles' => BusinessProfile::where('status', 'approved')->count(),
        ];
    }

    /**
     * Analytics - most viewed events/spaces by platform
     */
    private function getAnalytics(Request $request): array
    {
        $period = $request->input('analytics_period', 'month');

        $totalViews = $this->getViewsQuery($period)->count();

        // Views by platform
        $byPlatform = $this->getViewsQuery($period)
            ->selectRaw('platform, count(*) as count')
            ->groupBy('platform')
            ->pluck('count', 'platform');

        // Top viewed events
        $topEvents = $this->getViewsQuery($period)
            ->where('viewable_type', 'App\\Models\\Event')
            ->selectRaw('viewable_id, count(*) as views_count')
            ->groupBy('viewable_id')
            ->orderByDesc('views_count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $event = Event::select('id', 'name', 'name_ar')->find($item->viewable_id);
                return [
                    'id' => $item->viewable_id,
                    'name' => $event?->name,
                    'name_ar' => $event?->name_ar,
                    'views' => $item->views_count,
                ];
            });

        // Top viewed spaces
        $topSpaces = $this->getViewsQuery($period)
            ->where('viewable_type', 'App\\Models\\Space')
            ->selectRaw('viewable_id, count(*) as views_count')
            ->groupBy('viewable_id')
            ->orderByDesc('views_count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $space = Space::select('id', 'name', 'name_ar')->find($item->viewable_id);
                return [
                    'id' => $item->viewable_id,
                    'name' => $space?->name,
                    'name_ar' => $space?->name_ar,
                    'views' => $item->views_count,
                ];
            });

        return [
            'total_views' => $totalViews,
            'by_platform' => [
                'web' => $byPlatform->get('web', 0),
                'mobile' => $byPlatform->get('mobile', 0),
                'api' => $byPlatform->get('api', 0),
            ],
            'top_events' => $topEvents,
            'top_spaces' => $topSpaces,
        ];
    }

    /**
     * Build page views query with period filter
     */
    private function getViewsQuery(string $period)
    {
        $query = PageView::query();

        match ($period) {
            'today' => $query->where('created_at', '>=', Carbon::today()),
            'week' => $query->where('created_at', '>=', Carbon::now()->startOfWeek()),
            'month' => $query->where('created_at', '>=', Carbon::now()->startOfMonth()),
            'year' => $query->where('created_at', '>=', Carbon::now()->startOfYear()),
            default => null,
        };

        return $query;
    }
}
