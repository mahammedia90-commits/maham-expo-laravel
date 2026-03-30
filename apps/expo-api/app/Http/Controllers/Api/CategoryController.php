<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller {
    public function index(): JsonResponse { return ApiResponse::success(Category::all()); }
    public function show(int $id): JsonResponse { return ApiResponse::success(Category::findOrFail($id)); }
}
