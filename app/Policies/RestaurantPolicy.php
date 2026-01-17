<?php

namespace App\Policies;

use App\Models\Restaurant;
use App\Models\User;
use App\Permissions\RestaurantPermissions;

class RestaurantPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(RestaurantPermissions::VIEW_ANY);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') &&
            $user->can(RestaurantPermissions::CREATE) &&
            !$user->restaurant;
    }

    public function update(User $user, Restaurant $restaurant): bool
    {
        return $user->can(RestaurantPermissions::UPDATE) &&
            $user->id === $restaurant->user_id;
    }

    public function search(User $user): bool
    {
        return $user->can(RestaurantPermissions::SEARCH);
    }

    public function delete(User $user, Restaurant $restaurant): bool
    {
        return false;
    }
}
