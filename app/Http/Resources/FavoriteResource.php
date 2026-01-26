<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'restaurant' => new RestaurantListResource($this->restaurant),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
