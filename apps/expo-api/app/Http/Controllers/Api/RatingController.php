<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = Rating::where('status', 'approved');
        if ($eventId = $request->input('event_id')) $q->where('eventId', $eventId);
        return ApiResponse::paginated($q->orderBy('createdAt', 'desc')->paginate(15));
    }
    
    public function store(Request $request): JsonResponse
    {
        $request->validate(['event_id' => 'required|integer', 'rating' => 'required|integer|min:1|max:5']);
        $rating = Rating::create([
            'userId' => $request->header('X-User-Id', 0),
            'eventId' => $request->event_id,
            'rating' => $request->rating,
            'comment' => $request->input('comment'),
            'status' => 'pending',
        ]);
        return ApiResponse::success($rating, 'شكراً لتقييمك', 201);
    }
    
    public function summary(Request $request): JsonResponse
    {
        $eventId = $request->input('event_id');
        $avg = Rating::where('status', 'approved')->where('eventId', $eventId)->avg('rating');
        $count = Rating::where('status', 'approved')->where('eventId', $eventId)->count();
        return ApiResponse::success(['average' => round($avg ?? 0, 2), 'count' => $count]);
    }
}
