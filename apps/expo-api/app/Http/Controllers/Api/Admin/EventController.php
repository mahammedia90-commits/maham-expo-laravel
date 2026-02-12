<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEventRequest;
use App\Http\Requests\Admin\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Http\Resources\EventListResource;
use App\Models\Event;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    use SafeOrderBy;
    /**
     * Get all events (admin)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Event::with(['category', 'city']);

        // Search (sanitized)
        if ($search = $this->sanitizeSearch($request->input('search'))) {
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

        // Sorting (safe - whitelisted columns only)
        $this->applySafeOrder($query, $request, [
            'created_at', 'start_date', 'end_date', 'name', 'name_ar', 'status',
        ]);

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
            $data['images'] = $this->uploadImages($request->file('images'), 'events');
        }

        // Handle 360 images
        if ($request->hasFile('images_360')) {
            $data['images_360'] = $this->uploadImages($request->file('images_360'), 'events/360');
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
            $data['images'] = $this->uploadImages($request->file('images'), 'events');
        }

        // Handle 360 images
        if ($request->hasFile('images_360')) {
            if ($event->images_360) {
                foreach ($event->images_360 as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            $data['images_360'] = $this->uploadImages($request->file('images_360'), 'events/360');
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
        if ($event->images_360) {
            foreach ($event->images_360 as $image) {
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
    protected function uploadImages(array $files, string $folder = 'events'): array
    {
        $paths = [];

        foreach ($files as $file) {
            $paths[] = $file->store($folder, 'public');
        }

        return $paths;
    }
}
