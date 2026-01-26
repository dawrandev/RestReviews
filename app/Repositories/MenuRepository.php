<?php

namespace App\Repositories;

use App\Models\MenuItem;
use App\Models\MenuSection;
use App\Models\RestaurantMenuItem;
use Illuminate\Database\Eloquent\Collection;

class MenuRepository
{
    /**
     * Get menu for a restaurant grouped by sections.
     */
    public function getRestaurantMenu(int $restaurantId): Collection
    {
        $restaurant = \App\Models\Restaurant::with('brand')->find($restaurantId);

        if (!$restaurant || !$restaurant->brand_id) {
            return collect();
        }

        return MenuSection::where('brand_id', $restaurant->brand_id)
            ->with([
                'translations',
                'menuItems.restaurantMenuItems' => function ($query) use ($restaurantId) {
                    $query->where('restaurant_id', $restaurantId)
                        ->where('is_available', true);
                },
                'menuItems.translations',
            ])
            ->orderBy('sort_order')
            ->get()
            ->map(function ($section) use ($restaurantId) {
                // Filter menu items that are available in this restaurant
                $section->menuItems = $section->menuItems->filter(function ($item) use ($restaurantId) {
                    return $item->restaurantMenuItems->isNotEmpty();
                })->map(function ($item) use ($restaurantId) {
                    // Get restaurant-specific price
                    $restaurantItem = $item->restaurantMenuItems->first();
                    $item->restaurant_price = $restaurantItem ? $restaurantItem->price : $item->base_price;
                    $item->is_available = $restaurantItem ? $restaurantItem->is_available : false;

                    // Clean up
                    unset($item->restaurantMenuItems);

                    return $item;
                });

                return $section;
            })
            ->filter(function ($section) {
                // Remove sections with no available items
                return $section->menuItems->isNotEmpty();
            });
    }

    /**
     * Get menu item by ID.
     */
    public function getMenuItemById(int $id): ?MenuItem
    {
        return MenuItem::with([
            'menuSection:id,brand_id',
            'translations',
        ])->find($id);
    }

    /**
     * Get menu item details for a specific restaurant.
     */
    public function getRestaurantMenuItem(int $restaurantId, int $menuItemId): ?array
    {
        $restaurantItem = RestaurantMenuItem::where('restaurant_id', $restaurantId)
            ->where('menu_item_id', $menuItemId)
            ->with(['menuItem.translations'])
            ->first();

        if (!$restaurantItem) {
            return null;
        }

        $menuItem = $restaurantItem->menuItem;

        return [
            'id' => $menuItem->id,
            'menu_section_id' => $menuItem->menu_section_id,
            'name' => $menuItem->name,
            'description' => $menuItem->description,
            'image_path' => $menuItem->image_path,
            'base_price' => $menuItem->base_price,
            'restaurant_price' => $restaurantItem->price,
            'is_available' => $restaurantItem->is_available,
        ];
    }

    /**
     * Search menu items by name.
     */
    public function searchMenuItems(string $query, ?int $restaurantId = null): Collection
    {
        $menuItems = MenuItem::query()
            ->with(['translations', 'menuSection'])
            ->whereHas('translations', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            });

        if ($restaurantId) {
            $menuItems->whereHas('restaurantMenuItems', function ($q) use ($restaurantId) {
                $q->where('restaurant_id', $restaurantId)
                    ->where('is_available', true);
            });
        }

        return $menuItems->get();
    }
}
