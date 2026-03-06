<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Get all active services (public)
     */
    public function index(Request $request): JsonResponse
    {
        $services = Service::active()
            ->ordered()
            ->paginate($request->input('per_page', 15));

        return ApiResponse::paginated($services);
    }
}
