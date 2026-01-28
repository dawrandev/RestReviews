<?php

namespace App\Services;

use App\Repositories\FavoriteRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class FavoriteService
{
    public function __construct(
        protected FavoriteRepository $favoriteRepository
    ) {}

    /**
     * Get all favorites for a client.
     */
    public function getClientFavorites(int $clientId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->favoriteRepository->getByClientId($clientId, $perPage);
    }

    /**
     * Toggle favorite status for a restaurant (with device tracking).
     */
    public function toggleFavorite(?int $clientId, int $restaurantId, string $deviceId, string $ipAddress): array
    {
        // Check by device_id first
        $favorite = \App\Models\Favorite::where('device_id', $deviceId)
            ->where('restaurant_id', $restaurantId)
            ->first();

        // If authenticated and not found by device, check by client_id
        if (!$favorite && $clientId) {
            $favorite = $this->favoriteRepository->findByClientAndRestaurant($clientId, $restaurantId);
        }

        if ($favorite) {
            $this->favoriteRepository->delete($favorite);
            return [
                'is_favorited' => false,
                'message' => 'Restoran sevimlilardan o\'chirildi',
            ];
        }

        $this->favoriteRepository->create([
            'client_id' => $clientId,
            'restaurant_id' => $restaurantId,
            'device_id' => $deviceId,
            'ip_address' => $ipAddress,
        ]);

        return [
            'is_favorited' => true,
            'message' => 'Restoran sevimlilarga qo\'shildi',
        ];
    }

    /**
     * Check if a restaurant is favorited by a client.
     */
    public function isFavorited(int $clientId, int $restaurantId): bool
    {
        return $this->favoriteRepository->isFavorited($clientId, $restaurantId);
    }

    /**
     * Get favorite count for a restaurant.
     */
    public function getFavoriteCount(int $restaurantId): int
    {
        return $this->favoriteRepository->getFavoriteCount($restaurantId);
    }
}
