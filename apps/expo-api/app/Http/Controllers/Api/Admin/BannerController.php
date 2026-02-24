<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class BannerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $banners = Banner::when($request->position, fn ($q) => $q->where('position', $request->position))
            ->when($request->is_active !== null, fn ($q) => $q->where('is_active', (bool) $request->is_active))
            ->orderBy('sort_order')
            ->paginate($request->get('per_page', 20));

        return ApiResponse::paginated($banners, __('messages.banner.list_fetched'));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'title_ar'    => 'required|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'image'       => 'required|string',
            'image_ar'    => 'nullable|string',
            'link_url'    => 'nullable|url|max:500',
            'position'    => 'required|string|max:100',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after:start_date',
        ]);

        $banner = Banner::create($request->only([
            'title', 'title_ar', 'description', 'description_ar',
            'image', 'image_ar', 'link_url', 'position',
            'is_active', 'sort_order', 'start_date', 'end_date',
        ]));

        Cache::forget("banners.active.{$request->position}");

        return ApiResponse::created($banner, __('messages.banner.created'));
    }

    public function show(string $id): JsonResponse
    {
        $banner = Banner::find($id);

        if (! $banner) {
            return ApiResponse::error(__('messages.banner.not_found'), ApiErrorCode::BANNER_NOT_FOUND, 404);
        }

        return ApiResponse::success($banner, __('messages.banner.fetched'));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $banner = Banner::find($id);

        if (! $banner) {
            return ApiResponse::error(__('messages.banner.not_found'), ApiErrorCode::BANNER_NOT_FOUND, 404);
        }

        $request->validate([
            'title'       => 'sometimes|string|max:255',
            'title_ar'    => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'image'       => 'sometimes|string',
            'image_ar'    => 'nullable|string',
            'link_url'    => 'nullable|url|max:500',
            'position'    => 'sometimes|string|max:100',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date',
        ]);

        $banner->update($request->only([
            'title', 'title_ar', 'description', 'description_ar',
            'image', 'image_ar', 'link_url', 'position',
            'is_active', 'sort_order', 'start_date', 'end_date',
        ]));

        Cache::forget("banners.active.{$banner->position}");

        return ApiResponse::success($banner, __('messages.banner.updated'));
    }

    public function destroy(string $id): JsonResponse
    {
        $banner = Banner::find($id);

        if (! $banner) {
            return ApiResponse::error(__('messages.banner.not_found'), ApiErrorCode::BANNER_NOT_FOUND, 404);
        }

        Cache::forget("banners.active.{$banner->position}");
        $banner->delete();

        return ApiResponse::success(null, __('messages.banner.deleted'));
    }
}
