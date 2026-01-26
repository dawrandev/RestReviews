<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Repositories\DiscoveryRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DiscoveryService
{
    public function __construct(
        protected DiscoveryRepository $discoveryRepository
    ) {}

    /**
     * Get all restaurants with filters.
     */
    public function getAllRestaurants(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->discoveryRepository->getAllRestaurants($filters, $perPage);
    }

    /**
     * Get nearby restaurants based on coordinates.
     */
    public function getNearbyRestaurants(
        float $latitude,
        float $longitude,
        ?float $radiusInKm = null,
        int $perPage = 15
    ): LengthAwarePaginator {
        $radius = $radiusInKm ?? 5; // Default 5km
        return $this->discoveryRepository->getNearbyRestaurants($latitude, $longitude, $radius, $perPage);
    }

    /**
     * Get restaurant by ID with full details.
     */
    public function getRestaurantById(int $id, ?int $clientId = null): ?Restaurant
    {
        return $this->discoveryRepository->getRestaurantById($id, $clientId);
    }

    /**
     * Search restaurants and menu items.
     */
    public function search(string $query, int $perPage = 15): array
    {
        // Search restaurants
        $restaurants = $this->discoveryRepository->searchRestaurants($query, $perPage);

        return [
            'restaurants' => $restaurants,
        ];
    }
}
