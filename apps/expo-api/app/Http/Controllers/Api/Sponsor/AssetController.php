<?php

namespace App\Http\Controllers\Api\Sponsor;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use App\Models\SponsorAsset;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AssetController extends Controller
{
    /**
     * List sponsor's own assets
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');
        $sponsorIds = Sponsor::forUser($userId)->pluck('id');

        $query = SponsorAsset::whereIn('sponsor_id', $sponsorIds)
            ->with(['sponsor', 'event']);

        if ($type = $request->input('type')) {
            $query->ofType($type);
        }

        if ($request->has('is_approved')) {
            if ($request->boolean('is_approved')) {
                $query->approved();
            } else {
                $query->pendingApproval();
            }
        }

        $assets = $query->ordered()->paginate($request->input('per_page', 15));

        return ApiResponse::paginated($assets);
    }

    /**
     * Upload a new asset
     */
    public function store(Request $request): JsonResponse
    {
        $userId = $request->input('auth_user_id');
        $sponsorIds = Sponsor::forUser($userId)->pluck('id');

        $validated = $request->validate([
            'sponsor_id' => ['required', 'uuid', Rule::in($sponsorIds)],
            'event_id' => ['nullable', 'uuid', 'exists:events,id'],
            'type' => ['required', Rule::enum(\App\Enums\SponsorAssetType::class)],
            'file' => ['required', 'file', 'max:' . config('expo-api.sponsor.max_asset_size', 10240)],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        // Check asset limit
        $currentCount = SponsorAsset::forSponsor($validated['sponsor_id'])->count();
        $maxAssets = config('expo-api.sponsor.max_assets_per_sponsor', 20);

        if ($currentCount >= $maxAssets) {
            return ApiResponse::error(
                __('messages.sponsor_asset.limit_reached'),
                ApiErrorCode::SPONSOR_ASSET_LIMIT_REACHED,
                422
            );
        }

        $file = $request->file('file');
        $filePath = $file->store(
            config('expo-api.uploads.paths.sponsor_assets', 'uploads/sponsor-assets'),
            'public'
        );

        $asset = SponsorAsset::create([
            'sponsor_id' => $validated['sponsor_id'],
            'event_id' => $validated['event_id'] ?? null,
            'type' => $validated['type'],
            'file_path' => $filePath,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        $asset->load('sponsor');

        return ApiResponse::created($asset, __('messages.sponsor_asset.uploaded'));
    }

    /**
     * Show a specific asset (ownership verified)
     */
    public function show(Request $request, SponsorAsset $sponsorAsset): JsonResponse
    {
        $userId = $request->input('auth_user_id');
        $sponsorIds = Sponsor::forUser($userId)->pluck('id');

        if (!$sponsorIds->contains($sponsorAsset->sponsor_id)) {
            return ApiResponse::error(
                __('messages.auth.permission_denied'),
                ApiErrorCode::PERMISSION_DENIED,
                403
            );
        }

        $sponsorAsset->load(['sponsor', 'event']);

        return ApiResponse::success($sponsorAsset);
    }

    /**
     * Update asset metadata
     */
    public function update(Request $request, SponsorAsset $sponsorAsset): JsonResponse
    {
        $userId = $request->input('auth_user_id');
        $sponsorIds = Sponsor::forUser($userId)->pluck('id');

        if (!$sponsorIds->contains($sponsorAsset->sponsor_id)) {
            return ApiResponse::error(
                __('messages.auth.permission_denied'),
                ApiErrorCode::PERMISSION_DENIED,
                403
            );
        }

        $validated = $request->validate([
            'type' => ['sometimes', Rule::enum(\App\Enums\SponsorAssetType::class)],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $sponsorAsset->update($validated);

        return ApiResponse::success($sponsorAsset, __('messages.sponsor_asset.updated'));
    }

    /**
     * Delete an asset
     */
    public function destroy(Request $request, SponsorAsset $sponsorAsset): JsonResponse
    {
        $userId = $request->input('auth_user_id');
        $sponsorIds = Sponsor::forUser($userId)->pluck('id');

        if (!$sponsorIds->contains($sponsorAsset->sponsor_id)) {
            return ApiResponse::error(
                __('messages.auth.permission_denied'),
                ApiErrorCode::PERMISSION_DENIED,
                403
            );
        }

        // Cleanup file
        if ($sponsorAsset->file_path) {
            Storage::disk('public')->delete($sponsorAsset->file_path);
        }

        $sponsorAsset->delete();

        return ApiResponse::success(null, __('messages.sponsor_asset.deleted'));
    }
}
