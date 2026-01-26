<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Repositories\MenuRepository;
use Illuminate\Database\Eloquent\Collection;

class MenuService
{
    public function __construct(
        protected MenuRepository $menuRepository
    ) {}

    /**
     * Get menu for a restaurant.
     */
    public function getRestaurantMenu(int $restaurantId): Collection
    {
        return $this->menuRepository->getRestaurantMenu($restaurantId);
    }

    /**
     * Get menu item details.
     */
    public function getMenuItemById(int $id): ?MenuItem
    {
        return $this->menuRepository->getMenuItemById($id);
    }

    /**
     * Get menu item details for a specific restaurant.
     */
    public function getRestaurantMenuItem(int $restaurantId, int $menuItemId): ?array
    {
        return $this->menuRepository->getRestaurantMenuItem($restaurantId, $menuItemId);
    }

    /**
     * Search menu items.
     */
    public function searchMenuItems(string $query, ?int $restaurantId = null): Collection
    {
        return $this->menuRepository->searchMenuItems($query, $restaurantId);
    }
}
