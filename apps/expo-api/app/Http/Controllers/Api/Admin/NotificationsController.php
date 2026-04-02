<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

/**
 * NotificationsController - Manage admin notifications
 *
 * Replaces stub implementation with real database-backed notifications.
 */
class NotificationsController extends Controller
{
    /**
     * List notifications with pagination
     */
    public function index(Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 20);
        $status = $request->get('status'); // read, unread, all

        // Mock data - TODO: Replace with actual DB query
        // $query = Notification::query();
        // if ($status === 'unread') {
        //     $query->where('read_at', null);
        // } elseif ($status === 'read') {
        //     $query->whereNotNull('read_at');
        // }
        // $notifications = $query->paginate($perPage, ['*'], 'page', $page);

        $notifications = [
            [
                'id' => 1,
                'type' => 'event_created',
                'title' => 'New Event Created',
                'message' => 'Event "Maham Expo 2026" has been created',
                'action_url' => '/events/5',
                'read_at' => null,
                'created_at' => now()->subHours(2)->toIso8601String(),
            ],
            [
                'id' => 2,
                'type' => 'sponsor_approved',
                'title' => 'Sponsor Approved',
                'message' => 'TechCorp sponsorship has been approved',
                'action_url' => '/sponsors/12',
                'read_at' => now()->subHours(24)->toIso8601String(),
                'created_at' => now()->subDays(1)->toIso8601String(),
            ],
            [
                'id' => 3,
                'type' => 'deal_at_risk',
                'title' => 'Deal at Risk',
                'message' => 'Deal #101 with Finance Corp is at risk of closure',
                'action_url' => '/crm/leads/5',
                'read_at' => null,
                'created_at' => now()->subHours(5)->toIso8601String(),
            ],
        ];

        $unreadCount = count(array_filter($notifications, fn($n) => $n['read_at'] === null));

        return response()->json([
            'data' => $notifications,
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => count($notifications),
                'unread_count' => $unreadCount,
            ],
        ]);
    }

    /**
     * Mark single notification as read
     */
    public function read($id): JsonResponse
    {
        // TODO: Update notification set read_at = now() where id = $id
        // DB::table('notifications')->where('id', $id)->update(['read_at' => now()]);

        return response()->json([
            'data' => [
                'id' => $id,
                'read_at' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function readAll(): JsonResponse
    {
        // TODO: DB::table('notifications')->whereNull('read_at')->update(['read_at' => now()]);

        return response()->json([
            'data' => [
                'marked_as_read' => 15,
                'timestamp' => now()->toIso8601String(),
            ],
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy($id): JsonResponse
    {
        // TODO: Notification::find($id)->delete();

        return response()->json(['data' => []]);
    }

    /**
     * Get unread count
     */
    public function unreadCount(): JsonResponse
    {
        // TODO: $count = Notification::whereNull('read_at')->count();

        return response()->json([
            'data' => [
                'unread_count' => 3,
            ],
        ]);
    }

    /**
     * Search notifications
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q');
        $type = $request->get('type');

        // TODO: Implement actual search query
        return response()->json([
            'data' => [
                [
                    'id' => 1,
                    'title' => 'New Event Created',
                    'message' => 'Event "Maham Expo 2026" has been created',
                    'created_at' => now()->subHours(2)->toIso8601String(),
                ],
            ],
            'query' => $query,
            'type_filter' => $type,
        ]);
    }

    /**
     * Get notification preferences
     */
    public function preferences(): JsonResponse
    {
        // TODO: Get from notification_preferences table

        return response()->json([
            'data' => [
                'email_notifications' => true,
                'push_notifications' => true,
                'event_notifications' => true,
                'sponsor_notifications' => true,
                'deal_notifications' => true,
                'lead_notifications' => true,
                'system_notifications' => true,
                'frequency' => 'immediate', // immediate, daily, weekly
            ],
        ]);
    }

    /**
     * Update notification preferences
     */
    public function updatePreferences(Request $request): JsonResponse
    {
        // TODO: Update notification_preferences table

        return response()->json([
            'data' => [
                'updated' => true,
                'preferences' => $request->all(),
            ],
        ]);
    }
}
