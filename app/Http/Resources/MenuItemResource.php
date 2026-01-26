<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image_path' => $this->image_path ? asset('storage/' . $this->image_path) : null,
            'base_price' => $this->base_price,
            'weight_grams' => $this->weight_grams,
            'restaurant_price' => $this->when(isset($this->restaurant_price), $this->restaurant_price),
            'is_available' => $this->when(isset($this->is_available), $this->is_available),
        ];
    }
}
