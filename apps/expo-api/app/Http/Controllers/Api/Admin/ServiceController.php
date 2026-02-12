<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreServiceRequest;
use App\Http\Requests\Admin\UpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use SafeOrderBy;

    /**
     * Get all services (admin)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Service::query();

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search (sanitized)
        if ($search = $this->sanitizeSearch($request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('name_ar', 'like', "%{$search}%");
            });
        }

        $services = $query->ordered()->get();

        return ApiResponse::success(
            ServiceResource::collection($services)
        );
    }

    /**
     * Create service
     */
    public function store(StoreServiceRequest $request): JsonResponse
    {
        $service = Service::create($request->validated());

        return ApiResponse::created(
            new ServiceResource($service),
            __('messages.service.created')
        );
    }

    /**
     * Get single service
     */
    public function show(Service $service): JsonResponse
    {
        return ApiResponse::success(
            new ServiceResource($service)
        );
    }

    /**
     * Update service
     */
    public function update(UpdateServiceRequest $request, Service $service): JsonResponse
    {
        $service->update($request->validated());

        return ApiResponse::success(
            new ServiceResource($service),
            __('messages.service.updated')
        );
    }

    /**
     * Delete service
     */
    public function destroy(Service $service): JsonResponse
    {
        // Check if service is used by any spaces
        $hasSpaces = $service->spaces()->exists();

        if ($hasSpaces) {
            return ApiResponse::error(
                __('messages.service.has_spaces'),
                'service_has_spaces'
            );
        }

        $service->delete();

        return ApiResponse::success(
            null,
            __('messages.service.deleted')
        );
    }
}
