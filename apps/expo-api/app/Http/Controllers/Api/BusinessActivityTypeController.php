<?php

namespace App\Http\Controllers\Api;

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
     * قائمة أنواع النشاط التجاري (عامة)
     */
    public function index(Request $request): JsonResponse
    {
        $query = BusinessActivityType::active();

        // Search
        if ($search = $this->sanitizeSearch($request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%");
            });
        }

        $this->applySafeOrder($query, $request, ['name', 'name_ar', 'sort_order'], 'sort_order', 'asc');

        $types = $query->paginate($request->input('per_page', 50));

        return ApiResponse::paginated($types);
    }

    /**
     * عرض نوع نشاط تجاري
     */
    public function show(BusinessActivityType $businessActivityType): JsonResponse
    {
        if (!$businessActivityType->is_active) {
            return ApiResponse::notFound('نوع النشاط التجاري غير موجود');
        }

        return ApiResponse::success(new BusinessActivityTypeResource($businessActivityType));
    }
}
