<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PageView;
use App\Models\UserActivity;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use App\Traits\TracksPlatform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Tracking Controller
 *
 * Public & authenticated tracking endpoints.
 * Records page views and user actions for analytics.
 * Works from both web and mobile (X-Platform header).
 */
class TrackingController extends Controller
{
    use TracksPlatform;

    /**
     * Record a page/entity view
     * POST /v1/track/view
     *
     * Tracks views for events, spaces, sections, sponsors, pages, etc.
     * Also records in the legacy PageView table for backward compatibility.
     */
    public function view(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'resource_type' => 'required|string|in:event,space,section,sponsor,page,banner,faq,service',
            'resource_id'   => 'required|uuid',
            'session_id'    => 'nullable|string|max:100',
            'metadata'      => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors()->toArray());
        }

        $resourceType = UserActivity::resolveResourceType($request->input('resource_type'));
        if (!$resourceType) {
            return ApiResponse::error(
                __('messages.tracking.invalid_resource_type'),
                ApiErrorCode::INVALID_INPUT,
                400
            );
        }

        $platform = $this->getPlatform($request);
        $userId = $request->input('auth_user_id'); // null if not authenticated

        // Record in UserActivity table
        UserActivity::record(
            action: 'view',
            userId: $userId,
            resourceType: $resourceType,
            resourceId: $request->input('resource_id'),
            platform: $platform,
            metadata: $request->input('metadata'),
            sessionId: $request->input('session_id'),
        );

        // Also record in legacy PageView table (polymorphic)
        PageView::create([
            'viewable_type' => $resourceType,
            'viewable_id'   => $request->input('resource_id'),
            'user_id'       => $userId,
            'platform'      => $platform,
            'ip_address'    => $request->ip(),
            'user_agent'    => $request->userAgent(),
            'referrer'      => $request->headers->get('referer'),
        ]);

        return ApiResponse::success(null, __('messages.tracking.view_recorded'), 201);
    }

    /**
     * Record a user action
     * POST /v1/track/action
     *
     * Tracks actions like search, filter, share, click, download, etc.
     * Supports metadata for context (search query, filter params, etc.)
     */
    public function action(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'action'        => 'required|string|in:' . implode(',', UserActivity::ACTIONS),
            'resource_type' => 'nullable|string|in:event,space,section,sponsor,page,banner,faq,service',
            'resource_id'   => 'nullable|uuid|required_with:resource_type',
            'session_id'    => 'nullable|string|max:100',
            'metadata'      => 'nullable|array',
            'metadata.search_query'  => 'nullable|string|max:255',
            'metadata.filter_params' => 'nullable|array',
            'metadata.source_page'   => 'nullable|string|max:255',
            'metadata.target_url'    => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return ApiResponse::validationError($validator->errors()->toArray());
        }

        $resourceType = UserActivity::resolveResourceType($request->input('resource_type'));
        $platform = $this->getPlatform($request);
        $userId = $request->input('auth_user_id');

        UserActivity::record(
            action: $request->input('action'),
            userId: $userId,
            resourceType: $resourceType,
            resourceId: $request->input('resource_id'),
            platform: $platform,
            metadata: $request->input('metadata'),
            sessionId: $request->input('session_id'),
        );

        return ApiResponse::success(null, __('messages.tracking.action_recorded'), 201);
    }
}
