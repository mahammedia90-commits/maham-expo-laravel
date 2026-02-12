<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * Get user statistics for admin dashboard
     */
    public function users(Request $request): JsonResponse
    {
        $period = $request->input('period', 'all');

        $query = User::query();
        $this->applyPeriodFilter($query, $period);

        $totalUsers = $query->count();

        // Users grouped by role
        $usersByRole = Role::withCount(['users' => function ($q) use ($period) {
            $this->applyPeriodFilter($q, $period, 'user_roles.created_at');
        }])
            ->whereHas('users')
            ->get()
            ->map(fn($role) => [
                'role' => $role->name,
                'role_label' => $role->display_name,
                'count' => $role->users_count,
            ])
            ->sortByDesc('count')
            ->values();

        // Users grouped by status
        $usersByStatus = User::query()
            ->when($period !== 'all', fn($q) => $this->applyPeriodFilter($q, $period))
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->map(fn($item) => [
                'status' => $item->status,
                'count' => $item->count,
            ]);

        // New users counts
        $newUsersThisMonth = User::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        $newUsersThisWeek = User::where('created_at', '>=', Carbon::now()->startOfWeek())->count();

        return ApiResponse::success([
            'total_users' => $totalUsers,
            'users_by_role' => $usersByRole,
            'users_by_status' => $usersByStatus,
            'new_users_this_month' => $newUsersThisMonth,
            'new_users_this_week' => $newUsersThisWeek,
        ]);
    }

    /**
     * Apply period filter to query
     */
    private function applyPeriodFilter($query, string $period, string $column = 'created_at'): void
    {
        match ($period) {
            'today' => $query->where($column, '>=', Carbon::today()),
            'week' => $query->where($column, '>=', Carbon::now()->startOfWeek()),
            'month' => $query->where($column, '>=', Carbon::now()->startOfMonth()),
            'year' => $query->where($column, '>=', Carbon::now()->startOfYear()),
            default => null, // 'all' — no filter
        };
    }
}
