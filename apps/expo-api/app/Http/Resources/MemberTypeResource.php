<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->localized_name,
            'name_en' => $this->name,
            'name_ar' => $this->name_ar,
            'description' => $this->localized_description,
            'description_en' => $this->description,
            'description_ar' => $this->description_ar,
            'scope' => $this->scope,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'team_members_count' => $this->whenCounted('teamMembers'),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
