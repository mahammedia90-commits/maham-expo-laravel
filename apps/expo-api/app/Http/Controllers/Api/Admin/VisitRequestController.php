<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewRequestRequest;
use App\Http\Resources\VisitRequestResource;
use App\Models\Notification;
use App\Models\VisitRequest;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisitRequestController extends Controller
{
    /**
     * Get all visit requests (admin)
     */
    public function index(Request $request): JsonResponse
    {
        $query = VisitRequest::with('event.city');

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter by event
        if ($eventId = $request->input('event_id')) {
            $query->forEvent($eventId);
        }

        // Filter by date range
        if ($fromDate = $request->input('from_date')) {
            $query->where('visit_date', '>=', $fromDate);
        }
        if ($toDate = $request->input('to_date')) {
            $query->where('visit_date', '<=', $toDate);
        }

        // Search by request number
        if ($search = $request->input('search')) {
            $query->where('request_number', 'like', "%{$search}%");
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $requests = $query->paginate(15);

        return ApiResponse::paginated(
            $requests->through(fn($item) => new VisitRequestResource($item))
        );
    }

    /**
     * Get single visit request
     */
    public function show(VisitRequest $visitRequest): JsonResponse
    {
        $visitRequest->load('event.city');

        return ApiResponse::success(
            new VisitRequestResource($visitRequest)
        );
    }

    /**
     * Approve visit request
     */
    public function approve(ReviewRequestRequest $request, VisitRequest $visitRequest): JsonResponse
    {
        if ($visitRequest->status->value !== 'pending') {
            return ApiResponse::error(
                __('messages.visit_request.not_pending'),
                'request_not_pending'
            );
        }

        $adminId = $request->input('auth_user_id');
        $visitRequest->approve($adminId, $request->notes);

        // Send notification to user
        Notification::send(
            userId: $visitRequest->user_id,
            title: 'Visit Request Approved',
            titleAr: 'تمت الموافقة على طلب الزيارة',
            type: 'visit_request_approved',
            body: "Your visit request #{$visitRequest->request_number} has been approved.",
            bodyAr: "تمت الموافقة على طلب الزيارة رقم #{$visitRequest->request_number}",
            data: [
                'request_id' => $visitRequest->id,
                'request_number' => $visitRequest->request_number,
            ]
        );

        $visitRequest->load('event.city');

        return ApiResponse::success(
            new VisitRequestResource($visitRequest),
            __('messages.visit_request.approved')
        );
    }

    /**
     * Reject visit request
     */
    public function reject(ReviewRequestRequest $request, VisitRequest $visitRequest): JsonResponse
    {
        if ($visitRequest->status->value !== 'pending') {
            return ApiResponse::error(
                __('messages.visit_request.not_pending'),
                'request_not_pending'
            );
        }

        $adminId = $request->input('auth_user_id');
        $visitRequest->reject($adminId, $request->reason, $request->notes);

        // Send notification to user
        Notification::send(
            userId: $visitRequest->user_id,
            title: 'Visit Request Rejected',
            titleAr: 'تم رفض طلب الزيارة',
            type: 'visit_request_rejected',
            body: "Your visit request #{$visitRequest->request_number} has been rejected.",
            bodyAr: "تم رفض طلب الزيارة رقم #{$visitRequest->request_number}",
            data: [
                'request_id' => $visitRequest->id,
                'request_number' => $visitRequest->request_number,
                'reason' => $request->reason,
            ]
        );

        $visitRequest->load('event.city');

        return ApiResponse::success(
            new VisitRequestResource($visitRequest),
            __('messages.visit_request.rejected')
        );
    }
}
