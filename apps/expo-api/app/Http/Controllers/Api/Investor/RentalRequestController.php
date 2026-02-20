<?php

namespace App\Http\Controllers\Api\Investor;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\RentalRequest;
use App\Models\Space;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RentalRequestController extends Controller
{
    /**
     * List rental requests for investor's spaces
     */
    public function index(Request $request): JsonResponse
    {
        $investorId = $request->input('auth_user_id');
        $spaceIds = Space::where('investor_id', $investorId)->pluck('id');

        $query = RentalRequest::whereIn('space_id', $spaceIds)
            ->with([
                'space:id,name,name_ar,location_code,price_total',
                'businessProfile:id,company_name,company_name_ar,contact_phone',
            ]);

        // Filter by investor_status
        if ($request->has('investor_status')) {
            $query->where('investor_status', $request->input('investor_status'));
        }

        // Filter by main status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by space
        if ($request->has('space_id')) {
            $query->where('space_id', $request->input('space_id'));
        }

        // Filter by payment status
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        $requests = $query->latest()->paginate($request->input('per_page', 15));

        return ApiResponse::success($requests);
    }

    /**
     * Show rental request details
     */
    public function show(Request $request, RentalRequest $rentalRequest): JsonResponse
    {
        $investorId = $request->input('auth_user_id');

        // Verify investor owns the space
        if ($rentalRequest->space?->investor_id !== $investorId) {
            return ApiResponse::error(
                message: __('messages.auth.forbidden'),
                errorCode: ApiErrorCode::PERMISSION_DENIED,
                httpCode: 403
            );
        }

        $rentalRequest->load([
            'space:id,name,name_ar,location_code,price_total,event_id',
            'space.event:id,name,name_ar',
            'businessProfile:id,company_name,company_name_ar,contact_phone,contact_email',
        ]);

        return ApiResponse::success($rentalRequest);
    }

    /**
     * Approve rental request (first step - investor approval)
     * After investor approval, admin/supervisor needs to approve
     */
    public function approve(Request $request, RentalRequest $rentalRequest): JsonResponse
    {
        $investorId = $request->input('auth_user_id');

        // Verify investor owns the space
        if ($rentalRequest->space?->investor_id !== $investorId) {
            return ApiResponse::error(
                message: __('messages.auth.forbidden'),
                errorCode: ApiErrorCode::PERMISSION_DENIED,
                httpCode: 403
            );
        }

        // Check if already processed by investor
        if ($rentalRequest->investor_status !== 'pending') {
            return ApiResponse::error(
                message: __('messages.rental_request.already_processed'),
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                httpCode: 422
            );
        }

        $validated = $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $rentalRequest->update([
            'investor_status' => 'approved',
            'investor_reviewed_by' => $investorId,
            'investor_reviewed_at' => now(),
            'investor_notes' => $validated['notes'] ?? null,
        ]);

        // Notify admins that investor approved
        // TODO: Implement notification to admins

        // Notify the merchant
        Notification::create([
            'user_id' => $rentalRequest->businessProfile?->user_id,
            'type' => 'rental_request',
            'title' => 'تمت موافقة المستثمر على طلب الإيجار',
            'title_en' => 'Investor Approved Your Rental Request',
            'message' => 'تمت موافقة المستثمر على طلب الإيجار الخاص بك رقم ' . $rentalRequest->request_number . '. في انتظار الموافقة النهائية.',
            'message_en' => 'The investor approved your rental request #' . $rentalRequest->request_number . '. Awaiting final approval.',
            'data' => [
                'rental_request_id' => $rentalRequest->id,
                'request_number' => $rentalRequest->request_number,
            ],
        ]);

        return ApiResponse::success(
            data: $rentalRequest,
            message: __('messages.rental_request.investor_approved')
        );
    }

    /**
     * Reject rental request by investor
     */
    public function reject(Request $request, RentalRequest $rentalRequest): JsonResponse
    {
        $investorId = $request->input('auth_user_id');

        // Verify investor owns the space
        if ($rentalRequest->space?->investor_id !== $investorId) {
            return ApiResponse::error(
                message: __('messages.auth.forbidden'),
                errorCode: ApiErrorCode::PERMISSION_DENIED,
                httpCode: 403
            );
        }

        // Check if already processed by investor
        if ($rentalRequest->investor_status !== 'pending') {
            return ApiResponse::error(
                message: __('messages.rental_request.already_processed'),
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                httpCode: 422
            );
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $rentalRequest->update([
            'investor_status' => 'rejected',
            'investor_reviewed_by' => $investorId,
            'investor_reviewed_at' => now(),
            'investor_notes' => $validated['reason'],
            // Also reject the main request
            'status' => 'rejected',
            'rejection_reason' => 'رفض من المستثمر: ' . $validated['reason'],
        ]);

        // Notify the merchant
        Notification::create([
            'user_id' => $rentalRequest->businessProfile?->user_id,
            'type' => 'rental_request',
            'title' => 'تم رفض طلب الإيجار',
            'title_en' => 'Rental Request Rejected',
            'message' => 'تم رفض طلب الإيجار رقم ' . $rentalRequest->request_number . ' من قبل المستثمر.',
            'message_en' => 'Your rental request #' . $rentalRequest->request_number . ' was rejected by the investor.',
            'data' => [
                'rental_request_id' => $rentalRequest->id,
                'request_number' => $rentalRequest->request_number,
                'reason' => $validated['reason'],
            ],
        ]);

        return ApiResponse::success(
            data: $rentalRequest,
            message: __('messages.rental_request.rejected')
        );
    }

    /**
     * Get pending requests count for investor
     */
    public function pendingCount(Request $request): JsonResponse
    {
        $investorId = $request->input('auth_user_id');
        $spaceIds = Space::where('investor_id', $investorId)->pluck('id');

        $count = RentalRequest::whereIn('space_id', $spaceIds)
            ->where('investor_status', 'pending')
            ->count();

        return ApiResponse::success(['pending_count' => $count]);
    }
}
