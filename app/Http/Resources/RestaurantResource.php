<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
{
    // "data" wrapper ni o'chirish
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'brand_id' => $this->brand_id,
            'brand' => $this->brand->name ?? 'N/A',
            'city_id' => $this->city_id,
            'city' => $this->city->translations->first()->name ?? 'N/A',
            'branch_name' => $this->branch_name,
            'phone' => $this->phone,
            'address' => $this->address,
            'description' => $this->description,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_active' => $this->is_active,
            'categories' => $this->categories->pluck('id'),
            'category_names' => $this->categories->pluck('translations.0.name'),
            'images' => $this->images->map(fn($img) => [
                'id' => $img->id,
                'url' => asset('storage/' . $img->image_path),
                'is_cover' => $img->is_cover,
            ]),
            'operating_hours' => $this->operatingHours->map(fn($oh) => [
                'day_of_week' => $oh->day_of_week,
                'opening_time' => $oh->opening_time ? substr($oh->opening_time, 0, 5) : null,
                'closing_time' => $oh->closing_time ? substr($oh->closing_time, 0, 5) : null,
                'is_closed' => $oh->is_closed,
            ]),
            'qr_code' => $this->qr_code ? asset('storage/' . $this->qr_code) : null,
            'created_at' => $this->created_at->format('d.m.Y'),
        ];
    }
}
