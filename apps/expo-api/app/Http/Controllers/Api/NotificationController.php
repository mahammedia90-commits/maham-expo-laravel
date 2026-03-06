<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get user's notifications
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        $query = Notification::forUser($userId);

        // Filter by read status
        if ($request->has('unread_only') && $request->boolean('unread_only')) {
            $query->unread();
        }

        // Filter by type
        if ($type = $request->input('type')) {
            $query->ofType($type);
        }

        $notifications = $query->latest()->paginate(20);

        // Get unread count
        $unreadCount = Notification::forUser($userId)->unread()->count();

        $response = ApiResponse::paginated($notifications);
        $data = $response->getData(true);
        $data['pagination']['unread_count'] = $unreadCount;

        return response()->json($data);
    }

    /**
     * Get unread count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        $count = Notification::getUnreadCountForUser($userId);

        return ApiResponse::success([
            'unread_count' => $count,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        // Ensure user owns this notification
        if ($notification->user_id !== $userId) {
            return ApiResponse::forbidden(
                __('messages.forbidden')
            );
        }

        $notification->markAsRead();

        return ApiResponse::success(
            new NotificationResource($notification),
            __('messages.notification.marked_as_read')
        );
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');

        $count = Notification::markAllAsReadForUser($userId);

        return ApiResponse::success([
            'marked_count' => $count,
        ], __('messages.notification.all_marked_as_read'));
    }
}
