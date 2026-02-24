<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Sponsor;
use App\Models\SponsorPackage;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    /**
     * List active sponsors for an event (public)
     */
    public function index(Request $request, Event $event): JsonResponse
    {
        $sponsors = $event->sponsors()
            ->active()
            ->with(['contracts' => function ($q) {
                $q->active()->with('sponsorPackage:id,name,name_ar,tier');
            }])
            ->get()
            ->map(function ($sponsor) {
                $activeTier = $sponsor->contracts->first()?->sponsorPackage;

                return [
                    'id' => $sponsor->id,
                    'name' => $sponsor->localized_name,
                    'name_en' => $sponsor->name,
                    'name_ar' => $sponsor->name_ar,
                    'company_name' => $sponsor->localized_company_name,
                    'description' => $sponsor->localized_description,
                    'logo' => $sponsor->logo,
                    'website' => $sponsor->website,
                    'tier' => $activeTier?->tier?->value,
                    'tier_label' => $activeTier ? (app()->getLocale() === 'ar' ? $activeTier->tier->label() : $activeTier->tier->labelEn()) : null,
                    'tier_color' => $activeTier?->tier?->color(),
                    'tier_sort' => $activeTier?->tier?->sortOrder() ?? 99,
                ];
            })
            ->sortBy('tier_sort')
            ->values();

        return ApiResponse::success($sponsors);
    }

    /**
     * List available sponsor packages for an event (public)
     */
    public function packages(Request $request, Event $event): JsonResponse
    {
        $packages = $event->sponsorPackages()
            ->active()
            ->ordered()
            ->withCount([
                'contracts as active_contracts_count' => fn($q) => $q->whereIn('status', ['active', 'completed']),
            ])
            ->get()
            ->map(function ($package) {
                return [
                    'id' => $package->id,
                    'name' => $package->localized_name,
                    'name_en' => $package->name,
                    'name_ar' => $package->name_ar,
                    'description' => $package->localized_description,
                    'tier' => $package->tier->value,
                    'tier_label' => $package->tier_label,
                    'tier_color' => $package->tier->color(),
                    'price' => (float) $package->price,
                    'benefits' => $package->benefits,
                    'display_screens_count' => $package->display_screens_count,
                    'banners_count' => $package->banners_count,
                    'vip_invitations_count' => $package->vip_invitations_count,
                    'booth_area_sqm' => $package->booth_area_sqm ? (float) $package->booth_area_sqm : null,
                    'logo_placement' => $package->logo_placement,
                    'is_available' => $package->is_available,
                    'remaining_slots' => $package->getRemainingSlots(),
                ];
            });

        return ApiResponse::success($packages);
    }
}
