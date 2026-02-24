<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BannerController extends Controller
{
    /**
     * قائمة البانرات النشطة
     * GET /banners
     */
    public function index(Request $request): JsonResponse
    {
        $position = $request->get('position', 'home');
        $cacheKey = "banners.active.{$position}";

        $banners = Cache::remember($cacheKey, config('expo-api.cache.ttl.banners', 1800), function () use ($position) {
            return Banner::active()
                ->when($position !== 'all', fn ($q) => $q->where('position', $position))
                ->orderBy('sort_order')
                ->get();
        });

        // تسجيل impressions بشكل async (لا يؤثر على الاستجابة)
        $banners->each(fn ($b) => $b->trackImpression());

        return ApiResponse::success($banners, __('messages.banner.list_fetched'));
    }

    /**
     * تسجيل نقرة على بانر
     * POST /banners/{banner}/click
     */
    public function click(string $id): JsonResponse
    {
        $banner = Banner::find($id);

        if (! $banner) {
            return ApiResponse::error(
                __('messages.banner.not_found'),
                ApiErrorCode::BANNER_NOT_FOUND,
                404
            );
        }

        $banner->trackClick();

        return ApiResponse::success(
            ['clicks_count' => $banner->clicks_count],
            __('messages.banner.click_tracked')
        );
    }
}
