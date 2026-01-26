<?php

namespace App\Repositories;

use App\Models\Favorite;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FavoriteRepository
{
    /**
     * Get all favorites for a client.
     */
    public function getByClientId(int $clientId, int $perPage = 15): LengthAwarePaginator
    {
        return Favorite::where('client_id', $clientId)
            ->with([
                'restaurant.brand:id,name,logo',
                'restaurant.city:id',
                'restaurant.city.translations',
                'restaurant.coverImage:id,restaurant_id,image_path',
                'restaurant.categories:id,icon',
                'restaurant.categories.translations',
            ])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Find favorite by client and restaurant.
     */
    public function findByClientAndRestaurant(int $clientId, int $restaurantId): ?Favorite
    {
        return Favorite::where('client_id', $clientId)
            ->where('restaurant_id', $restaurantId)
            ->first();
    }

    /**
     * Create a new favorite.
     */
    public function create(array $data): Favorite
    {
        return Favorite::create($data);
    }

    /**
     * Delete a favorite.
     */
    public function delete(Favorite $favorite): bool
    {
        return $favorite->delete();
    }

    /**
     * Check if a restaurant is favorited by a client.
     */
    public function isFavorited(int $clientId, int $restaurantId): bool
    {
        return Favorite::where('client_id', $clientId)
            ->where('restaurant_id', $restaurantId)
            ->exists();
    }

    /**
     * Get favorite count for a restaurant.
     */
    public function getFavoriteCount(int $restaurantId): int
    {
        return Favorite::where('restaurant_id', $restaurantId)->count();
    }
}
