<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class CityController extends Controller
{
    public function index(): JsonResponse { return ApiResponse::success(City::all()); }
    public function show(int $id): JsonResponse { return ApiResponse::success(City::findOrFail($id)); }
}
