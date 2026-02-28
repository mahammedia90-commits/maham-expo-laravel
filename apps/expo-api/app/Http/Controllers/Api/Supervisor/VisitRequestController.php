<?php

namespace App\Http\Controllers\Api\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\VisitRequest;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Supervisor Visit Request Controller
 *
 * Supervisors can:
 * - View all visit requests
 * - Approve/reject requests (after investor approval if applicable)
 * - Cannot delete requests
 */
class VisitRequestController extends Controller
{
    /**
     * List all visit requests
     */
    public function index(Request $request): JsonResponse
    {
        $query = VisitRequest::with([
            'event:id,name,name_ar,start_date,end_date',
        ]);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by investor_status
        if ($request->has('investor_status')) {
            $query->where('investor_status', $request->input('investor_status'));
        }

        // Filter by event
        if ($request->has('event_id')) {
            $query->where('event_id', $request->input('event_id'));
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('visit_date', '>=', $request->input('from_date'));
        }

        if ($request->has('to_date')) {
            $query->where('visit_date', '<=', $request->input('to_date'));
        }

        // Only show requests ready for approval
        if ($request->boolean('ready_for_approval', false)) {
            $query->where('investor_status', 'approved')
                ->where('status', 'pending');
        }

        $requests = $query->latest()->paginate($request->input('per_page', 15));

        return ApiResponse::success($requests);
    }

    /**
     * Show visit request details
     */
    public function show(VisitRequest $visitRequest): JsonResponse
    {
        $visitRequest->load([
            'event:id,name,name_ar,start_date,end_date,address,address_ar',
        ]);

        return ApiResponse::success($visitRequest);
    }

    /**
     * Approve visit request (final approval)
     */
    public function approve(Request $request, VisitRequest $visitRequest): JsonResponse
    {
        $supervisorId = $request->input('auth_user_id');

        // Check if investor has approved first (if investor approval is required)
        if ($visitRequest->investor_status === 'pending') {
            return ApiResponse::error(
                message: __('messages.visit_request.investor_approval_required'),
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                httpCode: 422,
                errors: [
                    'investor_status' => $visitRequest->investor_status,
                    'message' => 'يجب موافقة المستثمر أولاً',
                ]
            );
        }

        // Check if already processed
        if ($visitRequest->status->value !== 'pending') {
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
            'status' => 'approved',
            'reviewed_by' => $supervisorId,
            'reviewed_at' => now(),
            'admin_notes' => $validated['notes'] ?? null,
        ]);

        // Notify merchant
        Notification::send(
            userId: $visitRequest->user_id,
            title: 'Visit Request Approved',
            titleAr: 'تمت الموافقة على طلب الزيارة',
            type: 'visit_request',
            body: 'Your visit request has been approved',
            bodyAr: 'تمت الموافقة على طلب الزيارة الخاص بك',
            data: [
                'visit_request_id' => $visitRequest->id,
                'visit_date' => $visitRequest->visit_date->toDateString(),
            ],
        );

        return ApiResponse::success(
            data: $visitRequest,
            message: __('messages.visit_request.approved')
        );
    }

    /**
     * Reject visit request
     */
    public function reject(Request $request, VisitRequest $visitRequest): JsonResponse
    {
        $supervisorId = $request->input('auth_user_id');

        if ($visitRequest->status->value !== 'pending') {
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
            'status' => 'rejected',
            'reviewed_by' => $supervisorId,
            'reviewed_at' => now(),
            'rejection_reason' => $validated['reason'],
        ]);

        // Notify merchant
        Notification::send(
            userId: $visitRequest->user_id,
            title: 'Visit Request Rejected',
            titleAr: 'تم رفض طلب الزيارة',
            type: 'visit_request',
            body: 'Your visit request has been rejected',
            bodyAr: 'تم رفض طلب الزيارة الخاص بك',
            data: [
                'visit_request_id' => $visitRequest->id,
                'reason' => $validated['reason'],
            ],
        );

        return ApiResponse::success(
            data: $visitRequest,
            message: __('messages.visit_request.rejected')
        );
    }

    // Note: Supervisor cannot delete visit requests
    // Delete functionality is not implemented
}
