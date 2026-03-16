<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MemberTypeResource;
use App\Models\MemberType;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberTypeController extends Controller
{
    use SafeOrderBy;

    /**
     * قائمة أنواع أعضاء الفريق
     */
    public function index(Request $request): JsonResponse
    {
        $query = MemberType::query();

        // Filter by scope
        if ($request->filled('scope')) {
            $query->forScope($request->input('scope'));
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search
        if ($search = $this->sanitizeSearch($request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%");
            });
        }

        // Order
        $this->applySafeOrder($query, $request, ['name', 'name_ar', 'sort_order', 'created_at'], 'sort_order', 'asc');

        $memberTypes = $query->withCount('teamMembers')->paginate($request->input('per_page', 15));

        return ApiResponse::paginated($memberTypes);
    }

    /**
     * إنشاء نوع عضو جديد
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'name_ar' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'description_ar' => 'nullable|string|max:500',
            'scope' => 'required|in:merchant,investor,both',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer|min:0',
        ]);

        $memberType = MemberType::create($validated);

        return ApiResponse::created(
            new MemberTypeResource($memberType),
            __('messages.member_type.created', [], 'ar')
        );
    }

    /**
     * عرض نوع عضو
     */
    public function show(MemberType $memberType): JsonResponse
    {
        $memberType->loadCount('teamMembers');

        return ApiResponse::success(new MemberTypeResource($memberType));
    }

    /**
     * تحديث نوع عضو
     */
    public function update(Request $request, MemberType $memberType): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'name_ar' => 'sometimes|string|max:100',
            'description' => 'nullable|string|max:500',
            'description_ar' => 'nullable|string|max:500',
            'scope' => 'sometimes|in:merchant,investor,both',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer|min:0',
        ]);

        $memberType->update($validated);

        return ApiResponse::success(
            new MemberTypeResource($memberType),
            __('messages.member_type.updated', [], 'ar')
        );
    }

    /**
     * حذف نوع عضو
     */
    public function destroy(MemberType $memberType): JsonResponse
    {
        if ($memberType->teamMembers()->exists()) {
            return ApiResponse::error(
                'لا يمكن حذف نوع العضو لأنه مرتبط بأعضاء فريق',
                ApiErrorCode::RESOURCE_DELETION_FAILED,
                422
            );
        }

        $memberType->delete();

        return ApiResponse::success(null, 'تم حذف نوع العضو بنجاح');
    }
}
