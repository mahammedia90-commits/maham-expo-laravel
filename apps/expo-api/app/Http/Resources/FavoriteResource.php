<?php

namespace App\Http\Resources;

use App\Models\Event;
use App\Models\Space;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $item = null;
        $type = null;

        if ($this->favoritable instanceof Event) {
            $item = new EventListResource($this->favoritable);
            $type = 'event';
        } elseif ($this->favoritable instanceof Space) {
            $item = new SpaceListResource($this->favoritable);
            $type = 'space';
        }

        return [
            'id' => $this->id,
            'type' => $type,
            'item' => $item,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
