<?php

namespace App\Http\Controllers\Api\Investor;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Space;
use App\Models\VisitRequest;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Investor Visit Request Controller
 *
 * Investors can view and approve/reject visit requests
 * for events that contain their spaces.
 * This is the first step in the two-step approval process.
 */
class VisitRequestController extends Controller
{
    /**
     * List visit requests for events where investor has spaces
     */
    public function index(Request $request): JsonResponse
    {
        $investorId = $request->input('auth_user_id');

        // Get event IDs where investor has spaces
        $eventIds = Space::where('investor_id', $investorId)
            ->distinct()
            ->pluck('event_id');

        $query = VisitRequest::whereIn('event_id', $eventIds)
            ->with([
                'event:id,name,name_ar,start_date,end_date',
            ]);

        // Filter by investor_status
        if ($request->has('investor_status')) {
            $query->where('investor_status', $request->input('investor_status'));
        }

        // Filter by main status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by event
        if ($request->has('event_id')) {
            $query->where('event_id', $request->input('event_id'));
        }

        // Search by request number
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('request_number', 'like', "%{$search}%");
        }

        $requests = $query->latest()->paginate($request->input('per_page', 15));

        return ApiResponse::success($requests);
    }

    /**
     * Show visit request details
     */
    public function show(Request $request, VisitRequest $visitRequest): JsonResponse
    {
        $investorId = $request->input('auth_user_id');

        // Verify investor has spaces in this event
        $hasSpaces = Space::where('investor_id', $investorId)
            ->where('event_id', $visitRequest->event_id)
            ->exists();

        if (!$hasSpaces) {
            return ApiResponse::error(
                message: __('messages.auth.forbidden'),
                errorCode: ApiErrorCode::PERMISSION_DENIED,
                httpCode: 403
            );
        }

        $visitRequest->load([
            'event:id,name,name_ar,start_date,end_date',
        ]);

        return ApiResponse::success($visitRequest);
    }

    /**
     * Approve visit request (first step - investor approval)
     */
    public function approve(Request $request, VisitRequest $visitRequest): JsonResponse
    {
        $investorId = $request->input('auth_user_id');

        // Verify investor has spaces in this event
        $hasSpaces = Space::where('investor_id', $investorId)
            ->where('event_id', $visitRequest->event_id)
            ->exists();

        if (!$hasSpaces) {
            return ApiResponse::error(
                message: __('messages.auth.forbidden'),
                errorCode: ApiErrorCode::PERMISSION_DENIED,
                httpCode: 403
            );
        }

        // Check if already processed by investor
        if ($visitRequest->investor_status !== 'pending') {
            return ApiResponse::error(
                message: __('messages.visit_request.already_processed'),
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                httpCode: 422
            );
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $visitRequest->update([
            'investor_status' => 'approved',
            'investor_reviewed_by' => $investorId,
            'investor_reviewed_at' => now(),
            'investor_notes' => $validated['notes'] ?? null,
        ]);

        // Notify the merchant
        Notification::send(
            userId: $visitRequest->user_id,
            title: 'Investor Approved Your Visit Request',
            titleAr: 'تمت موافقة المستثمر على طلب الزيارة',
            type: 'visit_request',
            body: 'The investor approved your visit request #' . $visitRequest->request_number . '. Awaiting final approval.',
            bodyAr: 'تمت موافقة المستثمر على طلب الزيارة الخاص بك رقم ' . $visitRequest->request_number . '. في انتظار الموافقة النهائية.',
            data: [
                'visit_request_id' => $visitRequest->id,
                'request_number' => $visitRequest->request_number,
            ],
        );

        return ApiResponse::success(
            data: $visitRequest,
            message: __('messages.visit_request.investor_approved')
        );
    }

    /**
     * Reject visit request by investor
     */
    public function reject(Request $request, VisitRequest $visitRequest): JsonResponse
    {
        $investorId = $request->input('auth_user_id');

        // Verify investor has spaces in this event
        $hasSpaces = Space::where('investor_id', $investorId)
            ->where('event_id', $visitRequest->event_id)
            ->exists();

        if (!$hasSpaces) {
            return ApiResponse::error(
                message: __('messages.auth.forbidden'),
                errorCode: ApiErrorCode::PERMISSION_DENIED,
                httpCode: 403
            );
        }

        // Check if already processed by investor
        if ($visitRequest->investor_status !== 'pending') {
            return ApiResponse::error(
                message: __('messages.visit_request.already_processed'),
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                httpCode: 422
            );
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $visitRequest->update([
            'investor_status' => 'rejected',
            'investor_reviewed_by' => $investorId,
            'investor_reviewed_at' => now(),
            'investor_notes' => $validated['reason'],
            // Also reject the main request
            'status' => 'rejected',
            'rejection_reason' => $validated['reason'],
        ]);

        // Notify the merchant
        Notification::send(
            userId: $visitRequest->user_id,
            title: 'Visit Request Rejected',
            titleAr: 'تم رفض طلب الزيارة',
            type: 'visit_request',
            body: 'Your visit request #' . $visitRequest->request_number . ' was rejected by the investor.',
            bodyAr: 'تم رفض طلب الزيارة رقم ' . $visitRequest->request_number . ' من قبل المستثمر.',
            data: [
                'visit_request_id' => $visitRequest->id,
                'request_number' => $visitRequest->request_number,
                'reason' => $validated['reason'],
            ],
        );

        return ApiResponse::success(
            data: $visitRequest,
            message: __('messages.visit_request.rejected')
        );
    }

    /**
     * Get pending visit requests count for investor
     */
    public function pendingCount(Request $request): JsonResponse
    {
        $investorId = $request->input('auth_user_id');

        $eventIds = Space::where('investor_id', $investorId)
            ->distinct()
            ->pluck('event_id');

        $count = VisitRequest::whereIn('event_id', $eventIds)
            ->where('investor_status', 'pending')
            ->count();

        return ApiResponse::success(['pending_count' => $count]);
    }
}
