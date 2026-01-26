<?php

namespace App\Repositories;

use App\Models\Restaurant;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RestaurantRepository
{
    public function getAll(int $perPage = 10): LengthAwarePaginator
    {
        return Restaurant::with(['brand', 'city.translations', 'images', 'categories.translations'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->latest()
            ->paginate($perPage);
    }

    public function getByUserId(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return Restaurant::with(['brand', 'city.translations', 'images', 'categories.translations'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function findById(int $id): ?Restaurant
    {
        return Restaurant::with(['brand', 'city.translations', 'images', 'categories.translations', 'operatingHours'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->find($id);
    }

    public function create(array $data): Restaurant
    {
        return DB::transaction(function () use ($data) {
            // Create restaurant with location
            $restaurant = Restaurant::create([
                'user_id' => $data['user_id'],
                'brand_id' => $data['brand_id'],
                'city_id' => $data['city_id'],
                'branch_name' => $data['branch_name'],
                'phone' => $data['phone'] ?? null,
                'description' => $data['description'] ?? null,
                'address' => $data['address'],
                'location' => isset($data['latitude']) && isset($data['longitude'])
                    ? DB::raw("ST_GeomFromText('POINT({$data['longitude']} {$data['latitude']})')")
                    : null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            // Attach categories
            if (isset($data['categories']) && is_array($data['categories'])) {
                $restaurant->categories()->attach($data['categories']);
            }

            // Save images
            if (isset($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $index => $imagePath) {
                    $restaurant->images()->create([
                        'image_path' => $imagePath,
                        'is_cover' => $index === 0, // First image is cover
                    ]);
                }
            }

            return $restaurant->load(['brand', 'city.translations', 'images', 'categories.translations']);
        });
    }

    public function update(Restaurant $restaurant, array $data): Restaurant
    {
        return DB::transaction(function () use ($restaurant, $data) {
            $updateData = [
                'brand_id' => $data['brand_id'] ?? $restaurant->brand_id,
                'city_id' => $data['city_id'] ?? $restaurant->city_id,
                'branch_name' => $data['branch_name'] ?? $restaurant->branch_name,
                'phone' => $data['phone'] ?? $restaurant->phone,
                'description' => $data['description'] ?? $restaurant->description,
                'address' => $data['address'] ?? $restaurant->address,
                'is_active' => $data['is_active'] ?? $restaurant->is_active,
            ];

            // Update location if provided
            if (isset($data['latitude']) && isset($data['longitude'])) {
                $updateData['location'] = DB::raw("ST_GeomFromText('POINT({$data['longitude']} {$data['latitude']})')");
            }

            $restaurant->update($updateData);

            // Update categories
            if (isset($data['categories'])) {
                $restaurant->categories()->sync($data['categories']);
            }

            // Add new images
            if (isset($data['new_images']) && is_array($data['new_images'])) {
                foreach ($data['new_images'] as $imagePath) {
                    $restaurant->images()->create([
                        'image_path' => $imagePath,
                        'is_cover' => false,
                    ]);
                }
            }

            return $restaurant->fresh(['brand', 'city.translations', 'images', 'categories.translations']);
        });
    }

    public function delete(Restaurant $restaurant): bool
    {
        return DB::transaction(function () use ($restaurant) {
            $restaurant->categories()->detach();
            $restaurant->images()->delete();
            $restaurant->operatingHours()->delete();
            return $restaurant->delete();
        });
    }

    public function deleteImage(int $imageId): bool
    {
        $image = \App\Models\RestaurantImage::find($imageId);
        if ($image) {
            Storage::disk('public')->delete($image->image_path);
            return $image->delete();
        }
        return false;
    }
}
