<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSpaceRequest;
use App\Http\Requests\Admin\UpdateSpaceRequest;
use App\Http\Resources\SpaceResource;
use App\Http\Resources\SpaceListResource;
use App\Models\Event;
use App\Models\Space;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SpaceController extends Controller
{
    use SafeOrderBy;
    /**
     * Get event spaces (admin)
     */
    public function index(Request $request, Event $event): JsonResponse
    {
        $query = $event->spaces()->with('section');

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter by section
        if ($sectionId = $request->input('section_id')) {
            $query->inSection($sectionId);
        }

        // Filter by space type
        if ($spaceType = $request->input('space_type')) {
            $query->ofType($spaceType);
        }

        // Filter by floor
        if ($floor = $request->input('floor')) {
            $query->where('floor_number', $floor);
        }

        // Sorting (safe - whitelisted columns only)
        $this->applySafeOrder($query, $request, [
            'location_code', 'area_sqm', 'price_per_day', 'price_total',
            'status', 'floor_number', 'created_at',
        ], 'location_code', 'asc');

        $spaces = $query->paginate($request->input('per_page', 15));

        return ApiResponse::paginated($spaces);
    }

    /**
     * Create space for event
     */
    public function store(StoreSpaceRequest $request, Event $event): JsonResponse
    {
        $data = $request->validated();
        $data['event_id'] = $event->id;

        // Remove services from data (handled separately)
        $services = $data['services'] ?? null;
        unset($data['services']);

        // Handle images
        if ($request->hasFile('images')) {
            $data['images'] = $this->uploadImages($request->file('images'), 'spaces');
        }

        // Handle 360 images
        if ($request->hasFile('images_360')) {
            $data['images_360'] = $this->uploadImages($request->file('images_360'), 'spaces/360');
        }

        $space = Space::create($data);

        // Sync services
        if ($services) {
            $space->services()->sync($services);
        }

        $space->load(['event', 'section', 'services']);

        return ApiResponse::created(
            new SpaceResource($space),
            __('messages.space.created')
        );
    }

    /**
     * Get single space
     */
    public function show(Space $space): JsonResponse
    {
        $space->load(['event.city', 'section', 'services']);

        return ApiResponse::success(
            new SpaceResource($space)
        );
    }

    /**
     * Update space
     */
    public function update(UpdateSpaceRequest $request, Space $space): JsonResponse
    {
        $data = $request->validated();

        // Remove services from data (handled separately)
        $services = $data['services'] ?? null;
        unset($data['services']);

        // Handle images
        if ($request->hasFile('images')) {
            // Delete old images
            if ($space->images) {
                foreach ($space->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            $data['images'] = $this->uploadImages($request->file('images'), 'spaces');
        }

        // Handle 360 images
        if ($request->hasFile('images_360')) {
            if ($space->images_360) {
                foreach ($space->images_360 as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            $data['images_360'] = $this->uploadImages($request->file('images_360'), 'spaces/360');
        }

        $space->update($data);

        // Sync services if provided
        if ($services !== null) {
            $space->services()->sync($services);
        }

        $space->load(['event', 'section', 'services']);

        return ApiResponse::success(
            new SpaceResource($space),
            __('messages.space.updated')
        );
    }

    /**
     * Delete space
     */
    public function destroy(Space $space): JsonResponse
    {
        // Check if space has active rentals
        $hasActiveRentals = $space->rentalRequests()
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($hasActiveRentals) {
            return ApiResponse::error(
                __('messages.space.has_active_rentals'),
                'space_has_active_rentals'
            );
        }

        // Delete images
        if ($space->images) {
            foreach ($space->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }
        if ($space->images_360) {
            foreach ($space->images_360 as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        // Detach services
        $space->services()->detach();

        $space->delete();

        return ApiResponse::success(
            null,
            __('messages.space.deleted')
        );
    }

    /**
     * Upload images
     */
    protected function uploadImages(array $files, string $folder = 'spaces'): array
    {
        $paths = [];

        foreach ($files as $file) {
            $paths[] = $file->store($folder, 'public');
        }

        return $paths;
    }
}
