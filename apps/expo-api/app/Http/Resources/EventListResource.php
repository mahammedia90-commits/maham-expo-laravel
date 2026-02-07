<?php

namespace App\Http\Resources;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $userId = $request->input('auth_user_id');

        return [
            'id' => $this->id,
            'name' => $this->localized_name,
            'category' => $this->whenLoaded('category', fn() => [
                'id' => $this->category->id,
                'name' => $this->category->localized_name,
            ]),
            'city' => $this->whenLoaded('city', fn() => [
                'id' => $this->city->id,
                'name' => $this->city->localized_name,
            ]),
            'address' => $this->localized_address,
            'start_date' => $this->start_date->format('Y-m-d'),
            'end_date' => $this->end_date->format('Y-m-d'),
            'main_image' => $this->main_image,
            'status' => $this->status->value,
            'is_featured' => $this->is_featured,
            'is_ongoing' => $this->is_ongoing,
            'available_spaces_count' => $this->available_spaces_count,
            'is_favorited' => $userId ? Favorite::isFavorited($userId, 'event', $this->id) : false,
        ];
    }
}
