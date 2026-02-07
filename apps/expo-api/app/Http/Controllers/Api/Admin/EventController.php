<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEventRequest;
use App\Http\Requests\Admin\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Http\Resources\EventListResource;
use App\Models\Event;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Get all events (admin)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Event::with(['category', 'city']);

        // Search
        if ($search = $request->input('search')) {
            $query->search($search);
        }

        // Filter by status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter by city
        if ($cityId = $request->input('city_id')) {
            $query->inCity($cityId);
        }

        // Filter by category
        if ($categoryId = $request->input('category_id')) {
            $query->inCategory($categoryId);
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $events = $query->paginate(15);

        return ApiResponse::paginated(
            $events->through(fn($event) => new EventListResource($event))
        );
    }

    /**
     * Create event
     */
    public function store(StoreEventRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['created_by'] = $request->input('auth_user_id');

        // Handle images
        if ($request->hasFile('images')) {
            $data['images'] = $this->uploadImages($request->file('images'));
        }

        $event = Event::create($data);
        $event->load(['category', 'city']);

        return ApiResponse::created(
            new EventResource($event),
            __('messages.event.created')
        );
    }

    /**
     * Get single event
     */
    public function show(Event $event): JsonResponse
    {
        $event->load(['category', 'city']);
        $event->loadCount('spaces');

        return ApiResponse::success(
            new EventResource($event)
        );
    }

    /**
     * Update event
     */
    public function update(UpdateEventRequest $request, Event $event): JsonResponse
    {
        $data = $request->validated();

        // Handle images
        if ($request->hasFile('images')) {
            // Delete old images
            if ($event->images) {
                foreach ($event->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            $data['images'] = $this->uploadImages($request->file('images'));
        }

        $event->update($data);
        $event->load(['category', 'city']);

        return ApiResponse::success(
            new EventResource($event),
            __('messages.event.updated')
        );
    }

    /**
     * Delete event
     */
    public function destroy(Event $event): JsonResponse
    {
        // Check if event has active requests
        $hasActiveRequests = $event->visitRequests()
            ->whereIn('status', ['pending', 'approved'])
            ->exists();

        if ($hasActiveRequests) {
            return ApiResponse::error(
                __('messages.event.has_active_requests'),
                'event_has_active_requests'
            );
        }

        // Delete images
        if ($event->images) {
            foreach ($event->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $event->delete();

        return ApiResponse::success(
            null,
            __('messages.event.deleted')
        );
    }

    /**
     * Upload images
     */
    protected function uploadImages(array $files): array
    {
        $paths = [];
        $uploadPath = config('expo-api.uploads.paths.events');

        foreach ($files as $file) {
            $paths[] = $file->store($uploadPath, 'public');
        }

        return $paths;
    }
}
