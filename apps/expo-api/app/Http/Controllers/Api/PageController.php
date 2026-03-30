<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class PageController extends Controller
{
    public function index(): JsonResponse
    {
        return ApiResponse::success(Page::published()->get());
    }
    public function show(string $slug): JsonResponse
    {
        return ApiResponse::success(Page::where('slug', $slug)->published()->firstOrFail());
    }
}
