<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rating;
use App\Models\Space;
use App\Models\Event;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * عرض ملخص تقييمات كيان (مساحة أو فعالية)
     * GET /ratings/summary?rateable_type=space&rateable_id=xxx
     */
    public function summary(Request $request): JsonResponse
    {
        $request->validate([
            'rateable_type' => 'required|in:space,event',
            'rateable_id'   => 'required|uuid',
        ]);

        $model = $request->rateable_type === 'space'
            ? Space::findOrFail($request->rateable_id)
            : Event::findOrFail($request->rateable_id);

        $summary = Rating::getAverageForRateable($model);

        return ApiResponse::success($summary, __('messages.rating.summary_fetched'));
    }

    /**
     * عرض تقييمات كيان معين (مع pagination)
     * GET /ratings?rateable_type=space&rateable_id=xxx
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'rateable_type' => 'required|in:space,event',
            'rateable_id'   => 'required|uuid',
        ]);

        $ratings = Rating::with('user:id,name')
            ->where('rateable_type', $request->rateable_type === 'space' ? Space::class : Event::class)
            ->where('rateable_id', $request->rateable_id)
            ->approved()
            ->orderByDesc('created_at')
            ->paginate($request->get('per_page', 15));

        return ApiResponse::paginated($ratings, __('messages.rating.list_fetched'));
    }

    /**
     * إضافة تقييم جديد
     * POST /ratings
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'rateable_type'        => 'required|in:space,event',
            'rateable_id'          => 'required|uuid',
            'overall_rating'       => 'required|integer|min:1|max:5',
            'cleanliness_rating'   => 'nullable|integer|min:1|max:5',
            'location_rating'      => 'nullable|integer|min:1|max:5',
            'facilities_rating'    => 'nullable|integer|min:1|max:5',
            'value_rating'         => 'nullable|integer|min:1|max:5',
            'communication_rating' => 'nullable|integer|min:1|max:5',
            'comment'              => 'nullable|string|max:1000',
            'comment_ar'           => 'nullable|string|max:1000',
            'rental_request_id'    => 'nullable|uuid|exists:rental_requests,id',
        ]);

        $userId = $request->input('auth_user_id');
        $modelClass = $request->rateable_type === 'space' ? Space::class : Event::class;

        // التحقق من عدم وجود تقييم سابق
        $existing = Rating::where('user_id', $userId)
            ->where('rateable_type', $modelClass)
            ->where('rateable_id', $request->rateable_id)
            ->first();

        if ($existing) {
            return ApiResponse::error(
                __('messages.rating.already_exists'),
                ApiErrorCode::RATING_ALREADY_EXISTS
            );
        }

        $rating = Rating::create([
            'user_id'              => $userId,
            'rateable_type'        => $modelClass,
            'rateable_id'          => $request->rateable_id,
            'type'                 => $request->rateable_type,
            'overall_rating'       => $request->overall_rating,
            'cleanliness_rating'   => $request->cleanliness_rating,
            'location_rating'      => $request->location_rating,
            'facilities_rating'    => $request->facilities_rating,
            'value_rating'         => $request->value_rating,
            'communication_rating' => $request->communication_rating,
            'comment'              => $request->comment,
            'comment_ar'           => $request->comment_ar,
            'rental_request_id'    => $request->rental_request_id,
            'is_approved'          => (bool) config('expo-api.rating.auto_approve', false),
        ]);

        return ApiResponse::created($rating, __('messages.rating.created'));
    }

    /**
     * تعديل تقييم موجود (المالك فقط)
     * PUT /ratings/{rating}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $rating = Rating::where('id', $id)
            ->where('user_id', $request->input('auth_user_id'))
            ->first();

        if (! $rating) {
            return ApiResponse::error(
                __('messages.rating.not_found'),
                ApiErrorCode::RATING_NOT_FOUND,
                404
            );
        }

        $request->validate([
            'overall_rating'       => 'sometimes|integer|min:1|max:5',
            'cleanliness_rating'   => 'nullable|integer|min:1|max:5',
            'location_rating'      => 'nullable|integer|min:1|max:5',
            'facilities_rating'    => 'nullable|integer|min:1|max:5',
            'value_rating'         => 'nullable|integer|min:1|max:5',
            'communication_rating' => 'nullable|integer|min:1|max:5',
            'comment'              => 'nullable|string|max:1000',
            'comment_ar'           => 'nullable|string|max:1000',
        ]);

        $rating->update($request->only([
            'overall_rating', 'cleanliness_rating', 'location_rating',
            'facilities_rating', 'value_rating', 'communication_rating',
            'comment', 'comment_ar',
        ]));

        // إعادة التقييم لانتظار الموافقة بعد التعديل
        $rating->update(['is_approved' => (bool) config('expo-api.rating.auto_approve', false)]);

        return ApiResponse::success($rating, __('messages.rating.updated'));
    }

    /**
     * حذف تقييم (المالك فقط)
     * DELETE /ratings/{rating}
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $rating = Rating::where('id', $id)
            ->where('user_id', $request->input('auth_user_id'))
            ->first();

        if (! $rating) {
            return ApiResponse::error(
                __('messages.rating.not_found'),
                ApiErrorCode::RATING_NOT_FOUND,
                404
            );
        }

        $rating->delete();

        return ApiResponse::success(null, __('messages.rating.deleted'));
    }
}
