<?php

namespace App\Http\Controllers\Api\Supervisor;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\RentalRequest;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Supervisor Rental Request Controller
 *
 * Supervisors can:
 * - View all rental requests
 * - Approve/reject requests (after investor approval)
 * - Record payments
 * - Cannot delete requests
 */
class RentalRequestController extends Controller
{
    /**
     * List all rental requests
     */
    public function index(Request $request): JsonResponse
    {
        $query = RentalRequest::with([
            'space:id,name,name_ar,location_code,price_total,event_id',
            'space.event:id,name,name_ar',
            'businessProfile:id,company_name,company_name_ar,contact_phone',
        ]);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by investor_status
        if ($request->has('investor_status')) {
            $query->where('investor_status', $request->input('investor_status'));
        }

        // Filter by payment status
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        // Filter by event
        if ($request->has('event_id')) {
            $query->whereHas('space', fn($q) => $q->where('event_id', $request->input('event_id')));
        }

        // Only show requests that investor has approved (ready for supervisor approval)
        if ($request->boolean('ready_for_approval', false)) {
            $query->where('investor_status', 'approved')
                ->where('status', 'pending');
        }

        $requests = $query->latest()->paginate($request->input('per_page', 15));

        return ApiResponse::paginated($requests);
    }

    /**
     * Show rental request details
     */
    public function show(RentalRequest $rentalRequest): JsonResponse
    {
        $rentalRequest->load([
            'space:id,name,name_ar,location_code,price_total,event_id,investor_id',
            'space.event:id,name,name_ar',
            'businessProfile:id,company_name,company_name_ar,contact_phone,contact_email',
        ]);

        return ApiResponse::success($rentalRequest);
    }

    /**
     * Approve rental request (final approval after investor)
     */
    public function approve(Request $request, RentalRequest $rentalRequest): JsonResponse
    {
        $supervisorId = $request->input('auth_user_id');

        // Check if investor has approved first
        if ($rentalRequest->investor_status !== 'approved') {
            return ApiResponse::error(
                message: __('messages.rental_request.investor_approval_required'),
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                httpCode: 422,
                errors: [
                    'investor_status' => $rentalRequest->investor_status,
                    'message' => 'يجب موافقة المستثمر أولاً',
                ]
            );
        }

        // Check if already processed
        if ($rentalRequest->status->value !== 'pending') {
            return ApiResponse::error(
                message: __('messages.rental_request.already_processed'),
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                httpCode: 422
            );
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $rentalRequest->approve($supervisorId, $validated['notes'] ?? null);

        // Notify merchant
        Notification::send(
            userId: $rentalRequest->businessProfile?->user_id,
            title: 'Rental Request Approved',
            titleAr: 'تمت الموافقة على طلب الإيجار',
            type: 'rental_request',
            body: 'Your rental request #' . $rentalRequest->request_number . ' has been approved',
            bodyAr: 'تمت الموافقة على طلب الإيجار رقم ' . $rentalRequest->request_number,
            data: [
                'rental_request_id' => $rentalRequest->id,
                'request_number' => $rentalRequest->request_number,
            ],
        );

        return ApiResponse::success(
            data: $rentalRequest,
            message: __('messages.rental_request.approved')
        );
    }

    /**
     * Reject rental request
     */
    public function reject(Request $request, RentalRequest $rentalRequest): JsonResponse
    {
        $supervisorId = $request->input('auth_user_id');

        if ($rentalRequest->status->value !== 'pending') {
            return ApiResponse::error(
                message: __('messages.rental_request.already_processed'),
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                httpCode: 422
            );
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        $rentalRequest->reject($supervisorId, $validated['reason'], $validated['notes'] ?? null);

        // Notify merchant
        Notification::send(
            userId: $rentalRequest->businessProfile?->user_id,
            title: 'Rental Request Rejected',
            titleAr: 'تم رفض طلب الإيجار',
            type: 'rental_request',
            body: 'Your rental request #' . $rentalRequest->request_number . ' has been rejected',
            bodyAr: 'تم رفض طلب الإيجار رقم ' . $rentalRequest->request_number,
            data: [
                'rental_request_id' => $rentalRequest->id,
                'request_number' => $rentalRequest->request_number,
                'reason' => $validated['reason'],
            ],
        );

        return ApiResponse::success(
            data: $rentalRequest,
            message: __('messages.rental_request.rejected')
        );
    }

    /**
     * Record payment for rental request
     */
    public function recordPayment(Request $request, RentalRequest $rentalRequest): JsonResponse
    {
        if ($rentalRequest->status->value !== 'approved') {
            return ApiResponse::error(
                message: __('messages.rental_request.must_be_approved'),
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                httpCode: 422
            );
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $rentalRequest->remaining_amount,
        ]);

        $rentalRequest->recordPayment($validated['amount']);

        // Notify merchant
        Notification::send(
            userId: $rentalRequest->businessProfile?->user_id,
            title: 'Payment Recorded',
            titleAr: 'تم تسجيل دفعة',
            type: 'payment',
            body: 'Payment of ' . $validated['amount'] . ' SAR recorded for request #' . $rentalRequest->request_number,
            bodyAr: 'تم تسجيل دفعة بمبلغ ' . $validated['amount'] . ' ر.س للطلب رقم ' . $rentalRequest->request_number,
            data: [
                'rental_request_id' => $rentalRequest->id,
                'amount' => $validated['amount'],
                'remaining' => $rentalRequest->remaining_amount,
            ],
        );

        return ApiResponse::success(
            data: $rentalRequest,
            message: __('messages.rental_request.payment_recorded')
        );
    }

    // Note: Supervisor cannot delete rental requests
    // Delete functionality is not implemented
}
