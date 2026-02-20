<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Notification;
use App\Models\Space;
use App\Models\VisitRequest;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VisitRequestController extends Controller
{
    /**
     * List merchant's own visit requests
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        $query = VisitRequest::where('user_id', $userId)
            ->with([
                'event:id,name,name_ar,start_date,end_date',
            ]);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by event
        if ($request->has('event_id')) {
            $query->where('event_id', $request->input('event_id'));
        }

        $requests = $query->latest()->paginate($request->input('per_page', 15));

        return ApiResponse::success($requests);
    }

    /**
     * Create visit request for an event
     */
    public function store(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        $validated = $request->validate([
            'event_id' => 'required|uuid|exists:events,id',
            'visit_date' => 'required|date|after_or_equal:today',
            'visit_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
            'contact_name' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'contact_email' => 'nullable|email|max:255',
        ]);

        $event = Event::find($validated['event_id']);

        // Check if event is published
        if ($event->status !== 'published') {
            return ApiResponse::error(
                message: __('messages.event.not_available'),
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                httpCode: 422
            );
        }

        // Check if visit date is within event dates
        if ($validated['visit_date'] < $event->start_date || $validated['visit_date'] > $event->end_date) {
            return ApiResponse::error(
                message: __('messages.visit_request.date_outside_event'),
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                httpCode: 422
            );
        }

        // Check for duplicate request on same date
        $exists = VisitRequest::where('user_id', $userId)
            ->where('event_id', $validated['event_id'])
            ->where('visit_date', $validated['visit_date'])
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->exists();

        if ($exists) {
            return ApiResponse::error(
                message: __('messages.visit_request.duplicate'),
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                httpCode: 422
            );
        }

        $visitRequest = VisitRequest::create([
            'user_id' => $userId,
            'event_id' => $validated['event_id'],
            'visit_date' => $validated['visit_date'],
            'visit_time' => $validated['visit_time'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'contact_name' => $validated['contact_name'],
            'contact_phone' => $validated['contact_phone'],
            'contact_email' => $validated['contact_email'] ?? null,
            'status' => 'pending',
        ]);

        $visitRequest->load('event:id,name,name_ar');

        return ApiResponse::success(
            data: $visitRequest,
            message: __('messages.visit_request.created'),
            httpCode: 201
        );
    }

    /**
     * Show visit request details
     */
    public function show(Request $request, VisitRequest $visitRequest): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        // Verify ownership
        if ($visitRequest->user_id !== $userId) {
            return ApiResponse::error(
                message: __('messages.auth.forbidden'),
                errorCode: ApiErrorCode::PERMISSION_DENIED,
                httpCode: 403
            );
        }

        $visitRequest->load([
            'event:id,name,name_ar,start_date,end_date,address,address_ar',
        ]);

        return ApiResponse::success($visitRequest);
    }

    /**
     * Update visit request (only if pending)
     */
    public function update(Request $request, VisitRequest $visitRequest): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        // Verify ownership
        if ($visitRequest->user_id !== $userId) {
            return ApiResponse::error(
                message: __('messages.auth.forbidden'),
                errorCode: ApiErrorCode::PERMISSION_DENIED,
                httpCode: 403
            );
        }

        // Only pending requests can be modified
        if ($visitRequest->status->value !== 'pending') {
            return ApiResponse::error(
                message: __('messages.visit_request.cannot_modify'),
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                httpCode: 422
            );
        }

        $validated = $request->validate([
            'visit_date' => 'sometimes|date|after_or_equal:today',
            'visit_time' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:1000',
            'contact_name' => 'sometimes|string|max:255',
            'contact_phone' => 'sometimes|string|max:20',
            'contact_email' => 'nullable|email|max:255',
        ]);

        // Validate date within event
        if (isset($validated['visit_date'])) {
            $event = $visitRequest->event;
            if ($validated['visit_date'] < $event->start_date || $validated['visit_date'] > $event->end_date) {
                return ApiResponse::error(
                    message: __('messages.visit_request.date_outside_event'),
                    errorCode: ApiErrorCode::VALIDATION_FAILED,
                    httpCode: 422
                );
            }
        }

        $visitRequest->update($validated);

        return ApiResponse::success(
            data: $visitRequest,
            message: __('messages.visit_request.updated')
        );
    }

    /**
     * Cancel visit request
     */
    public function destroy(Request $request, VisitRequest $visitRequest): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        // Verify ownership
        if ($visitRequest->user_id !== $userId) {
            return ApiResponse::error(
                message: __('messages.auth.forbidden'),
                errorCode: ApiErrorCode::PERMISSION_DENIED,
                httpCode: 403
            );
        }

        // Check if can be cancelled
        if (!in_array($visitRequest->status->value, ['pending', 'approved'])) {
            return ApiResponse::error(
                message: __('messages.visit_request.cannot_cancel'),
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                httpCode: 422
            );
        }

        // Cannot cancel if visit date has passed
        if ($visitRequest->visit_date < now()->toDateString()) {
            return ApiResponse::error(
                message: __('messages.visit_request.date_passed'),
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                httpCode: 422
            );
        }

        $visitRequest->update(['status' => 'cancelled']);

        return ApiResponse::success(
            message: __('messages.visit_request.cancelled')
        );
    }
}
