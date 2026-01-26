<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_name' => $this->branch_name,
            'phone' => $this->phone,
            'description' => $this->description,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_active' => $this->is_active,
            'qr_code' => $this->qr_code ? asset('storage/' . $this->qr_code) : null,

            // Brand info
            'brand' => [
                'id' => $this->brand->id,
                'name' => $this->brand->name,
                'logo' => $this->brand->logo ? asset('storage/' . $this->brand->logo) : null,
                'description' => $this->brand->description,
            ],

            // City
            'city' => [
                'id' => $this->city->id,
                'name' => $this->city->getTranslatedName(),
            ],

            // Categories
            'categories' => $this->categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->getTranslatedName(),
                    'icon' => $category->icon ? asset('storage/' . $category->icon) : null,
                ];
            }),

            // Images
            'images' => $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image_path' => asset('storage/' . $image->image_path),
                    'is_cover' => $image->is_cover,
                ];
            }),

            // Operating hours
            'operating_hours' => $this->operatingHours->map(function ($hour) {
                return [
                    'day_of_week' => $hour->day_of_week,
                    'opening_time' => $hour->opening_time,
                    'closing_time' => $hour->closing_time,
                    'is_closed' => $hour->is_closed,
                ];
            }),

            // Rating info
            'average_rating' => round($this->reviews_avg_rating ?? 0, 1),
            'reviews_count' => $this->reviews_count ?? 0,

            // Favorite status (if client is authenticated)
            'is_favorited' => $this->when(isset($this->is_favorited), $this->is_favorited),

            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
