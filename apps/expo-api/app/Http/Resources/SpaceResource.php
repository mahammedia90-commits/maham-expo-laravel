<?php

namespace App\Http\Resources;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpaceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $userId = $request->input('auth_user_id');

        return [
            'id' => $this->id,
            'event' => new EventListResource($this->whenLoaded('event')),
            'name' => $this->localized_name,
            'name_en' => $this->name,
            'name_ar' => $this->name_ar,
            'description' => $this->localized_description,
            'location_code' => $this->location_code,
            'area_sqm' => (float) $this->area_sqm,
            'price_per_day' => $this->price_per_day ? (float) $this->price_per_day : null,
            'price_total' => (float) $this->price_total,
            'images' => $this->images ?? [],
            'main_image' => $this->main_image,
            'amenities' => $this->localized_amenities ?? [],
            'status' => $this->status->value,
            'status_label' => app()->getLocale() === 'ar' ? $this->status->label() : $this->status->labelEn(),
            'is_available' => $this->is_available,
            'floor_number' => $this->floor_number,
            'section' => $this->section,
            'is_favorited' => $userId ? Favorite::isFavorited($userId, 'space', $this->id) : false,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
