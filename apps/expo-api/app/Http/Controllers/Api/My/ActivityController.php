<?php

namespace App\Http\Controllers\Api\My;

use App\Http\Controllers\Controller;
use App\Models\UserActivity;
use App\Support\ApiResponse;
use App\Traits\TracksPlatform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * My Activity Controller
 *
 * Returns the authenticated user's own activity history and summary.
 */
class ActivityController extends Controller
{
    use TracksPlatform;

    /**
     * Get own recent activity (paginated)
     * GET /v1/my/activity
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        $query = UserActivity::forUser($userId)
            ->orderByDesc('created_at');

        // Filter by action
        if ($request->has('action')) {
            $query->forAction($request->input('action'));
        }

        // Filter by platform
        if ($request->has('platform')) {
            $query->forPlatform($request->input('platform'));
        }

        // Filter by resource type
        if ($request->has('resource_type')) {
            $resourceType = UserActivity::resolveResourceType($request->input('resource_type'));
            if ($resourceType) {
                $query->forResource($resourceType);
            }
        }

        // Date range
        if ($request->has('from') || $request->has('to')) {
            $query->dateRange($request->input('from'), $request->input('to'));
        }

        $activities = $query->paginate($request->input('per_page', 20));

        return ApiResponse::paginated($activities);
    }

    /**
     * Get own activity summary
     * GET /v1/my/activity/summary
     */
    public function summary(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        $baseQuery = UserActivity::forUser($userId);

        // Period filter
        $period = $request->input('period', '30d');
        $from = match ($period) {
            '7d'  => now()->subDays(7),
            '30d' => now()->subDays(30),
            '90d' => now()->subDays(90),
            'all' => null,
            default => now()->subDays(30),
        };

        if ($from) {
            $baseQuery->where('created_at', '>=', $from);
        }

        // Actions breakdown
        $actionCounts = (clone $baseQuery)
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->pluck('count', 'action');

        // Platform breakdown
        $platformCounts = (clone $baseQuery)
            ->selectRaw('platform, COUNT(*) as count')
            ->groupBy('platform')
            ->pluck('count', 'platform');

        // Most viewed resources
        $topViewed = (clone $baseQuery)
            ->where('action', 'view')
            ->whereNotNull('resource_type')
            ->selectRaw('resource_type, resource_id, COUNT(*) as view_count')
            ->groupBy('resource_type', 'resource_id')
            ->orderByDesc('view_count')
            ->limit(10)
            ->get();

        // Recent searches
        $recentSearches = (clone $baseQuery)
            ->where('action', 'search')
            ->whereNotNull('metadata')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->pluck('metadata.search_query')
            ->filter()
            ->unique()
            ->values();

        return ApiResponse::success([
            'period'     => $period,
            'total'      => (clone $baseQuery)->count(),
            'actions'    => $actionCounts,
            'platforms'  => $platformCounts,
            'top_viewed' => $topViewed,
            'recent_searches' => $recentSearches,
        ]);
    }
}
