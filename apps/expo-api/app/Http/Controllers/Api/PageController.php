<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    /**
     * قائمة الصفحات النشطة
     * GET /pages
     */
    public function index(Request $request): JsonResponse
    {
        $pages = Cache::remember('pages.active', config('expo-api.cache.ttl.pages', 7200), function () {
            return Page::active()->ordered()->get(['id', 'slug', 'title', 'title_ar', 'type', 'sort_order']);
        });

        return ApiResponse::success($pages, __('messages.page.list_fetched'));
    }

    /**
     * عرض صفحة بالـ slug
     * GET /pages/{slug}
     */
    public function show(string $slug): JsonResponse
    {
        $page = Cache::remember("pages.{$slug}", config('expo-api.cache.ttl.pages', 7200), function () use ($slug) {
            return Page::active()->where('slug', $slug)->first();
        });

        if (! $page) {
            return ApiResponse::error(
                __('messages.page.not_found'),
                ApiErrorCode::PAGE_NOT_FOUND,
                404
            );
        }

        return ApiResponse::success($page, __('messages.page.fetched'));
    }
}
