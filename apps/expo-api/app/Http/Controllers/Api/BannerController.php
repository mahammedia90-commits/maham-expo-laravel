<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
class BannerController extends Controller {
    public function index(): JsonResponse { return ApiResponse::success(Banner::active()->orderBy('sortOrder')->get()); }
    public function click(int $id): JsonResponse { Banner::where('id',$id)->increment('clickCount'); return ApiResponse::success(['ok'=>true]); }
}
