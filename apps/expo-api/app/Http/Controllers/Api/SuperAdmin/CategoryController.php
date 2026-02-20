<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use SafeOrderBy;

    /**
     * List all categories
     */
    public function index(Request $request): JsonResponse
    {
        $query = Category::query();

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

        $this->applySafeOrder($query, $request, [
            'name', 'name_ar', 'sort_order', 'created_at', 'is_active',
        ], 'sort_order', 'asc');

        $categories = $query->paginate($request->input('per_page', 15));

        return ApiResponse::success($categories);
    }

    /**
     * Create a new category
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'name_ar' => 'required|string|max:255|unique:categories,name_ar',
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'description_ar' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $category = Category::create($validated);

        return ApiResponse::created(
            new CategoryResource($category),
            __('messages.category.created')
        );
    }

    /**
     * Show a single category
     */
    public function show(Category $category): JsonResponse
    {
        $category->loadCount('events');

        return ApiResponse::success(
            new CategoryResource($category)
        );
    }

    /**
     * Update a category
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255|unique:categories,name,' . $category->id,
            'name_ar' => 'sometimes|string|max:255|unique:categories,name_ar,' . $category->id,
            'icon' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'description_ar' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ]);

        $category->update($validated);

        return ApiResponse::success(
            new CategoryResource($category),
            __('messages.category.updated')
        );
    }

    /**
     * Delete a category
     */
    public function destroy(Category $category): JsonResponse
    {
        // Prevent deletion if category has events
        if ($category->events()->exists()) {
            return ApiResponse::error(
                message: __('messages.category.has_events'),
                errorCode: ApiErrorCode::RESOURCE_DELETION_FAILED,
                httpCode: 422
            );
        }

        $category->delete();

        return ApiResponse::success(
            null,
            __('messages.category.deleted')
        );
    }
}
