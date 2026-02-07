<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventListResource;
use App\Http\Resources\EventResource;
use App\Http\Resources\SpaceListResource;
use App\Models\Event;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Get events list with filters
     */
    public function index(Request $request): JsonResponse
    {
        $query = Event::with(['category', 'city'])
            ->published();

        // Search
        if ($search = $request->input('search')) {
            $query->search($search);
        }

        // Filter by city
        if ($cityId = $request->input('city_id')) {
            $query->inCity($cityId);
        }

        // Filter by category
        if ($categoryId = $request->input('category_id')) {
            $query->inCategory($categoryId);
        }

        // Filter by status (ongoing, upcoming)
        if ($status = $request->input('status')) {
            match($status) {
                'ongoing' => $query->ongoing(),
                'upcoming' => $query->upcoming(),
                default => null,
            };
        }

        // Filter by featured
        if ($request->boolean('featured')) {
            $query->featured();
        }

        // Filter by date range
        if ($startDate = $request->input('start_date')) {
            $query->where('start_date', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->where('end_date', '<=', $endDate);
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'start_date');
        $sortOrder = $request->input('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = min($request->input('per_page', 15), 50);
        $events = $query->paginate($perPage);

        return ApiResponse::paginated(
            $events->through(fn($event) => new EventListResource($event))
        );
    }

    /**
     * Get featured events
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = min($request->input('limit', 10), 20);

        $events = Event::with(['category', 'city'])
            ->published()
            ->featured()
            ->active()
            ->orderBy('start_date')
            ->limit($limit)
            ->get();

        return ApiResponse::success(
            EventListResource::collection($events)
        );
    }

    /**
     * Get single event
     */
    public function show(Event $event): JsonResponse
    {
        // Only show published events to public
        if ($event->status->value !== 'published') {
            return ApiResponse::notFound(
                __('messages.event.not_found'),
                'event'
            );
        }

        $event->load(['category', 'city']);
        $event->loadCount('spaces');

        // Increment views
        $event->incrementViews();

        return ApiResponse::success(
            new EventResource($event)
        );
    }

    /**
     * Get event spaces
     */
    public function spaces(Request $request, Event $event): JsonResponse
    {
        if ($event->status->value !== 'published') {
            return ApiResponse::notFound(
                __('messages.event.not_found'),
                'event'
            );
        }

        $query = $event->spaces();

        // Filter by availability
        if ($request->boolean('available_only')) {
            $query->available();
        }

        // Filter by price range
        $query->inPriceRange(
            $request->input('min_price'),
            $request->input('max_price')
        );

        // Filter by area range
        $query->inAreaRange(
            $request->input('min_area'),
            $request->input('max_area')
        );

        // Filter by floor
        if ($floor = $request->input('floor')) {
            $query->where('floor_number', $floor);
        }

        // Filter by section
        if ($section = $request->input('section')) {
            $query->where('section', $section);
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'location_code');
        $sortOrder = $request->input('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $spaces = $query->get();

        return ApiResponse::success(
            SpaceListResource::collection($spaces)
        );
    }
}
