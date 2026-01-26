<?php

namespace App\Repositories;

use App\Models\Restaurant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class DiscoveryRepository
{
    /**
     * Get all active restaurants with pagination and filters.
     */
    public function getAllRestaurants(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Restaurant::query()
            ->where('is_active', true)
            ->with([
                'brand:id,name,logo',
                'city:id',
                'city.translations',
                'coverImage:id,restaurant_id,image_path,is_cover',
                'categories:id,icon',
                'categories.translations',
            ])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');

        // Filter by category
        if (!empty($filters['category_id'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('categories.id', $filters['category_id']);
            });
        }

        // Filter by city
        if (!empty($filters['city_id'])) {
            $query->where('city_id', $filters['city_id']);
        }

        // Filter by brand
        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        // Sort by rating if requested
        if (!empty($filters['sort_by']) && $filters['sort_by'] === 'rating') {
            $query->orderBy('reviews_avg_rating', 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }

    /**
     * Get nearby restaurants based on coordinates.
     */
    public function getNearbyRestaurants(
        float $latitude,
        float $longitude,
        float $radiusInKm = 5,
        int $perPage = 15
    ): LengthAwarePaginator {
        // Using Haversine formula for distance calculation
        $query = Restaurant::query()
            ->select([
                'restaurants.*',
                DB::raw("
                    (6371 * acos(
                        cos(radians(?)) *
                        cos(radians(ST_Y(location))) *
                        cos(radians(ST_X(location)) - radians(?)) +
                        sin(radians(?)) *
                        sin(radians(ST_Y(location)))
                    )) AS distance
                ")
            ])
            ->setBindings([$latitude, $longitude, $latitude])
            ->where('is_active', true)
            ->whereNotNull('location')
            ->having('distance', '<=', $radiusInKm)
            ->with([
                'brand:id,name,logo',
                'city:id',
                'city.translations',
                'coverImage:id,restaurant_id,image_path,is_cover',
                'categories:id,icon',
                'categories.translations',
            ])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderBy('distance', 'asc');

        return $query->paginate($perPage);
    }

    /**
     * Get restaurant by ID with full details.
     */
    public function getRestaurantById(int $id, ?int $clientId = null): ?Restaurant
    {
        $restaurant = Restaurant::query()
            ->where('id', $id)
            ->where('is_active', true)
            ->with([
                'brand:id,name,logo,description',
                'city:id',
                'city.translations',
                'images:id,restaurant_id,image_path,is_cover',
                'categories:id,icon',
                'categories.translations',
                'operatingHours:id,restaurant_id,day_of_week,opening_time,closing_time,is_closed',
            ])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->first();

        if ($restaurant && $clientId) {
            // Add is_favorited attribute
            $restaurant->is_favorited = $restaurant->favorites()
                ->where('client_id', $clientId)
                ->exists();
        }

        return $restaurant;
    }

    /**
     * Search restaurants by name or brand.
     */
    public function searchRestaurants(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return Restaurant::query()
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('branch_name', 'like', "%{$query}%")
                    ->orWhereHas('brand', function ($q) use ($query) {
                        $q->where('name', 'like', "%{$query}%");
                    });
            })
            ->with([
                'brand:id,name,logo',
                'city:id',
                'city.translations',
                'coverImage:id,restaurant_id,image_path,is_cover',
                'categories:id,icon',
                'categories.translations',
            ])
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
}
