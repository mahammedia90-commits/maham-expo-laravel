<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Models\PageView;
use App\Models\Space;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use App\Traits\TracksPlatform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Merchant Space Controller
 *
 * Merchants can browse available spaces (read-only).
 * They cannot create, update, or delete spaces.
 */
class SpaceController extends Controller
{
    use SafeOrderBy, TracksPlatform;

    /**
     * Browse available spaces across all published events
     */
    public function index(Request $request): JsonResponse
    {
        $query = Space::where('status', 'available')
            ->whereHas('event', fn($q) => $q->where('status', 'published'))
            ->with([
                'event:id,name,name_ar,start_date,end_date',
                'section:id,name,name_ar',
                'services:id,name,name_ar,price',
            ]);

        // Filter by event
        if ($request->has('event_id')) {
            $query->where('event_id', $request->input('event_id'));
        }

        // Filter by section
        if ($request->has('section_id')) {
            $query->where('section_id', $request->input('section_id'));
        }

        // Filter by space type
        if ($request->has('space_type')) {
            $query->where('space_type', $request->input('space_type'));
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

        // Filter by floor
        if ($request->has('floor_number')) {
            $query->where('floor_number', $request->input('floor_number'));
        }

        // Filter by service
        if ($request->has('service_id')) {
            $query->whereHas('services', function ($q) use ($request) {
                $q->where('services.id', $request->input('service_id'));
            });
        }

        // Search
        if ($search = $this->sanitizeSearch($request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%")
                    ->orWhere('location_code', 'like', "%{$search}%");
            });
        }

        $this->applySafeOrder($query, $request, [
            'name', 'price_total', 'area_sqm', 'created_at', 'floor_number',
        ], 'created_at', 'desc');

        $spaces = $query->paginate($request->input('per_page', 15));

        return ApiResponse::success($spaces);
    }

    /**
     * Show space details
     */
    public function show(Request $request, Space $space): JsonResponse
    {
        // Only show available spaces from published events
        if ($space->status !== 'available') {
            return ApiResponse::error(
                message: __('messages.space.not_available'),
                errorCode: ApiErrorCode::SPACE_NOT_AVAILABLE,
                httpCode: 404
            );
        }

        $space->load([
            'event:id,name,name_ar,start_date,end_date,city_id',
            'event.city:id,name,name_ar',
            'section:id,name,name_ar',
            'services:id,name,name_ar,price,description,description_ar',
        ]);

        // Track page view
        PageView::track($space, $this->getCurrentUserId($request), $this->getPlatform($request));

        return ApiResponse::success($space);
    }
}
