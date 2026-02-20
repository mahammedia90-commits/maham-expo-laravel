<?php

namespace App\Http\Controllers\Api\Merchant;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Merchant Service Controller
 *
 * Merchants can browse available services (read-only).
 * They cannot create, update, or delete services.
 */
class ServiceController extends Controller
{
    use SafeOrderBy;

    /**
     * List all active services
     */
    public function index(Request $request): JsonResponse
    {
        $query = Service::where('is_active', true);

        // Search
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
     * Show a single service
     */
    public function show(Service $service): JsonResponse
    {
        if (!$service->is_active) {
            return ApiResponse::notFound(__('messages.service.not_found'));
        }

        return ApiResponse::success(
            new ServiceResource($service)
        );
    }
}
