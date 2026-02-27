<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageView;
use App\Models\UserActivity;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Admin Analytics Controller
 *
 * Provides comprehensive analytics data for management dashboards.
 * Covers: page views, user actions, platform stats, top resources.
 */
class AnalyticsController extends Controller
{
    /**
     * Analytics overview
     * GET /v1/manage/analytics
     */
    public function index(Request $request): JsonResponse
    {
        $period = $request->input('period', '30d');
        $from = $this->resolvePeriod($period);

        $activityQuery = UserActivity::query();
        $viewsQuery = PageView::query();

        if ($from) {
            $activityQuery->where('created_at', '>=', $from);
            $viewsQuery->where('created_at', '>=', $from);
        }

        // High-level counts
        $totalActivities = (clone $activityQuery)->count();
        $totalViews = (clone $viewsQuery)->count();
        $uniqueUsers = (clone $activityQuery)->whereNotNull('user_id')->distinct('user_id')->count('user_id');
        $uniqueIps = (clone $activityQuery)->distinct('ip_address')->count('ip_address');

        // Actions breakdown
        $actions = (clone $activityQuery)
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->orderByDesc('count')
            ->pluck('count', 'action');

        // Platform breakdown
        $platforms = (clone $activityQuery)
            ->selectRaw('platform, COUNT(*) as count')
            ->groupBy('platform')
            ->pluck('count', 'platform');

        // Daily trend (last 30 days max)
        $dailyTrend = (clone $activityQuery)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->limit(30)
            ->pluck('count', 'date');

        return ApiResponse::success([
            'period'          => $period,
            'total_activities' => $totalActivities,
            'total_views'     => $totalViews,
            'unique_users'    => $uniqueUsers,
            'unique_visitors' => $uniqueIps,
            'actions'         => $actions,
            'platforms'       => $platforms,
            'daily_trend'     => $dailyTrend,
        ]);
    }

    /**
     * View analytics — detailed page view data
     * GET /v1/manage/analytics/views
     */
    public function views(Request $request): JsonResponse
    {
        $period = $request->input('period', '30d');
        $from = $this->resolvePeriod($period);

        $query = PageView::query();
        if ($from) {
            $query->where('created_at', '>=', $from);
        }

        // Filter by platform
        if ($request->has('platform')) {
            $query->forPlatform($request->input('platform'));
        }

        // Top viewed resources
        $topResources = (clone $query)
            ->selectRaw('viewable_type, viewable_id, COUNT(*) as view_count')
            ->groupBy('viewable_type', 'viewable_id')
            ->orderByDesc('view_count')
            ->limit(20)
            ->get();

        // Views by resource type
        $byType = (clone $query)
            ->selectRaw('viewable_type, COUNT(*) as count')
            ->groupBy('viewable_type')
            ->orderByDesc('count')
            ->pluck('count', 'viewable_type');

        // Views by platform
        $byPlatform = (clone $query)
            ->selectRaw('platform, COUNT(*) as count')
            ->groupBy('platform')
            ->pluck('count', 'platform');

        // Hourly distribution
        $hourly = (clone $query)
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour');

        return ApiResponse::success([
            'period'        => $period,
            'total'         => (clone $query)->count(),
            'top_resources' => $topResources,
            'by_type'       => $byType,
            'by_platform'   => $byPlatform,
            'hourly_distribution' => $hourly,
        ]);
    }

    /**
     * Action analytics — search, click, share, etc.
     * GET /v1/manage/analytics/actions
     */
    public function actions(Request $request): JsonResponse
    {
        $period = $request->input('period', '30d');
        $from = $this->resolvePeriod($period);

        $query = UserActivity::query();
        if ($from) {
            $query->where('created_at', '>=', $from);
        }

        // Filter by action type
        if ($request->has('action')) {
            $query->forAction($request->input('action'));
        }

        // Filter by platform
        if ($request->has('platform')) {
            $query->forPlatform($request->input('platform'));
        }

        // Paginated list
        $activities = (clone $query)
            ->orderByDesc('created_at')
            ->paginate($request->input('per_page', 25));

        return ApiResponse::success($activities);
    }

    /**
     * User analytics — activity breakdown per user
     * GET /v1/manage/analytics/users
     */
    public function users(Request $request): JsonResponse
    {
        $period = $request->input('period', '30d');
        $from = $this->resolvePeriod($period);

        $query = UserActivity::query()->whereNotNull('user_id');
        if ($from) {
            $query->where('created_at', '>=', $from);
        }

        // Most active users
        $topUsers = (clone $query)
            ->selectRaw('user_id, COUNT(*) as activity_count, MAX(created_at) as last_active')
            ->groupBy('user_id')
            ->orderByDesc('activity_count')
            ->limit(50)
            ->get();

        // New vs returning (users with activities only in last 7 days)
        $recentUsersCount = (clone $query)
            ->where('created_at', '>=', now()->subDays(7))
            ->distinct('user_id')
            ->count('user_id');

        // Platform preference per user
        $platformPreference = (clone $query)
            ->selectRaw('platform, COUNT(DISTINCT user_id) as user_count')
            ->groupBy('platform')
            ->pluck('user_count', 'platform');

        return ApiResponse::success([
            'period'              => $period,
            'total_active_users'  => (clone $query)->distinct('user_id')->count('user_id'),
            'active_last_7_days'  => $recentUsersCount,
            'top_users'           => $topUsers,
            'users_by_platform'   => $platformPreference,
        ]);
    }

    /**
     * Resolve period string to Carbon date
     */
    private function resolvePeriod(string $period)
    {
        return match ($period) {
            '24h' => now()->subHours(24),
            '7d'  => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            '1y'  => now()->subYear(),
            'all' => null,
            default => now()->subDays(30),
        };
    }
}
