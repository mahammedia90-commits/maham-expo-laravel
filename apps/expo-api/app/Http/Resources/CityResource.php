<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->localized_name,
            'name_en' => $this->name,
            'name_ar' => $this->name_ar,
            'region' => $this->localized_region,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'events_count' => $this->whenCounted('events'),
            'is_active' => $this->is_active,
        ];
    }
}
