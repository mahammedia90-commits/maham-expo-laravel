<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->localized_name,
            'name_en' => $this->name,
            'name_ar' => $this->name_ar,
            'description' => $this->localized_description,
            'icon' => $this->icon,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'spaces_count' => $this->spaces_count,
            'available_spaces_count' => $this->available_spaces_count,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
