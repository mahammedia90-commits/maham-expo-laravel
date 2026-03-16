<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'id_number' => $this->when(
                $request->input('auth_user_id') === $this->owner_id,
                $this->id_number
            ),
            'member_type' => new MemberTypeResource($this->whenLoaded('memberType')),
            'member_type_id' => $this->member_type_id,
            'owner_type' => $this->owner_type,
            'is_active' => $this->is_active,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
