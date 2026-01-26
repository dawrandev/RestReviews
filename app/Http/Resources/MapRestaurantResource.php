<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MapRestaurantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource['id'],
            'name' => $this->resource['branch_name'],
            'brand_name' => $this->resource['brand']['name'] ?? null,
            'latitude' => $this->resource['latitude'],
            'longitude' => $this->resource['longitude'],
            'average_rating' => round($this->resource['reviews_avg_rating'] ?? 0, 1),
        ];
    }
}
