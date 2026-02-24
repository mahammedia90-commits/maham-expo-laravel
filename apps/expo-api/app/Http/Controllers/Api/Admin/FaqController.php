<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FaqController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $faqs = Faq::when($request->category, fn ($q) => $q->ofCategory($request->category))
            ->when($request->is_active !== null, fn ($q) => $q->where('is_active', (bool) $request->is_active))
            ->when($request->search, fn ($q) => $q->search($request->search))
            ->ordered()
            ->paginate($request->get('per_page', 20));

        return ApiResponse::paginated($faqs, __('messages.faq.list_fetched'));
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'question'    => 'required|string|max:500',
            'question_ar' => 'required|string|max:500',
            'answer'      => 'required|string',
            'answer_ar'   => 'required|string',
            'category'    => 'nullable|string|max:100',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer',
        ]);

        $faq = Faq::create($request->only([
            'question', 'question_ar', 'answer', 'answer_ar',
            'category', 'is_active', 'sort_order',
        ]));

        Cache::forget('faqs.active.all');
        if ($request->category) {
            Cache::forget("faqs.active.{$request->category}");
        }

        return ApiResponse::created($faq, __('messages.faq.created'));
    }

    public function show(string $id): JsonResponse
    {
        $faq = Faq::find($id);

        if (! $faq) {
            return ApiResponse::error(__('messages.faq.not_found'), ApiErrorCode::FAQ_NOT_FOUND, 404);
        }

        return ApiResponse::success($faq, __('messages.faq.fetched'));
    }

    public function update(Request $request, string $id): JsonResponse
    {
        $faq = Faq::find($id);

        if (! $faq) {
            return ApiResponse::error(__('messages.faq.not_found'), ApiErrorCode::FAQ_NOT_FOUND, 404);
        }

        $request->validate([
            'question'    => 'sometimes|string|max:500',
            'question_ar' => 'sometimes|string|max:500',
            'answer'      => 'sometimes|string',
            'answer_ar'   => 'sometimes|string',
            'category'    => 'nullable|string|max:100',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer',
        ]);

        $faq->update($request->only([
            'question', 'question_ar', 'answer', 'answer_ar',
            'category', 'is_active', 'sort_order',
        ]));

        Cache::forget('faqs.active.all');

        return ApiResponse::success($faq, __('messages.faq.updated'));
    }

    public function destroy(string $id): JsonResponse
    {
        $faq = Faq::find($id);

        if (! $faq) {
            return ApiResponse::error(__('messages.faq.not_found'), ApiErrorCode::FAQ_NOT_FOUND, 404);
        }

        $faq->delete();
        Cache::forget('faqs.active.all');

        return ApiResponse::success(null, __('messages.faq.deleted'));
    }
}
