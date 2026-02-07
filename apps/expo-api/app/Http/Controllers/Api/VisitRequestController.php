<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVisitRequestRequest;
use App\Http\Requests\UpdateVisitRequestRequest;
use App\Http\Resources\VisitRequestResource;
use App\Models\Notification;
use App\Models\VisitRequest;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisitRequestController extends Controller
{
    /**
     * Get user's visit requests
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        $query = VisitRequest::with('event.city')
            ->forUser($userId);

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter by upcoming
        if ($request->boolean('upcoming')) {
            $query->upcoming();
        }

        $requests = $query->latest()->paginate(15);

        return ApiResponse::paginated(
            $requests->through(fn($item) => new VisitRequestResource($item))
        );
    }

    /**
     * Create visit request
     */
    public function store(StoreVisitRequestRequest $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        // Check for duplicate request
        $existingRequest = VisitRequest::forUser($userId)
            ->forEvent($request->event_id)
            ->whereIn('status', ['pending', 'approved'])
            ->where('visit_date', $request->visit_date)
            ->first();

        if ($existingRequest) {
            return ApiResponse::error(
                __('messages.visit_request.already_exists'),
                ApiErrorCode::VISIT_REQUEST_ALREADY_EXISTS
            );
        }

        $data = $request->validated();
        $data['user_id'] = $userId;

        $visitRequest = VisitRequest::create($data);
        $visitRequest->load('event.city');

        return ApiResponse::created(
            new VisitRequestResource($visitRequest),
            __('messages.visit_request.created')
        );
    }

    /**
     * Get single visit request
     */
    public function show(Request $request, VisitRequest $visitRequest): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        // Ensure user owns this request
        if ($visitRequest->user_id !== $userId) {
            return ApiResponse::forbidden(
                __('messages.forbidden')
            );
        }

        $visitRequest->load('event.city');

        return ApiResponse::success(
            new VisitRequestResource($visitRequest)
        );
    }

    /**
     * Update visit request
     */
    public function update(UpdateVisitRequestRequest $request, VisitRequest $visitRequest): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        // Ensure user owns this request
        if ($visitRequest->user_id !== $userId) {
            return ApiResponse::forbidden(
                __('messages.forbidden')
            );
        }

        // Check if can be modified
        if (!$visitRequest->can_be_modified) {
            return ApiResponse::error(
                __('messages.visit_request.cannot_be_modified'),
                ApiErrorCode::VISIT_REQUEST_CANNOT_BE_MODIFIED
            );
        }

        $visitRequest->update($request->validated());
        $visitRequest->load('event.city');

        return ApiResponse::success(
            new VisitRequestResource($visitRequest),
            __('messages.visit_request.updated')
        );
    }

    /**
     * Cancel visit request
     */
    public function destroy(Request $request, VisitRequest $visitRequest): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        // Ensure user owns this request
        if ($visitRequest->user_id !== $userId) {
            return ApiResponse::forbidden(
                __('messages.forbidden')
            );
        }

        // Check if can be cancelled
        if (!$visitRequest->can_be_cancelled) {
            return ApiResponse::error(
                __('messages.visit_request.cannot_be_cancelled'),
                ApiErrorCode::VISIT_REQUEST_CANNOT_BE_CANCELLED
            );
        }

        $visitRequest->cancel();

        return ApiResponse::success(
            null,
            __('messages.visit_request.cancelled')
        );
    }
}
