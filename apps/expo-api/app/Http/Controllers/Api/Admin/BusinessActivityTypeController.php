<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessActivityTypeResource;
use App\Models\BusinessActivityType;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessActivityTypeController extends Controller
{
    use SafeOrderBy;

    /**
     * قائمة أنواع النشاط التجاري
     */
    public function index(Request $request): JsonResponse
    {
        $query = BusinessActivityType::query();

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

        $types = $query->paginate($request->input('per_page', 15));

        return ApiResponse::paginated($types);
    }

    /**
     * إنشاء نوع نشاط تجاري جديد
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'name_ar' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'description_ar' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:100',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer|min:0',
        ]);

        $type = BusinessActivityType::create($validated);

        return ApiResponse::created(
            new BusinessActivityTypeResource($type),
            'تم إنشاء نوع النشاط التجاري بنجاح'
        );
    }

    /**
     * عرض نوع نشاط تجاري
     */
    public function show(BusinessActivityType $businessActivityType): JsonResponse
    {
        return ApiResponse::success(new BusinessActivityTypeResource($businessActivityType));
    }

    /**
     * تحديث نوع نشاط تجاري
     */
    public function update(Request $request, BusinessActivityType $businessActivityType): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'name_ar' => 'sometimes|string|max:100',
            'description' => 'nullable|string|max:500',
            'description_ar' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:100',
            'is_active' => 'sometimes|boolean',
            'sort_order' => 'sometimes|integer|min:0',
        ]);

        $businessActivityType->update($validated);

        return ApiResponse::success(
            new BusinessActivityTypeResource($businessActivityType),
            'تم تحديث نوع النشاط التجاري بنجاح'
        );
    }

    /**
     * حذف نوع نشاط تجاري
     */
    public function destroy(BusinessActivityType $businessActivityType): JsonResponse
    {
        $businessActivityType->delete();

        return ApiResponse::success(null, 'تم حذف نوع النشاط التجاري بنجاح');
    }
}
