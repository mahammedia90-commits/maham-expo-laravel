<?php

namespace App\Http\Resources;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class EventListResource extends JsonResource
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
            'name' => $this->localized_name,
            'name_en' => $this->name,
            'name_ar' => $this->name_ar,
            'description' => $this->localized_description,
            'description_en' => $this->description,
            'description_ar' => $this->description_ar,
            'category' => $this->whenLoaded('category', fn() => [
                'id' => $this->category->id,
                'name' => $this->category->localized_name,
                'name_ar' => $this->category->name_ar,
            ]),
            'city' => $this->whenLoaded('city', fn() => [
                'id' => $this->city->id,
                'name' => $this->city->localized_name,
                'name_ar' => $this->city->name_ar,
            ]),
            'address' => $this->localized_address,
            'address_ar' => $this->address_ar,
            'start_date' => $this->start_date->format('Y-m-d'),
            'end_date' => $this->end_date->format('Y-m-d'),
            'opening_time' => $this->opening_time?->format('H:i'),
            'closing_time' => $this->closing_time?->format('H:i'),
            'images' => $this->toStorageUrls($this->images),
            'images_360' => $this->toStorageUrls($this->images_360),
            'main_image' => $this->toStorageUrl($this->main_image),
            'organizer_name' => $this->organizer_name,
            'organizer_phone' => $this->organizer_phone,
            'organizer_email' => $this->organizer_email,
            'website' => $this->website,
            'status' => $this->status->value,
            'is_featured' => $this->is_featured,
            'is_ongoing' => $this->is_ongoing,
            'available_spaces_count' => $this->available_spaces_count,
            'total_spaces_count' => $this->total_spaces_count,
            'min_price' => $this->min_price,
            'is_favorited' => $userId ? Favorite::isFavorited($userId, 'event', $this->id) : false,
        ];
    }
}
