<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Space;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpaceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = Space::query();
        if ($eventId = $request->input('event_id')) $q->where('eventId', $eventId);
        if ($request->boolean('available')) $q->available();
        return ApiResponse::paginated($q->paginate($request->input('per_page', 15)));
    }
    public function show(int $id): JsonResponse
    {
        return ApiResponse::success(Space::with('event')->findOrFail($id));
    }
}
