<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SpaceResource;
use App\Models\Space;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class SpaceController extends Controller
{
    /**
     * Get single space with full details
     */
    public function show(Space $space): JsonResponse
    {
        $space->load(['event.category', 'event.city', 'section', 'services']);

        return ApiResponse::success(
            new SpaceResource($space)
        );
    }
}
