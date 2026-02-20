<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\Notification;
use App\Models\RentalRequest;
use App\Models\Space;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use App\Traits\TracksPlatform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class RentalRequestController extends Controller
{
    use TracksPlatform;

    /**
     * List merchant's own rental requests
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        $query = RentalRequest::where('user_id', $userId)
            ->with([
                'space:id,name,name_ar,location_code,price_total,event_id',
                'space.event:id,name,name_ar',
            ]);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by payment status
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        $requests = $query->latest()->paginate($request->input('per_page', 15));

        return ApiResponse::success($requests);
    }

    /**
     * Create new rental request
     */
    public function store(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        // Check if user has verified business profile
        $profile = BusinessProfile::where('user_id', $userId)
            ->where('status', 'approved')
            ->first();

        if (!$profile) {
            return ApiResponse::error(
                message: __('messages.profile.verification_required'),
                errorCode: ApiErrorCode::PROFILE_NOT_VERIFIED,
                httpCode: 403
            );
        }

        $validated = $request->validate([
            'space_id' => 'required|uuid|exists:spaces,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $space = Space::find($validated['space_id']);

        // Check if space is available
        if ($space->status !== 'available') {
            return ApiResponse::error(
                message: __('messages.space.not_available'),
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                httpCode: 422
            );
        }

        // Check for date conflicts
        if (!$space->isAvailableForDates($validated['start_date'], $validated['end_date'])) {
            return ApiResponse::error(
                message: __('messages.rental_request.dates_conflict'),
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                httpCode: 422
            );
        }

        // Calculate total price
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $days = $startDate->diffInDays($endDate) + 1;

        $totalPrice = $space->price_per_day
            ? $space->price_per_day * $days
            : $space->price_total;

        $rentalRequest = RentalRequest::create([
            'space_id' => $validated['space_id'],
            'user_id' => $userId,
            'business_profile_id' => $profile->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'total_price' => $totalPrice,
            'notes' => $validated['notes'] ?? null,
            'status' => 'pending',
            'investor_status' => 'pending',
            'payment_status' => 'pending',
        ]);

        // Notify investor (if space has investor)
        if ($space->investor_id) {
            Notification::create([
                'user_id' => $space->investor_id,
                'type' => 'rental_request',
                'title' => 'طلب إيجار جديد',
                'title_en' => 'New Rental Request',
                'message' => 'لديك طلب إيجار جديد للمساحة ' . $space->name,
                'message_en' => 'You have a new rental request for space ' . $space->name,
                'data' => [
                    'rental_request_id' => $rentalRequest->id,
                    'request_number' => $rentalRequest->request_number,
                    'space_id' => $space->id,
                ],
            ]);
        }

        $rentalRequest->load([
            'space:id,name,name_ar,location_code,price_total',
            'space.event:id,name,name_ar',
        ]);

        return ApiResponse::success(
            data: $rentalRequest,
            message: __('messages.rental_request.created'),
            httpCode: 201
        );
    }

    /**
     * Show rental request details
     */
    public function show(Request $request, RentalRequest $rentalRequest): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        // Verify ownership
        if ($rentalRequest->user_id !== $userId) {
            return ApiResponse::error(
                message: __('messages.auth.forbidden'),
                errorCode: ApiErrorCode::PERMISSION_DENIED,
                httpCode: 403
            );
        }

        $rentalRequest->load([
            'space:id,name,name_ar,location_code,price_total,event_id,images',
            'space.event:id,name,name_ar,start_date,end_date',
            'space.services:id,name,name_ar,price',
        ]);

        return ApiResponse::success($rentalRequest);
    }

    /**
     * Update rental request (only if pending)
     */
    public function update(Request $request, RentalRequest $rentalRequest): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        // Verify ownership
        if ($rentalRequest->user_id !== $userId) {
            return ApiResponse::error(
                message: __('messages.auth.forbidden'),
                errorCode: ApiErrorCode::PERMISSION_DENIED,
                httpCode: 403
            );
        }

        // Only pending requests can be modified
        if ($rentalRequest->status->value !== 'pending') {
            return ApiResponse::error(
                message: __('messages.rental_request.cannot_modify'),
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                httpCode: 422
            );
        }

        $validated = $request->validate([
            'start_date' => 'sometimes|date|after_or_equal:today',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'notes' => 'nullable|string|max:1000',
        ]);

        // If dates changed, recalculate price and check availability
        if (isset($validated['start_date']) || isset($validated['end_date'])) {
            $startDate = $validated['start_date'] ?? $rentalRequest->start_date->toDateString();
            $endDate = $validated['end_date'] ?? $rentalRequest->end_date->toDateString();

            if (!$rentalRequest->space->isAvailableForDates($startDate, $endDate)) {
                return ApiResponse::error(
                    message: __('messages.rental_request.dates_conflict'),
                    errorCode: ApiErrorCode::VALIDATION_FAILED,
                    httpCode: 422
                );
            }

            // Recalculate price
            $days = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
            $validated['total_price'] = $rentalRequest->space->price_per_day
                ? $rentalRequest->space->price_per_day * $days
                : $rentalRequest->space->price_total;
        }

        $rentalRequest->update($validated);

        return ApiResponse::success(
            data: $rentalRequest,
            message: __('messages.rental_request.updated')
        );
    }

    /**
     * Cancel rental request
     */
    public function destroy(Request $request, RentalRequest $rentalRequest): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        // Verify ownership
        if ($rentalRequest->user_id !== $userId) {
            return ApiResponse::error(
                message: __('messages.auth.forbidden'),
                errorCode: ApiErrorCode::PERMISSION_DENIED,
                httpCode: 403
            );
        }

        // Check if can be cancelled
        if (!$rentalRequest->can_be_cancelled) {
            return ApiResponse::error(
                message: __('messages.rental_request.cannot_cancel'),
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                httpCode: 422
            );
        }

        $rentalRequest->cancel();

        return ApiResponse::success(
            message: __('messages.rental_request.cancelled')
        );
    }
}
