<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get all active categories
     */
    public function index(Request $request): JsonResponse
    {
        $categories = Category::active()
            ->ordered()
            ->withCount(['events' => function ($query) {
                $query->published();
            }])
            ->paginate($request->input('per_page', 15));

        return ApiResponse::paginated($categories);
    }

    /**
     * Get single category
     */
    public function show(Category $category): JsonResponse
    {
        $category->loadCount(['events' => function ($query) {
            $query->published();
        }]);

        return ApiResponse::success(
            new CategoryResource($category)
        );
    }
}
