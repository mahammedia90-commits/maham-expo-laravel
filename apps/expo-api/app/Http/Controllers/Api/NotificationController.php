<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = $request->header('X-User-Id', 0);
        $notifs = Notification::where('userId', $userId)->orderBy('createdAt', 'desc')->paginate(15);
        return ApiResponse::paginated($notifs);
    }
    public function unreadCount(Request $request): JsonResponse
    {
        $userId = $request->header('X-User-Id', 0);
        $count = Notification::where('userId', $userId)->whereNull('readAt')->count();
        return ApiResponse::success(['count' => $count]);
    }
    public function markAsRead(int $id): JsonResponse
    {
        Notification::where('id', $id)->update(['readAt' => now()]);
        return ApiResponse::success(['message' => 'تم']);
    }
    public function markAllAsRead(Request $request): JsonResponse
    {
        $userId = $request->header('X-User-Id', 0);
        Notification::where('userId', $userId)->whereNull('readAt')->update(['readAt' => now()]);
        return ApiResponse::success(['message' => 'تم']);
    }
}
