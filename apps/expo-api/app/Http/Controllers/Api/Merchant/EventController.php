<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\PageView;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use App\Traits\TracksPlatform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    use SafeOrderBy, TracksPlatform;

    /**
     * List published events for merchant to browse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Event::where('status', 'published')
            ->with(['city:id,name,name_ar', 'category:id,name,name_ar']);

        // Filter by city
        if ($request->has('city_id')) {
            $query->where('city_id', $request->input('city_id'));
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->where('start_date', '>=', $request->input('from_date'));
        }

        if ($request->has('to_date')) {
            $query->where('end_date', '<=', $request->input('to_date'));
        }

        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Has available spaces
        if ($request->boolean('has_available_spaces', false)) {
            $query->whereHas('spaces', function ($q) {
                $q->where('status', 'available');
            });
        }

        // Safe sorting
        $this->applySafeOrder($query, $request, [
            'created_at', 'start_date', 'name', 'name_ar'
        ], 'start_date', 'asc');

        $events = $query->paginate($request->input('per_page', 15));

        return ApiResponse::success($events);
    }

    /**
     * Show event details with available spaces
     */
    public function show(Request $request, Event $event): JsonResponse
    {
        // Only show published events
        if ($event->status !== 'published') {
            return ApiResponse::error(
                message: __('messages.event.not_found'),
                errorCode: \App\Support\ApiErrorCode::RESOURCE_NOT_FOUND,
                httpCode: 404
            );
        }

        $event->load([
            'city:id,name,name_ar',
            'category:id,name,name_ar',
            'sections:id,event_id,name,name_ar',
        ]);

        // Track page view
        PageView::track($event, $this->getCurrentUserId($request), $this->getPlatform($request));

        // Get available spaces count
        $event->available_spaces_count = $event->spaces()
            ->where('status', 'available')
            ->count();

        return ApiResponse::success($event);
    }

    /**
     * List sections for an event
     */
    public function sections(Request $request, Event $event): JsonResponse
    {
        if ($event->status !== 'published') {
            return ApiResponse::error(
                message: __('messages.event.not_found'),
                errorCode: \App\Support\ApiErrorCode::RESOURCE_NOT_FOUND,
                httpCode: 404
            );
        }

        $sections = $event->sections()
            ->withCount(['spaces as available_spaces_count' => function ($q) {
                $q->where('status', 'available');
            }])
            ->get();

        return ApiResponse::success($sections);
    }

    /**
     * List available spaces for an event
     */
    public function spaces(Request $request, Event $event): JsonResponse
    {
        if ($event->status !== 'published') {
            return ApiResponse::error(
                message: __('messages.event.not_found'),
                errorCode: \App\Support\ApiErrorCode::RESOURCE_NOT_FOUND,
                httpCode: 404
            );
        }

        $query = $event->spaces()
            ->where('status', 'available')
            ->with(['section:id,name,name_ar', 'services:id,name,name_ar,price']);

        // Filter by section
        if ($request->has('section_id')) {
            $query->where('section_id', $request->input('section_id'));
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price_total', '>=', $request->input('min_price'));
        }

        if ($request->has('max_price')) {
            $query->where('price_total', '<=', $request->input('max_price'));
        }

        // Filter by area range
        if ($request->has('min_area')) {
            $query->where('area_sqm', '>=', $request->input('min_area'));
        }

        if ($request->has('max_area')) {
            $query->where('area_sqm', '<=', $request->input('max_area'));
        }

        // Filter by space type
        if ($request->has('space_type')) {
            $query->where('space_type', $request->input('space_type'));
        }

        // Filter by service
        if ($request->has('service_id')) {
            $query->whereHas('services', function ($q) use ($request) {
                $q->where('services.id', $request->input('service_id'));
            });
        }

        $spaces = $query->paginate($request->input('per_page', 15));

        return ApiResponse::success($spaces);
    }
}
