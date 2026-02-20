<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\RentalRequest;
use App\Models\VisitRequest;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Get merchant dashboard overview
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        return ApiResponse::success([
            'profile' => $this->getProfileStatus($userId),
            'rental_requests' => $this->getRentalStats($userId),
            'visit_requests' => $this->getVisitStats($userId),
            'recent_activity' => $this->getRecentActivity($userId),
        ]);
    }

    /**
     * Get business profile status
     */
    private function getProfileStatus(string $userId): array
    {
        $profile = BusinessProfile::where('user_id', $userId)->first();

        if (!$profile) {
            return [
                'exists' => false,
                'status' => null,
                'message' => 'لم يتم إنشاء ملف تجاري بعد',
                'message_en' => 'Business profile not created yet',
            ];
        }

        return [
            'exists' => true,
            'status' => $profile->status,
            'company_name' => $profile->company_name,
            'is_verified' => $profile->status === 'approved',
        ];
    }

    /**
     * Get rental request statistics
     */
    private function getRentalStats(string $userId): array
    {
        $query = RentalRequest::where('user_id', $userId);

        $statusCounts = $query->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return [
            'total' => $statusCounts->sum(),
            'pending' => $statusCounts->get('pending', 0),
            'approved' => $statusCounts->get('approved', 0),
            'rejected' => $statusCounts->get('rejected', 0),
            'completed' => $statusCounts->get('completed', 0),
            'cancelled' => $statusCounts->get('cancelled', 0),
            'total_spent' => (float) RentalRequest::where('user_id', $userId)
                ->sum('paid_amount'),
        ];
    }

    /**
     * Get visit request statistics
     */
    private function getVisitStats(string $userId): array
    {
        $query = VisitRequest::where('user_id', $userId);

        $statusCounts = $query->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return [
            'total' => $statusCounts->sum(),
            'pending' => $statusCounts->get('pending', 0),
            'approved' => $statusCounts->get('approved', 0),
            'rejected' => $statusCounts->get('rejected', 0),
            'completed' => $statusCounts->get('completed', 0),
            'cancelled' => $statusCounts->get('cancelled', 0),
        ];
    }

    /**
     * Get recent activity
     */
    private function getRecentActivity(string $userId): array
    {
        $recentRentals = RentalRequest::where('user_id', $userId)
            ->with(['space:id,name,name_ar'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($r) => [
                'type' => 'rental',
                'id' => $r->id,
                'request_number' => $r->request_number,
                'space' => $r->space?->name,
                'status' => $r->status->value,
                'created_at' => $r->created_at->toISOString(),
            ]);

        $recentVisits = VisitRequest::where('user_id', $userId)
            ->with(['event:id,name,name_ar'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($v) => [
                'type' => 'visit',
                'id' => $v->id,
                'event' => $v->event?->name,
                'visit_date' => $v->visit_date?->toDateString(),
                'status' => $v->status->value,
                'created_at' => $v->created_at->toISOString(),
            ]);

        return $recentRentals->merge($recentVisits)
            ->sortByDesc('created_at')
            ->values()
            ->take(10)
            ->toArray();
    }
}
