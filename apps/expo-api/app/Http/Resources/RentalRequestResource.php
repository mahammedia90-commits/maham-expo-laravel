<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RentalRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'request_number' => $this->request_number,
            'space' => new SpaceResource($this->whenLoaded('space')),
            'business_profile' => new BusinessProfileResource($this->whenLoaded('businessProfile')),
            'start_date' => $this->start_date->format('Y-m-d'),
            'end_date' => $this->end_date->format('Y-m-d'),
            'rental_days' => $this->rental_days,
            'total_price' => (float) $this->total_price,
            'paid_amount' => (float) $this->paid_amount,
            'remaining_amount' => (float) $this->remaining_amount,
            'notes' => $this->notes,
            'status' => $this->status->value,
            'status_label' => $this->status_label,
            'status_color' => $this->status->color(),
            'payment_status' => $this->payment_status->value,
            'payment_status_label' => $this->payment_status_label,
            'can_be_modified' => $this->can_be_modified,
            'can_be_cancelled' => $this->can_be_cancelled,
            'rejection_reason' => $this->when(
                $this->status->value === 'rejected',
                $this->rejection_reason
            ),
            'first_payment_at' => $this->first_payment_at?->toISOString(),
            'reviewed_at' => $this->reviewed_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
