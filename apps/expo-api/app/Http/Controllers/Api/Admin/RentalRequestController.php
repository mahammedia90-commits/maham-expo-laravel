<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ReviewRequestRequest;
use App\Http\Resources\RentalRequestResource;
use App\Models\Notification;
use App\Models\RentalRequest;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RentalRequestController extends Controller
{
    /**
     * Get all rental requests (admin)
     */
    public function index(Request $request): JsonResponse
    {
        $query = RentalRequest::with(['space.event.city', 'businessProfile']);

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter by payment status
        if ($paymentStatus = $request->input('payment_status')) {
            $query->where('payment_status', $paymentStatus);
        }

        // Filter by event
        if ($eventId = $request->input('event_id')) {
            $query->whereHas('space', function ($q) use ($eventId) {
                $q->where('event_id', $eventId);
            });
        }

        // Filter by space
        if ($spaceId = $request->input('space_id')) {
            $query->forSpace($spaceId);
        }

        // Filter by date range
        if ($fromDate = $request->input('from_date')) {
            $query->where('start_date', '>=', $fromDate);
        }
        if ($toDate = $request->input('to_date')) {
            $query->where('end_date', '<=', $toDate);
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
            $requests->through(fn($item) => new RentalRequestResource($item))
        );
    }

    /**
     * Get single rental request
     */
    public function show(RentalRequest $rentalRequest): JsonResponse
    {
        $rentalRequest->load(['space.event.city', 'businessProfile']);

        return ApiResponse::success(
            new RentalRequestResource($rentalRequest)
        );
    }

    /**
     * Approve rental request
     */
    public function approve(ReviewRequestRequest $request, RentalRequest $rentalRequest): JsonResponse
    {
        if ($rentalRequest->status->value !== 'pending') {
            return ApiResponse::error(
                __('messages.rental_request.not_pending'),
                'request_not_pending'
            );
        }

        $adminId = $request->input('auth_user_id');
        $rentalRequest->approve($adminId, $request->notes);

        // Send notification to user
        Notification::send(
            userId: $rentalRequest->user_id,
            title: 'Rental Request Approved',
            titleAr: 'تمت الموافقة على طلب الإيجار',
            type: 'rental_request_approved',
            body: "Your rental request #{$rentalRequest->request_number} has been approved.",
            bodyAr: "تمت الموافقة على طلب الإيجار رقم #{$rentalRequest->request_number}",
            data: [
                'request_id' => $rentalRequest->id,
                'request_number' => $rentalRequest->request_number,
            ]
        );

        $rentalRequest->load(['space.event.city', 'businessProfile']);

        return ApiResponse::success(
            new RentalRequestResource($rentalRequest),
            __('messages.rental_request.approved')
        );
    }

    /**
     * Reject rental request
     */
    public function reject(ReviewRequestRequest $request, RentalRequest $rentalRequest): JsonResponse
    {
        if ($rentalRequest->status->value !== 'pending') {
            return ApiResponse::error(
                __('messages.rental_request.not_pending'),
                'request_not_pending'
            );
        }

        $adminId = $request->input('auth_user_id');
        $rentalRequest->reject($adminId, $request->reason, $request->notes);

        // Send notification to user
        Notification::send(
            userId: $rentalRequest->user_id,
            title: 'Rental Request Rejected',
            titleAr: 'تم رفض طلب الإيجار',
            type: 'rental_request_rejected',
            body: "Your rental request #{$rentalRequest->request_number} has been rejected.",
            bodyAr: "تم رفض طلب الإيجار رقم #{$rentalRequest->request_number}",
            data: [
                'request_id' => $rentalRequest->id,
                'request_number' => $rentalRequest->request_number,
                'reason' => $request->reason,
            ]
        );

        $rentalRequest->load(['space.event.city', 'businessProfile']);

        return ApiResponse::success(
            new RentalRequestResource($rentalRequest),
            __('messages.rental_request.rejected')
        );
    }

    /**
     * Record payment
     */
    public function recordPayment(Request $request, RentalRequest $rentalRequest): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $rentalRequest->remaining_amount,
        ]);

        if ($rentalRequest->status->value !== 'approved') {
            return ApiResponse::error(
                __('messages.rental_request.must_be_approved'),
                'request_must_be_approved'
            );
        }

        $rentalRequest->recordPayment($request->amount);

        // Send notification to user
        Notification::send(
            userId: $rentalRequest->user_id,
            title: 'Payment Recorded',
            titleAr: 'تم تسجيل الدفعة',
            type: 'payment_recorded',
            body: "Payment of {$request->amount} SAR has been recorded for request #{$rentalRequest->request_number}.",
            bodyAr: "تم تسجيل دفعة بمبلغ {$request->amount} ريال لطلب رقم #{$rentalRequest->request_number}",
            data: [
                'request_id' => $rentalRequest->id,
                'request_number' => $rentalRequest->request_number,
                'amount' => $request->amount,
            ]
        );

        $rentalRequest->load(['space.event.city', 'businessProfile']);

        return ApiResponse::success(
            new RentalRequestResource($rentalRequest),
            __('messages.rental_request.payment_recorded')
        );
    }
}
