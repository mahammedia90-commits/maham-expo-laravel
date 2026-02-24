<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\SponsorPackage;
use App\Support\ApiErrorCode;
use App\Support\ApiResponse;
use App\Support\SafeOrderBy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SponsorPackageController extends Controller
{
    use SafeOrderBy;

    /**
     * List sponsor packages for an event
     */
    public function index(Request $request, Event $event): JsonResponse
    {
        $query = $event->sponsorPackages()->withCount([
            'contracts as active_contracts_count' => fn($q) => $q->whereIn('status', ['active', 'completed']),
        ]);

        if ($tier = $request->input('tier')) {
            $query->ofTier($tier);
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $packages = $query->ordered()->get();

        return ApiResponse::success($packages);
    }

    /**
     * Create a sponsor package
     */
    public function store(Request $request, Event $event): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'name_ar' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'description_ar' => ['nullable', 'string', 'max:2000'],
            'tier' => ['required', Rule::enum(\App\Enums\SponsorTier::class)],
            'price' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'max_sponsors' => ['nullable', 'integer', 'min:1'],
            'benefits' => ['nullable', 'array'],
            'benefits.*' => ['string', 'max:500'],
            'display_screens_count' => ['sometimes', 'integer', 'min:0'],
            'banners_count' => ['sometimes', 'integer', 'min:0'],
            'vip_invitations_count' => ['sometimes', 'integer', 'min:0'],
            'booth_area_sqm' => ['nullable', 'numeric', 'min:0'],
            'logo_placement' => ['nullable', 'array'],
            'logo_placement.*' => ['string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $validated['event_id'] = $event->id;

        $package = SponsorPackage::create($validated);

        return ApiResponse::created($package, __('messages.sponsor_package.created'));
    }

    /**
     * Show a sponsor package
     */
    public function show(SponsorPackage $sponsorPackage): JsonResponse
    {
        $sponsorPackage->load('event');
        $sponsorPackage->loadCount([
            'contracts as active_contracts_count' => fn($q) => $q->whereIn('status', ['active', 'completed']),
        ]);

        return ApiResponse::success($sponsorPackage);
    }

    /**
     * Update a sponsor package
     */
    public function update(Request $request, SponsorPackage $sponsorPackage): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'name_ar' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'description_ar' => ['nullable', 'string', 'max:2000'],
            'tier' => ['sometimes', Rule::enum(\App\Enums\SponsorTier::class)],
            'price' => ['sometimes', 'numeric', 'min:0', 'max:9999999999'],
            'max_sponsors' => ['nullable', 'integer', 'min:1'],
            'benefits' => ['nullable', 'array'],
            'benefits.*' => ['string', 'max:500'],
            'display_screens_count' => ['sometimes', 'integer', 'min:0'],
            'banners_count' => ['sometimes', 'integer', 'min:0'],
            'vip_invitations_count' => ['sometimes', 'integer', 'min:0'],
            'booth_area_sqm' => ['nullable', 'numeric', 'min:0'],
            'logo_placement' => ['nullable', 'array'],
            'logo_placement.*' => ['string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ]);

        $sponsorPackage->update($validated);

        return ApiResponse::success($sponsorPackage, __('messages.sponsor_package.updated'));
    }

    /**
     * Delete a sponsor package
     */
    public function destroy(SponsorPackage $sponsorPackage): JsonResponse
    {
        $hasActiveContracts = $sponsorPackage->contracts()
            ->whereIn('status', ['pending', 'active'])
            ->exists();

        if ($hasActiveContracts) {
            return ApiResponse::error(
                __('messages.sponsor_package.has_active_contracts'),
                ApiErrorCode::VALIDATION_FAILED,
                422
            );
        }

        $sponsorPackage->delete();

        return ApiResponse::success(null, __('messages.sponsor_package.deleted'));
    }
}
