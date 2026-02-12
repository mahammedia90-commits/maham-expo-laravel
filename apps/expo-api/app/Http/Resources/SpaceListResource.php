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
            'section' => $this->whenLoaded('section', fn() => [
                'id' => $this->section->id,
                'name' => $this->section->localized_name,
            ]),
            'space_type' => $this->space_type?->value,
            'space_type_label' => $this->space_type ? (app()->getLocale() === 'ar' ? $this->space_type->label() : $this->space_type->labelEn()) : null,
            'payment_system' => $this->payment_system?->value,
            'payment_system_label' => $this->payment_system ? (app()->getLocale() === 'ar' ? $this->payment_system->label() : $this->payment_system->labelEn()) : null,
            'rental_duration' => $this->rental_duration?->value,
            'rental_duration_label' => $this->rental_duration ? (app()->getLocale() === 'ar' ? $this->rental_duration->label() : $this->rental_duration->labelEn()) : null,
            'is_favorited' => $userId ? Favorite::isFavorited($userId, 'space', $this->id) : false,
        ];
    }
}
