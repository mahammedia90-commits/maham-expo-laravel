<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PageController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $pages = Page::when($request->type, fn ($q) => $q->ofType($request->type))
            ->when($request->is_active !== null, fn ($q) => $q->where('is_active', (bool) $request->is_active))
            ->ordered()
            ->paginate($request->get('per_page', 20));

        return ApiResponse::paginated($pages, __('messages.page.list_fetched'));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'slug'       => 'required|string|unique:pages,slug|max:100',
            'title'      => 'required|string|max:255',
            'title_ar'   => 'required|string|max:255',
            'content'    => 'required|string',
            'content_ar' => 'required|string',
            'type'       => 'required|string',
            'is_active'  => 'boolean',
            'sort_order' => 'integer',
            'meta'       => 'nullable|array',
        ]);

        $page = Page::create($request->only([
            'slug', 'title', 'title_ar', 'content', 'content_ar',
            'type', 'is_active', 'sort_order', 'meta',
        ]));

        Cache::forget('pages.active');

        return ApiResponse::created($page, __('messages.page.created'));
    }

    public function show(string $id): JsonResponse
    {
        $page = Page::find($id);

        if (! $page) {
            return ApiResponse::error(__('messages.page.not_found'), ApiErrorCode::PAGE_NOT_FOUND, 404);
        }

        return ApiResponse::success($page, __('messages.page.fetched'));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $page = Page::find($id);

        if (! $page) {
            return ApiResponse::error(__('messages.page.not_found'), ApiErrorCode::PAGE_NOT_FOUND, 404);
        }

        $request->validate([
            'slug'       => "sometimes|string|unique:pages,slug,{$id}|max:100",
            'title'      => 'sometimes|string|max:255',
            'title_ar'   => 'sometimes|string|max:255',
            'content'    => 'sometimes|string',
            'content_ar' => 'sometimes|string',
            'type'       => 'sometimes|string',
            'is_active'  => 'boolean',
            'sort_order' => 'integer',
            'meta'       => 'nullable|array',
        ]);

        $page->update($request->only([
            'slug', 'title', 'title_ar', 'content', 'content_ar',
            'type', 'is_active', 'sort_order', 'meta',
        ]));

        Cache::forget('pages.active');
        Cache::forget("pages.{$page->slug}");

        return ApiResponse::success($page, __('messages.page.updated'));
    }

    public function destroy(string $id): JsonResponse
    {
        $page = Page::find($id);

        if (! $page) {
            return ApiResponse::error(__('messages.page.not_found'), ApiErrorCode::PAGE_NOT_FOUND, 404);
        }

        Cache::forget('pages.active');
        Cache::forget("pages.{$page->slug}");

        $page->delete();

        return ApiResponse::success(null, __('messages.page.deleted'));
    }
}
