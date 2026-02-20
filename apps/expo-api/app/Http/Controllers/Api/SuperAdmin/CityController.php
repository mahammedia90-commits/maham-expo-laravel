<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Models\City;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CityController extends Controller
{
    use SafeOrderBy;

    /**
     * List all cities
     */
    public function index(Request $request): JsonResponse
    {
        $query = City::query();

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search
        if ($search = $this->sanitizeSearch($request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%")
                    ->orWhere('region', 'like', "%{$search}%")
                    ->orWhere('region_ar', 'like', "%{$search}%");
            });
        }

        $this->applySafeOrder($query, $request, [
            'name', 'name_ar', 'region', 'sort_order', 'created_at', 'is_active',
        ], 'sort_order', 'asc');

        $cities = $query->paginate($request->input('per_page', 15));

        return ApiResponse::success($cities);
    }

    /**
     * Create a new city
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'region' => 'nullable|string|max:255',
            'region_ar' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $city = City::create($validated);

        return ApiResponse::created(
            new CityResource($city),
            __('messages.city.created')
        );
    }

    /**
     * Show a single city
     */
    public function show(City $city): JsonResponse
    {
        $city->loadCount('events');

        return ApiResponse::success(
            new CityResource($city)
        );
    }

    /**
     * Update a city
     */
    public function update(Request $request, City $city): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'name_ar' => 'sometimes|string|max:255',
            'region' => 'nullable|string|max:255',
            'region_ar' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $city->update($validated);

        return ApiResponse::success(
            new CityResource($city),
            __('messages.city.updated')
        );
    }

    /**
     * Delete a city
     */
    public function destroy(City $city): JsonResponse
    {
        // Prevent deletion if city has events
        if ($city->events()->exists()) {
            return ApiResponse::error(
                message: __('messages.city.has_events'),
                errorCode: ApiErrorCode::RESOURCE_DELETION_FAILED,
                httpCode: 422
            );
        }

        $city->delete();

        return ApiResponse::success(
            null,
            __('messages.city.deleted')
        );
    }
}
