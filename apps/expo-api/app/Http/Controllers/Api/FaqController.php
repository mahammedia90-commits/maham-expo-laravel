<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FaqController extends Controller {
    public function index(Request $request): JsonResponse {
        $q = Faq::active();
        if ($cat = $request->input('category')) $q->where('category', $cat);
        return ApiResponse::success($q->orderBy('sort_order')->get());
    }
    public function show(int $id): JsonResponse { return ApiResponse::success(Faq::findOrFail($id)); }
    public function categories(): JsonResponse { return ApiResponse::success(Faq::distinct()->pluck('category')); }
    public function helpful(int $id): JsonResponse { return ApiResponse::success(['message' => 'شكراً']); }
}
