<?php

namespace App\Http\Controllers\Api\Investor;

use App\Http\Controllers\Controller;
use App\Models\RentalRequest;
use App\Models\Space;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PaymentController extends Controller
{
    /**
     * List payments/revenue for investor's spaces
     */
    public function index(Request $request): JsonResponse
    {
        $investorId = $request->input('auth_user_id');
        $spaceIds = Space::where('investor_id', $investorId)->pluck('id');

        $query = RentalRequest::whereIn('space_id', $spaceIds)
            ->where('paid_amount', '>', 0)
            ->with([
                'space:id,name,name_ar,location_code',
                'businessProfile:id,company_name,company_name_ar',
            ]);

        // Filter by payment status
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('first_payment_at', '>=', $request->input('from_date'));
        }

        if ($request->has('to_date')) {
            $query->where('first_payment_at', '<=', $request->input('to_date'));
        }

        // Filter by space
        if ($request->has('space_id')) {
            $query->where('space_id', $request->input('space_id'));
        }

        $payments = $query->orderByDesc('first_payment_at')
            ->paginate($request->input('per_page', 15));

        return ApiResponse::paginated($payments);
    }

    /**
     * Get payment summary/statistics
     */
    public function summary(Request $request): JsonResponse
    {
        $investorId = $request->input('auth_user_id');
        $period = $request->input('period', 'all');
        $spaceIds = Space::where('investor_id', $investorId)->pluck('id');

        $query = RentalRequest::whereIn('space_id', $spaceIds);

        // Apply period filter
        $this->applyPeriodFilter($query, $period);

        // Calculate totals
        $totals = $query->selectRaw('
            SUM(total_price) as total_expected,
            SUM(paid_amount) as total_paid,
            SUM(total_price - paid_amount) as total_pending,
            COUNT(*) as total_requests
        ')->first();

        // Payment status breakdown
        $byStatus = $query->clone()
            ->selectRaw('payment_status, COUNT(*) as count, SUM(paid_amount) as amount')
            ->groupBy('payment_status')
            ->get()
            ->keyBy('payment_status');

        // Monthly breakdown
        $monthlyQuery = RentalRequest::whereIn('space_id', $spaceIds)
            ->whereNotNull('first_payment_at')
            ->where('first_payment_at', '>=', Carbon::now()->subMonths(12));

        $monthly = $monthlyQuery
            ->selectRaw('DATE_FORMAT(first_payment_at, "%Y-%m") as month, SUM(paid_amount) as amount')
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('amount', 'month');

        // Top paying spaces
        $topSpaces = RentalRequest::whereIn('space_id', $spaceIds)
            ->selectRaw('space_id, SUM(paid_amount) as total_revenue')
            ->groupBy('space_id')
            ->orderByDesc('total_revenue')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $space = Space::find($item->space_id);
                return [
                    'space_id' => $item->space_id,
                    'space_name' => $space?->name,
                    'space_name_ar' => $space?->name_ar,
                    'total_revenue' => (float) $item->total_revenue,
                ];
            });

        return ApiResponse::success([
            'totals' => [
                'expected' => (float) ($totals->total_expected ?? 0),
                'paid' => (float) ($totals->total_paid ?? 0),
                'pending' => (float) ($totals->total_pending ?? 0),
                'requests' => (int) ($totals->total_requests ?? 0),
            ],
            'by_status' => [
                'paid' => [
                    'count' => $byStatus->get('paid')?->count ?? 0,
                    'amount' => (float) ($byStatus->get('paid')?->amount ?? 0),
                ],
                'partial' => [
                    'count' => $byStatus->get('partial')?->count ?? 0,
                    'amount' => (float) ($byStatus->get('partial')?->amount ?? 0),
                ],
                'pending' => [
                    'count' => $byStatus->get('pending')?->count ?? 0,
                    'amount' => 0,
                ],
            ],
            'monthly' => $monthly,
            'top_spaces' => $topSpaces,
        ]);
    }

    /**
     * Get payment details for a specific rental request
     */
    public function show(Request $request, RentalRequest $rentalRequest): JsonResponse
    {
        $investorId = $request->input('auth_user_id');

        // Verify investor owns the space
        if ($rentalRequest->space?->investor_id !== $investorId) {
            return ApiResponse::error(
                message: __('messages.auth.forbidden'),
                errorCode: \App\Support\ApiErrorCode::PERMISSION_DENIED,
                httpCode: 403
            );
        }

        $rentalRequest->load([
            'space:id,name,name_ar,location_code,price_total',
            'businessProfile:id,company_name,company_name_ar,contact_phone',
        ]);

        return ApiResponse::success([
            'rental_request' => $rentalRequest,
            'payment_info' => [
                'total_price' => (float) $rentalRequest->total_price,
                'paid_amount' => (float) $rentalRequest->paid_amount,
                'remaining_amount' => (float) $rentalRequest->remaining_amount,
                'payment_status' => $rentalRequest->payment_status->value,
                'payment_status_label' => $rentalRequest->payment_status_label,
                'first_payment_at' => $rentalRequest->first_payment_at?->toISOString(),
            ],
        ]);
    }

    /**
     * Apply period filter to query
     */
    private function applyPeriodFilter($query, string $period): void
    {
        match ($period) {
            'today' => $query->whereDate('created_at', Carbon::today()),
            'week' => $query->where('created_at', '>=', Carbon::now()->startOfWeek()),
            'month' => $query->where('created_at', '>=', Carbon::now()->startOfMonth()),
            'year' => $query->where('created_at', '>=', Carbon::now()->startOfYear()),
            default => null,
        };
    }
}
