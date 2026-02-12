<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ServiceController extends Controller
{
    /**
     * Get all active services (public)
     */
    public function index(): JsonResponse
    {
        $services = Service::active()
            ->ordered()
            ->get();

        return ApiResponse::success(
            ServiceResource::collection($services)
        );
    }
}
