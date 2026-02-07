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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SpaceController extends Controller
{
    /**
     * Get event spaces (admin)
     */
    public function index(Request $request, Event $event): JsonResponse
    {
        $query = $event->spaces();

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter by floor
        if ($floor = $request->input('floor')) {
            $query->where('floor_number', $floor);
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

    /**
     * Create space for event
     */
    public function store(StoreSpaceRequest $request, Event $event): JsonResponse
    {
        $data = $request->validated();
        $data['event_id'] = $event->id;

        // Handle images
        if ($request->hasFile('images')) {
            $data['images'] = $this->uploadImages($request->file('images'));
        }

        $space = Space::create($data);
        $space->load('event');

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
        $space->load('event.city');

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

        // Handle images
        if ($request->hasFile('images')) {
            // Delete old images
            if ($space->images) {
                foreach ($space->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            $data['images'] = $this->uploadImages($request->file('images'));
        }

        $space->update($data);
        $space->load('event');

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

        $space->delete();

        return ApiResponse::success(
            null,
            __('messages.space.deleted')
        );
    }

    /**
     * Upload images
     */
    protected function uploadImages(array $files): array
    {
        $paths = [];
        $uploadPath = config('expo-api.uploads.paths.spaces');

        foreach ($files as $file) {
            $paths[] = $file->store($uploadPath, 'public');
        }

        return $paths;
    }
}
