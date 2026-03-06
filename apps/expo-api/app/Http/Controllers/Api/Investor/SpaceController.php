<?php

namespace App\Http\Controllers\Api\Investor;

use App\Enums\SpaceStatus;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Space;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use App\Traits\TracksPlatform;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class SpaceController extends Controller
{
    use TracksPlatform;

    /**
     * List investor's own spaces
     */
    public function index(Request $request): JsonResponse
    {
        $investorId = $request->input('auth_user_id');

        $query = Space::where('investor_id', $investorId)
            ->with(['event:id,name,name_ar', 'section:id,name,name_ar', 'services:id,name,name_ar']);

        // Filters
        if ($request->has('event_id')) {
            $query->where('event_id', $request->input('event_id'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%")
                    ->orWhere('location_code', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $allowedSorts = ['created_at', 'name', 'price_total', 'area_sqm', 'status'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        $spaces = $query->paginate($request->input('per_page', 15));

        return ApiResponse::paginated($spaces);
    }

    /**
     * Create a new space on an event
     */
    public function store(Request $request): JsonResponse
    {
        $investorId = $request->input('auth_user_id');

        $validated = $request->validate([
            'event_id' => 'required|uuid|exists:events,id',
            'section_id' => 'nullable|uuid|exists:sections,id',
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'location_code' => 'required|string|max:50',
            'area_sqm' => 'required|numeric|min:1',
            'price_per_day' => 'nullable|numeric|min:0',
            'price_total' => 'required|numeric|min:0',
            'floor_number' => 'nullable|integer',
            'space_type' => 'nullable|string',
            'payment_system' => 'nullable|string',
            'rental_duration' => 'nullable|string',
            'amenities' => 'nullable|array',
            'amenities_ar' => 'nullable|array',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'address' => 'nullable|string',
            'address_ar' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
            'images_360' => 'nullable|array',
            'images_360.*' => 'image|max:10240',
            'services' => 'nullable|array',
            'services.*' => 'uuid|exists:services,id',
        ]);

        // Check if location_code is unique for this event
        $exists = Space::where('event_id', $validated['event_id'])
            ->where('location_code', $validated['location_code'])
            ->exists();

        if ($exists) {
            return ApiResponse::error(
                message: __('messages.space.location_code_exists'),
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                httpCode: 422
            );
        }

        DB::beginTransaction();

        try {
            // Handle image uploads
            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imagePaths[] = $image->store('spaces', 'public');
                }
            }

            $images360Paths = [];
            if ($request->hasFile('images_360')) {
                foreach ($request->file('images_360') as $image) {
                    $images360Paths[] = $image->store('spaces/360', 'public');
                }
            }

            $space = Space::create([
                ...$validated,
                'investor_id' => $investorId,
                'images' => $imagePaths,
                'images_360' => $images360Paths,
                'status' => SpaceStatus::AVAILABLE,
                ...$this->getTrackingData($request),
            ]);

            // Attach services
            if (!empty($validated['services'])) {
                $space->services()->attach($validated['services']);
            }

            DB::commit();

            $space->load(['event:id,name,name_ar', 'section:id,name,name_ar', 'services:id,name,name_ar']);

            return ApiResponse::success(
                data: $space,
                message: __('messages.space.created'),
                httpCode: 201
            );
        } catch (\Exception $e) {
            DB::rollBack();

            // Clean up uploaded files
            foreach ($imagePaths as $path) {
                Storage::disk('public')->delete($path);
            }
            foreach ($images360Paths as $path) {
                Storage::disk('public')->delete($path);
            }

            return ApiResponse::error(
                message: __('messages.error.server'),
                errorCode: ApiErrorCode::INTERNAL_SERVER_ERROR,
                httpCode: 500
            );
        }
    }

    /**
     * Show space details
     */
    public function show(Request $request, Space $space): JsonResponse
    {
        $investorId = $request->input('auth_user_id');

        // Ensure investor owns this space
        if ($space->investor_id !== $investorId) {
            return ApiResponse::error(
                message: __('messages.auth.forbidden'),
                errorCode: ApiErrorCode::PERMISSION_DENIED,
                httpCode: 403
            );
        }

        $space->load([
            'event:id,name,name_ar,start_date,end_date',
            'section:id,name,name_ar',
            'services:id,name,name_ar,price',
            'rentalRequests' => fn($q) => $q->with('businessProfile:id,company_name,company_name_ar')->latest()->limit(10),
        ]);

        return ApiResponse::success($space);
    }

    /**
     * Update space
     */
    public function update(Request $request, Space $space): JsonResponse
    {
        $investorId = $request->input('auth_user_id');

        // Ensure investor owns this space
        if ($space->investor_id !== $investorId) {
            return ApiResponse::error(
                message: __('messages.auth.forbidden'),
                errorCode: ApiErrorCode::PERMISSION_DENIED,
                httpCode: 403
            );
        }

        $validated = $request->validate([
            'section_id' => 'nullable|uuid|exists:sections,id',
            'name' => 'sometimes|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'location_code' => [
                'sometimes',
                'string',
                'max:50',
                Rule::unique('spaces')->where(function ($query) use ($space) {
                    return $query->where('event_id', $space->event_id);
                })->ignore($space->id),
            ],
            'area_sqm' => 'sometimes|numeric|min:1',
            'price_per_day' => 'nullable|numeric|min:0',
            'price_total' => 'sometimes|numeric|min:0',
            'floor_number' => 'nullable|integer',
            'amenities' => 'nullable|array',
            'amenities_ar' => 'nullable|array',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'address' => 'nullable|string',
            'address_ar' => 'nullable|string',
            'services' => 'nullable|array',
            'services.*' => 'uuid|exists:services,id',
        ]);

        // Handle image uploads if provided
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('spaces', 'public');
            }
            $validated['images'] = array_merge($space->images ?? [], $imagePaths);
        }

        if ($request->hasFile('images_360')) {
            $images360Paths = [];
            foreach ($request->file('images_360') as $image) {
                $images360Paths[] = $image->store('spaces/360', 'public');
            }
            $validated['images_360'] = array_merge($space->images_360 ?? [], $images360Paths);
        }

        $space->update($validated);

        // Sync services if provided
        if (isset($validated['services'])) {
            $space->services()->sync($validated['services']);
        }

        $space->load(['event:id,name,name_ar', 'section:id,name,name_ar', 'services:id,name,name_ar']);

        return ApiResponse::success(
            data: $space,
            message: __('messages.space.updated')
        );
    }

    /**
     * Delete space (only if not rented)
     */
    public function destroy(Request $request, Space $space): JsonResponse
    {
        $investorId = $request->input('auth_user_id');

        // Ensure investor owns this space
        if ($space->investor_id !== $investorId) {
            return ApiResponse::error(
                message: __('messages.auth.forbidden'),
                errorCode: ApiErrorCode::PERMISSION_DENIED,
                httpCode: 403
            );
        }

        // Cannot delete rented space
        if ($space->status === SpaceStatus::RENTED) {
            return ApiResponse::error(
                message: __('messages.space.cannot_delete_rented'),
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                httpCode: 422
            );
        }

        // Check for active rental requests
        $hasActiveRentals = $space->rentalRequests()
            ->whereIn('status', ['approved', 'completed'])
            ->where('end_date', '>=', now()->toDateString())
            ->exists();

        if ($hasActiveRentals) {
            return ApiResponse::error(
                message: __('messages.space.has_active_rentals'),
                errorCode: ApiErrorCode::VALIDATION_FAILED,
                httpCode: 422
            );
        }

        $space->delete();

        return ApiResponse::success(
            message: __('messages.space.deleted')
        );
    }

    /**
     * Add services to space
     */
    public function addServices(Request $request, Space $space): JsonResponse
    {
        $investorId = $request->input('auth_user_id');

        if ($space->investor_id !== $investorId) {
            return ApiResponse::error(
                message: __('messages.auth.forbidden'),
                errorCode: ApiErrorCode::PERMISSION_DENIED,
                httpCode: 403
            );
        }

        $validated = $request->validate([
            'services' => 'required|array|min:1',
            'services.*' => 'uuid|exists:services,id',
        ]);

        $space->services()->syncWithoutDetaching($validated['services']);

        $space->load('services:id,name,name_ar,price');

        return ApiResponse::success(
            data: $space,
            message: __('messages.space.services_updated')
        );
    }

    /**
     * Remove services from space
     */
    public function removeServices(Request $request, Space $space): JsonResponse
    {
        $investorId = $request->input('auth_user_id');

        if ($space->investor_id !== $investorId) {
            return ApiResponse::error(
                message: __('messages.auth.forbidden'),
                errorCode: ApiErrorCode::PERMISSION_DENIED,
                httpCode: 403
            );
        }

        $validated = $request->validate([
            'services' => 'required|array|min:1',
            'services.*' => 'uuid|exists:services,id',
        ]);

        $space->services()->detach($validated['services']);

        $space->load('services:id,name,name_ar,price');

        return ApiResponse::success(
            data: $space,
            message: __('messages.space.services_updated')
        );
    }
}
