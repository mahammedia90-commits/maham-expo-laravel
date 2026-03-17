<?php

namespace App\Http\Controllers\Api\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use App\Models\SponsorContract;
use App\Models\SponsorDeliverable;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DeliverableController extends Controller
{
    use SafeOrderBy;

    /**
     * تسليماتي
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');
        $sponsorIds = Sponsor::forUser($userId)->pluck('id');
        $contractIds = SponsorContract::whereIn('sponsor_id', $sponsorIds)->pluck('id');

        $query = SponsorDeliverable::whereIn('sponsor_contract_id', $contractIds)
            ->with(['contract.event']);

        if ($contractId = $request->input('contract_id')) {
            $query->forContract($contractId);
        }

        if ($category = $request->input('category')) {
            $query->ofCategory($category);
        }

        if ($status = $request->input('status')) {
            $query->ofStatus($status);
        }

        $query->ordered();

        $perPage = min($request->input('per_page', 15), 50);

        return ApiResponse::paginated($query->paginate($perPage));
    }

    /**
     * تفاصيل تسليم
     */
    public function show(Request $request, SponsorDeliverable $sponsorDeliverable): JsonResponse
    {
        $userId = $request->input('auth_user_id');
        $sponsorIds = Sponsor::forUser($userId)->pluck('id');
        $contractIds = SponsorContract::whereIn('sponsor_id', $sponsorIds)->pluck('id');

        if (!$contractIds->contains($sponsorDeliverable->sponsor_contract_id)) {
            return ApiResponse::error(__('messages.unauthorized'), 'unauthorized', 403);
        }

        $sponsorDeliverable->load(['contract.sponsor', 'contract.event']);

        return ApiResponse::success($sponsorDeliverable);
    }

    /**
     * رفع ملف تسليم
     */
    public function upload(Request $request, SponsorDeliverable $sponsorDeliverable): JsonResponse
    {
        $userId = $request->input('auth_user_id');
        $sponsorIds = Sponsor::forUser($userId)->pluck('id');
        $contractIds = SponsorContract::whereIn('sponsor_id', $sponsorIds)->pluck('id');

        if (!$contractIds->contains($sponsorDeliverable->sponsor_contract_id)) {
            return ApiResponse::error(__('messages.unauthorized'), 'unauthorized', 403);
        }

        $request->validate([
            'file' => ['required', 'file', 'max:102400'], // 100MB max
        ]);

        // Delete old file if exists
        if ($sponsorDeliverable->file_path) {
            Storage::disk('public')->delete($sponsorDeliverable->file_path);
        }

        $file = $request->file('file');
        $path = $file->store('uploads/sponsor-deliverables', 'public');

        $sponsorDeliverable->update([
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'status' => 'review',
        ]);

        $sponsorDeliverable->load(['contract']);

        return ApiResponse::success($sponsorDeliverable, __('messages.sponsor_deliverable.uploaded'));
    }
}
