<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

use AppModelsSection;
use AppModelsSpace;

class EventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $q = Event::published();
        if ($s = $request->input('search')) $q->search($s);
        if ($c = $request->input('city')) $q->inCity($c);
        if ($request->boolean('featured')) $q->featured();
        return ApiResponse::paginated($q->orderBy('createdAt','desc')->paginate($request->input('per_page',15)));
    }
    public function featured(): JsonResponse
    {
        return ApiResponse::success(Event::published()->featured()->limit(10)->get());
    }
    public function show(int $id): JsonResponse
    {
        return ApiResponse::success(Event::findOrFail($id));
    }
    public function sections(int $eventId): JsonResponse
    {
        Event::findOrFail($eventId);
        return ApiResponse::success(Section::where('eventId',$eventId)->get());
    }
    public function spaces(Request $request, int $eventId): JsonResponse
    {
        Event::findOrFail($eventId);
        return ApiResponse::paginated(Space::where('eventId',$eventId)->paginate(15));
    }
}
