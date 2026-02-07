<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Models\City;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class CityController extends Controller
{
    /**
     * Get all active cities
     */
    public function index(): JsonResponse
    {
        $cities = City::active()
            ->ordered()
            ->withCount(['events' => function ($query) {
                $query->published();
            }])
            ->get();

        return ApiResponse::success(
            CityResource::collection($cities)
        );
    }

    /**
     * Get single city
     */
    public function show(City $city): JsonResponse
    {
        $city->loadCount(['events' => function ($query) {
            $query->published();
        }]);

        return ApiResponse::success(
            new CityResource($city)
        );
    }
}
