<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'request_number' => $this->request_number,
            'event' => new EventListResource($this->whenLoaded('event')),
            'visit_date' => $this->visit_date->format('Y-m-d'),
            'visit_time' => $this->visit_time?->format('H:i'),
            'visitors_count' => $this->visitors_count,
            'notes' => $this->notes,
            'contact_phone' => $this->contact_phone,
            'status' => $this->status->value,
            'status_label' => $this->status_label,
            'status_color' => $this->status->color(),
            'can_be_modified' => $this->can_be_modified,
            'can_be_cancelled' => $this->can_be_cancelled,
            'rejection_reason' => $this->when(
                $this->status->value === 'rejected',
                $this->rejection_reason
            ),
            'reviewed_at' => $this->reviewed_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
