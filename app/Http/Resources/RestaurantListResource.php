<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_name' => $this->branch_name,
            'address' => $this->address,
            'phone' => $this->phone,

            // Brand info
            'brand' => [
                'id' => $this->brand->id,
                'name' => $this->brand->name,
                'logo' => $this->brand->logo ? asset('storage/' . $this->brand->logo) : null,
            ],

            // City
            'city' => [
                'id' => $this->city->id,
                'name' => $this->city->getTranslatedName(),
            ],

            // Cover image
            'cover_image' => $this->coverImage
                ? asset('storage/' . $this->coverImage->image_path)
                : null,

            // Categories
            'categories' => $this->categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->getTranslatedName(),
                ];
            }),

            // Rating info
            'average_rating' => round($this->reviews_avg_rating ?? 0, 1),
            'reviews_count' => $this->reviews_count ?? 0,

            // Distance (if available from nearby search)
            'distance' => $this->when(isset($this->distance), round($this->distance, 2)),
        ];
    }
}
