<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class BannerController extends Controller
{
    public function index(): JsonResponse
    {
        return ApiResponse::success(Banner::active()->orderBy('sort_order')->get());
    }
    public function click(string $id): JsonResponse
    {
        Banner::where('id', $id)->increment('clicks_count');
        return ApiResponse::success(['message' => 'ok']);
    }
}
