<?php

namespace App\Http\Resources;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class SpaceResource extends JsonResource
{
    protected function toStorageUrl(?string $path): ?string
    {
        if (!$path) return null;
        if (str_starts_with($path, 'http')) return $path;
        return Storage::disk('public')->url($path);
    }

    protected function toStorageUrls(?array $paths): array
    {
        return array_map(fn($p) => $this->toStorageUrl($p), $paths ?? []);
    }

    public function toArray(Request $request): array
    {
        $userId = $request->input('auth_user_id');

        return [
            'id' => $this->id,
            'event' => new EventListResource($this->whenLoaded('event')),
            'section' => new SectionResource($this->whenLoaded('section')),
            'services' => ServiceResource::collection($this->whenLoaded('services')),
            'name' => $this->localized_name,
            'name_en' => $this->name,
            'name_ar' => $this->name_ar,
            'description' => $this->localized_description,
            'location_code' => $this->location_code,
            'area_sqm' => (float) $this->area_sqm,
            'price_per_day' => $this->price_per_day ? (float) $this->price_per_day : null,
            'price_total' => (float) $this->price_total,
            'images' => $this->toStorageUrls($this->images),
            'images_360' => $this->toStorageUrls($this->images_360),
            'main_image' => $this->toStorageUrl($this->main_image),
            'amenities' => $this->localized_amenities ?? [],
            'status' => $this->status->value,
            'status_label' => app()->getLocale() === 'ar' ? $this->status->label() : $this->status->labelEn(),
            'is_available' => $this->is_available,
            'floor_number' => $this->floor_number,
            'space_type' => $this->space_type?->value,
            'space_type_label' => $this->space_type ? (app()->getLocale() === 'ar' ? $this->space_type->label() : $this->space_type->labelEn()) : null,
            'payment_system' => $this->payment_system?->value,
            'payment_system_label' => $this->payment_system ? (app()->getLocale() === 'ar' ? $this->payment_system->label() : $this->payment_system->labelEn()) : null,
            'rental_duration' => $this->rental_duration?->value,
            'rental_duration_label' => $this->rental_duration ? (app()->getLocale() === 'ar' ? $this->rental_duration->label() : $this->rental_duration->labelEn()) : null,
            'location' => [
                'latitude' => $this->latitude ? (float) $this->latitude : null,
                'longitude' => $this->longitude ? (float) $this->longitude : null,
                'address' => $this->localized_address,
            ],
            'is_featured' => (bool) $this->is_featured,
            'is_favorited' => $userId ? Favorite::isFavorited($userId, 'space', $this->id) : false,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
