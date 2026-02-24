<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * قائمة جميع التقييمات مع فلترة
     */
    public function index(Request $request): JsonResponse
    {
        $query = Rating::with(['user:id,name', 'rateable'])
            ->when($request->rateable_type, fn ($q) => $q->where('rateable_type', $request->rateable_type))
            ->when($request->rateable_id, fn ($q) => $q->where('rateable_id', $request->rateable_id))
            ->when($request->is_approved !== null, fn ($q) => $q->where('is_approved', (bool) $request->is_approved))
            ->when($request->min_rating, fn ($q) => $q->where('overall_rating', '>=', $request->min_rating))
            ->when($request->search, fn ($q) => $q->where(function ($sub) use ($request) {
                $sub->where('comment', 'like', "%{$request->search}%")
                    ->orWhere('comment_ar', 'like', "%{$request->search}%");
            }))
            ->orderByDesc('created_at');

        $ratings = $query->paginate($request->get('per_page', 20));

        return ApiResponse::paginated($ratings, __('messages.rating.list_fetched'));
    }

    /**
     * عرض تفاصيل تقييم
     */
    public function show(string $id): JsonResponse
    {
        $rating = Rating::with(['user:id,name,email', 'rateable'])->find($id);

        if (! $rating) {
            return ApiResponse::error(
                __('messages.rating.not_found'),
                ApiErrorCode::RATING_NOT_FOUND,
                404
            );
        }

        return ApiResponse::success($rating, __('messages.rating.fetched'));
    }

    /**
     * اعتماد تقييم
     */
    public function approve(Request $request, string $id): JsonResponse
    {
        $rating = Rating::find($id);

        if (! $rating) {
            return ApiResponse::error(
                __('messages.rating.not_found'),
                ApiErrorCode::RATING_NOT_FOUND,
                404
            );
        }

        $rating->update(['is_approved' => true]);

        return ApiResponse::success($rating, __('messages.rating.approved'));
    }

    /**
     * رفض تقييم (حذفه)
     */
    public function reject(Request $request, string $id): JsonResponse
    {
        $rating = Rating::find($id);

        if (! $rating) {
            return ApiResponse::error(
                __('messages.rating.not_found'),
                ApiErrorCode::RATING_NOT_FOUND,
                404
            );
        }

        $rating->delete();

        return ApiResponse::success(null, __('messages.rating.rejected'));
    }

    /**
     * حذف تقييم نهائياً
     */
    public function destroy(string $id): JsonResponse
    {
        $rating = Rating::find($id);

        if (! $rating) {
            return ApiResponse::error(
                __('messages.rating.not_found'),
                ApiErrorCode::RATING_NOT_FOUND,
                404
            );
        }

        $rating->forceDelete();

        return ApiResponse::success(null, __('messages.rating.deleted'));
    }
}
