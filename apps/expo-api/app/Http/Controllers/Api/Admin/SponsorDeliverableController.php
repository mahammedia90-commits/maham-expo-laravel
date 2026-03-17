<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\SponsorDeliverable;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SponsorDeliverableController extends Controller
{
    use SafeOrderBy;

    /**
     * قائمة جميع التسليمات
     */
    public function index(Request $request): JsonResponse
    {
        $query = SponsorDeliverable::with(['contract.sponsor', 'contract.event']);

        if ($contractId = $request->input('contract_id')) {
            $query->forContract($contractId);
        }

        if ($category = $request->input('category')) {
            $query->ofCategory($category);
        }

        if ($status = $request->input('status')) {
            $query->ofStatus($status);
        }

        if ($request->boolean('overdue')) {
            $query->overdue();
        }

        $this->applySafeOrder($query, $request, [
            'title', 'category', 'status', 'deadline', 'sort_order', 'created_at',
        ], 'sort_order', 'asc');

        $perPage = min($request->input('per_page', 15), 50);

        return ApiResponse::paginated($query->paginate($perPage));
    }

    /**
     * إنشاء تسليم
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'sponsor_contract_id' => ['required', 'uuid', 'exists:sponsor_contracts,id'],
            'category' => ['required', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'description_ar' => ['nullable', 'string', 'max:2000'],
            'specs' => ['nullable', 'string', 'max:1000'],
            'status' => ['sometimes', 'in:not_started,pending,in_progress,review,completed,rejected'],
            'deadline' => ['nullable', 'date'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $deliverable = SponsorDeliverable::create($validated);
        $deliverable->load(['contract.sponsor']);

        return ApiResponse::created($deliverable, __('messages.sponsor_deliverable.created'));
    }

    /**
     * تفاصيل تسليم
     */
    public function show(SponsorDeliverable $sponsorDeliverable): JsonResponse
    {
        $sponsorDeliverable->load(['contract.sponsor', 'contract.event']);
        return ApiResponse::success($sponsorDeliverable);
    }

    /**
     * تحديث تسليم
     */
    public function update(Request $request, SponsorDeliverable $sponsorDeliverable): JsonResponse
    {
        $validated = $request->validate([
            'category' => ['sometimes', 'string', 'max:100'],
            'title' => ['sometimes', 'string', 'max:255'],
            'title_ar' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'description_ar' => ['nullable', 'string', 'max:2000'],
            'specs' => ['nullable', 'string', 'max:1000'],
            'status' => ['sometimes', 'in:not_started,pending,in_progress,review,completed,rejected'],
            'deadline' => ['nullable', 'date'],
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        // If marking as completed
        if (isset($validated['status']) && $validated['status'] === 'completed') {
            $validated['completed_at'] = now()->toDateString();
        }

        $sponsorDeliverable->update($validated);
        $sponsorDeliverable->load(['contract.sponsor']);

        return ApiResponse::success($sponsorDeliverable, __('messages.sponsor_deliverable.updated'));
    }

    /**
     * حذف تسليم
     */
    public function destroy(SponsorDeliverable $sponsorDeliverable): JsonResponse
    {
        if ($sponsorDeliverable->file_path) {
            Storage::disk('public')->delete($sponsorDeliverable->file_path);
        }

        $sponsorDeliverable->delete();
        return ApiResponse::success(null, __('messages.sponsor_deliverable.deleted'));
    }

    /**
     * اعتماد تسليم
     */
    public function approve(SponsorDeliverable $sponsorDeliverable): JsonResponse
    {
        $sponsorDeliverable->update([
            'status' => 'completed',
            'completed_at' => now()->toDateString(),
        ]);

        return ApiResponse::success($sponsorDeliverable, __('messages.sponsor_deliverable.approved'));
    }

    /**
     * رفض تسليم
     */
    public function reject(Request $request, SponsorDeliverable $sponsorDeliverable): JsonResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $sponsorDeliverable->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        return ApiResponse::success($sponsorDeliverable, __('messages.sponsor_deliverable.rejected'));
    }
}
