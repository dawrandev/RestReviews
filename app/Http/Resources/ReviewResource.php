<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'client' => $this->client ? [
                'id' => $this->client->id,
                'full_name' => $this->client->full_name,
                'image_path' => $this->client->image_path
                    ? asset('storage/' . $this->client->image_path)
                    : null,
            ] : null,
            'is_guest' => $this->client_id === null,
            'restaurant_id' => $this->restaurant_id,
            'rating' => $this->rating,
            'comment' => $this->comment,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
