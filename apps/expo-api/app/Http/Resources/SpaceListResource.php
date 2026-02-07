<?php

namespace App\Http\Resources;

use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SpaceListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $userId = $request->input('auth_user_id');

        return [
            'id' => $this->id,
            'name' => $this->localized_name,
            'location_code' => $this->location_code,
            'area_sqm' => (float) $this->area_sqm,
            'price_total' => (float) $this->price_total,
            'main_image' => $this->main_image,
            'status' => $this->status->value,
            'is_available' => $this->is_available,
            'floor_number' => $this->floor_number,
            'section' => $this->section,
            'is_favorited' => $userId ? Favorite::isFavorited($userId, 'space', $this->id) : false,
        ];
    }
}
