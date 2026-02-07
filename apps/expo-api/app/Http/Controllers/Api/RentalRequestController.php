<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRentalRequestRequest;
use App\Http\Requests\UpdateRentalRequestRequest;
use App\Http\Resources\RentalRequestResource;
use App\Models\BusinessProfile;
use App\Models\RentalRequest;
use App\Models\Space;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RentalRequestController extends Controller
{
    /**
     * Get user's rental requests
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        $query = RentalRequest::with(['space.event.city', 'businessProfile'])
            ->forUser($userId);

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter by payment status
        if ($paymentStatus = $request->input('payment_status')) {
            $query->where('payment_status', $paymentStatus);
        }

        // Filter by active
        if ($request->boolean('active')) {
            $query->active();
        }

        $requests = $query->latest()->paginate(15);

        return ApiResponse::paginated(
            $requests->through(fn($item) => new RentalRequestResource($item))
        );
    }

    /**
     * Create rental request
     */
    public function store(StoreRentalRequestRequest $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        // Check for verified business profile
        $profile = BusinessProfile::forUser($userId)->approved()->first();

        if (!$profile) {
            return ApiResponse::error(
                __('messages.profile.required_for_rental'),
                ApiErrorCode::PROFILE_REQUIRED
            );
        }

        $space = Space::with('event')->find($request->space_id);

        // Check for existing request for same space and overlapping dates
        $existingRequest = RentalRequest::forUser($userId)
            ->forSpace($request->space_id)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                    ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                    });
            })
            ->first();

        if ($existingRequest) {
            return ApiResponse::error(
                __('messages.rental_request.already_exists'),
                ApiErrorCode::RENTAL_REQUEST_ALREADY_EXISTS
            );
        }

        // Calculate total price
        $startDate = \Carbon\Carbon::parse($request->start_date);
        $endDate = \Carbon\Carbon::parse($request->end_date);
        $days = $startDate->diffInDays($endDate) + 1;

        $totalPrice = $space->price_per_day
            ? $space->price_per_day * $days
            : $space->price_total;

        $data = $request->validated();
        $data['user_id'] = $userId;
        $data['business_profile_id'] = $profile->id;
        $data['total_price'] = $totalPrice;

        $rentalRequest = RentalRequest::create($data);
        $rentalRequest->load(['space.event.city', 'businessProfile']);

        return ApiResponse::created(
            new RentalRequestResource($rentalRequest),
            __('messages.rental_request.created')
        );
    }

    /**
     * Get single rental request
     */
    public function show(Request $request, RentalRequest $rentalRequest): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        // Ensure user owns this request
        if ($rentalRequest->user_id !== $userId) {
            return ApiResponse::forbidden(
                __('messages.forbidden')
            );
        }

        $rentalRequest->load(['space.event.city', 'businessProfile']);

        return ApiResponse::success(
            new RentalRequestResource($rentalRequest)
        );
    }

    /**
     * Update rental request
     */
    public function update(UpdateRentalRequestRequest $request, RentalRequest $rentalRequest): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        // Ensure user owns this request
        if ($rentalRequest->user_id !== $userId) {
            return ApiResponse::forbidden(
                __('messages.forbidden')
            );
        }

        // Check if can be modified
        if (!$rentalRequest->can_be_modified) {
            return ApiResponse::error(
                __('messages.rental_request.cannot_be_modified'),
                ApiErrorCode::RENTAL_REQUEST_CANNOT_BE_MODIFIED
            );
        }

        $data = $request->validated();

        // Recalculate total price if dates changed
        if (isset($data['start_date']) || isset($data['end_date'])) {
            $startDate = \Carbon\Carbon::parse($data['start_date'] ?? $rentalRequest->start_date);
            $endDate = \Carbon\Carbon::parse($data['end_date'] ?? $rentalRequest->end_date);
            $days = $startDate->diffInDays($endDate) + 1;

            $space = $rentalRequest->space;
            $data['total_price'] = $space->price_per_day
                ? $space->price_per_day * $days
                : $space->price_total;
        }

        $rentalRequest->update($data);
        $rentalRequest->load(['space.event.city', 'businessProfile']);

        return ApiResponse::success(
            new RentalRequestResource($rentalRequest),
            __('messages.rental_request.updated')
        );
    }

    /**
     * Cancel rental request
     */
    public function destroy(Request $request, RentalRequest $rentalRequest): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        // Ensure user owns this request
        if ($rentalRequest->user_id !== $userId) {
            return ApiResponse::forbidden(
                __('messages.forbidden')
            );
        }

        // Check if can be cancelled
        if (!$rentalRequest->can_be_cancelled) {
            return ApiResponse::error(
                __('messages.rental_request.cannot_be_cancelled'),
                ApiErrorCode::RENTAL_REQUEST_CANNOT_BE_CANCELLED
            );
        }

        $rentalRequest->cancel();

        return ApiResponse::success(
            null,
            __('messages.rental_request.cancelled')
        );
    }
}
