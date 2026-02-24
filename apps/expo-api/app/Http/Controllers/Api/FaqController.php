<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FaqController extends Controller
{
    /**
     * قائمة الأسئلة الشائعة
     * GET /faqs
     */
    public function index(Request $request): JsonResponse
    {
        $cacheKey = 'faqs.active.' . ($request->category ?? 'all');

        $faqs = Cache::remember($cacheKey, config('expo-api.cache.ttl.faqs', 3600), function () use ($request) {
            return Faq::active()
                ->when($request->category, fn ($q) => $q->ofCategory($request->category))
                ->when($request->search, fn ($q) => $q->search($request->search))
                ->ordered()
                ->get();
        });

        return ApiResponse::success($faqs, __('messages.faq.list_fetched'));
    }

    /**
     * الفئات المتاحة
     * GET /faqs/categories
     */
    public function categories(): JsonResponse
    {
        $categories = Faq::active()->distinct()->pluck('category')->filter()->values();

        return ApiResponse::success($categories, __('messages.faq.categories_fetched'));
    }

    /**
     * عرض سؤال وتسجيل مشاهدة
     * GET /faqs/{faq}
     */
    public function show(string $id): JsonResponse
    {
        $faq = Faq::active()->find($id);

        if (! $faq) {
            return ApiResponse::error(
                __('messages.faq.not_found'),
                ApiErrorCode::FAQ_NOT_FOUND,
                404
            );
        }

        $faq->incrementViews();

        return ApiResponse::success($faq, __('messages.faq.fetched'));
    }

    /**
     * تسجيل مفيد/غير مفيد
     * POST /faqs/{faq}/helpful
     */
    public function helpful(string $id): JsonResponse
    {
        $faq = Faq::active()->find($id);

        if (! $faq) {
            return ApiResponse::error(
                __('messages.faq.not_found'),
                ApiErrorCode::FAQ_NOT_FOUND,
                404
            );
        }

        $faq->markHelpful();

        return ApiResponse::success(['helpful_count' => $faq->helpful_count], __('messages.faq.marked_helpful'));
    }
}
