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
        $page = Page::where('slug', $slug)->published()->firstOrFail();
        return ApiResponse::success($page);
    }
}
