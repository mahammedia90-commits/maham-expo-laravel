<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventListResource;
use App\Http\Resources\EventResource;
use App\Http\Resources\SectionResource;
use App\Http\Resources\SpaceListResource;
use App\Models\Event;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    use SafeOrderBy;
    /**
     * Get events list with filters
     */
    public function index(Request $request): JsonResponse
    {
        $query = Event::with(['category', 'city'])
            ->published();

        // Search (sanitized)
        if ($search = $this->sanitizeSearch($request->input('search'))) {
            $query->search($search);
        }

        // Filter by city
        if ($cityId = $request->input('city_id')) {
            $query->inCity($cityId);
        }

        // Filter by category (event type)
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

        // Filter by rental duration (events that have spaces with this rental duration)
        if ($rentalDuration = $request->input('rental_duration')) {
            $query->hasSpacesWithRentalDuration($rentalDuration);
        }

        // Filter by price range (events that have spaces in this price range)
        if ($request->input('min_price') !== null || $request->input('max_price') !== null) {
            $query->hasSpacesInPriceRange(
                $request->input('min_price') ? (float) $request->input('min_price') : null,
                $request->input('max_price') ? (float) $request->input('max_price') : null
            );
        }

        // Filter by area range (events that have spaces in this area range)
        if ($request->input('min_area') !== null || $request->input('max_area') !== null) {
            $query->hasSpacesInAreaRange(
                $request->input('min_area') ? (float) $request->input('min_area') : null,
                $request->input('max_area') ? (float) $request->input('max_area') : null
            );
        }

        // Sorting (safe - whitelisted columns only)
        $this->applySafeOrder($query, $request, [
            'start_date', 'end_date', 'created_at', 'name', 'name_ar',
        ], 'start_date', 'asc');

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
     * Get single event with sections
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

        $event->load(['category', 'city', 'sections' => function ($q) {
            $q->active()->ordered();
        }]);
        $event->loadCount('spaces');

        // Increment views
        $event->incrementViews();

        return ApiResponse::success(
            new EventResource($event)
        );
    }

    /**
     * Get event sections with spaces count
     */
    public function sections(Request $request, Event $event): JsonResponse
    {
        if ($event->status->value !== 'published') {
            return ApiResponse::notFound(
                __('messages.event.not_found'),
                'event'
            );
        }

        $sections = $event->sections()
            ->active()
            ->ordered()
            ->paginate($request->input('per_page', 15));

        return ApiResponse::paginated($sections);
    }

    /**
     * Get event spaces with filters
     */
    public function spaces(Request $request, Event $event): JsonResponse
    {
        if ($event->status->value !== 'published') {
            return ApiResponse::notFound(
                __('messages.event.not_found'),
                'event'
            );
        }

        $query = $event->spaces()->with('section');

        // Filter by availability
        if ($request->boolean('available_only')) {
            $query->available();
        }

        // Filter by section
        if ($sectionId = $request->input('section_id')) {
            $query->inSection($sectionId);
        }

        // Filter by space type
        if ($spaceType = $request->input('space_type')) {
            $query->ofType($spaceType);
        }

        // Filter by payment system
        if ($paymentSystem = $request->input('payment_system')) {
            $query->withPaymentSystem($paymentSystem);
        }

        // Filter by rental duration
        if ($rentalDuration = $request->input('rental_duration')) {
            $query->withRentalDuration($rentalDuration);
        }

        // Filter by service
        if ($serviceId = $request->input('service_id')) {
            $query->hasService($serviceId);
        }

        // Filter by price range
        $query->inPriceRange(
            $request->input('min_price') ? (float) $request->input('min_price') : null,
            $request->input('max_price') ? (float) $request->input('max_price') : null
        );

        // Filter by area range
        $query->inAreaRange(
            $request->input('min_area') ? (float) $request->input('min_area') : null,
            $request->input('max_area') ? (float) $request->input('max_area') : null
        );

        // Filter by floor
        if ($floor = $request->input('floor')) {
            $query->where('floor_number', $floor);
        }

        // Sorting (safe - whitelisted columns only)
        $this->applySafeOrder($query, $request, [
            'location_code', 'area_sqm', 'price_per_day', 'price_total',
            'status', 'floor_number', 'created_at',
        ], 'location_code', 'asc');

        // Return grouped by section if requested
        if ($request->boolean('grouped_by_section')) {
            $spaces = $query->get();
            $grouped = $spaces->groupBy('section_id')->map(function ($sectionSpaces, $sectionId) {
                $section = $sectionSpaces->first()->section;
                return [
                    'section' => $section ? [
                        'id' => $section->id,
                        'name' => $section->localized_name,
                    ] : null,
                    'spaces' => SpaceListResource::collection($sectionSpaces),
                ];
            })->values();

            return ApiResponse::success($grouped);
        }

        $spaces = $query->paginate($request->input('per_page', 15));

        return ApiResponse::paginated($spaces);
    }
}
