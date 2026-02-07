<?php

namespace App\Http\Resources;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
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
            'category' => new CategoryResource($this->whenLoaded('category')),
            'city' => new CityResource($this->whenLoaded('city')),
            'address' => $this->localized_address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'start_date' => $this->start_date->format('Y-m-d'),
            'end_date' => $this->end_date->format('Y-m-d'),
            'opening_time' => $this->opening_time?->format('H:i'),
            'closing_time' => $this->closing_time?->format('H:i'),
            'images' => $this->images ?? [],
            'main_image' => $this->main_image,
            'features' => $this->localized_features ?? [],
            'organizer' => [
                'name' => $this->organizer_name,
                'phone' => $this->organizer_phone,
                'email' => $this->organizer_email,
            ],
            'website' => $this->website,
            'status' => $this->status->value,
            'status_label' => app()->getLocale() === 'ar' ? $this->status->label() : $this->status->labelEn(),
            'is_featured' => $this->is_featured,
            'is_ongoing' => $this->is_ongoing,
            'is_upcoming' => $this->is_upcoming,
            'is_ended' => $this->is_ended,
            'views_count' => $this->views_count,
            'available_spaces_count' => $this->available_spaces_count,
            'spaces_count' => $this->whenCounted('spaces'),
            'is_favorited' => $userId ? Favorite::isFavorited($userId, 'event', $this->id) : false,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
