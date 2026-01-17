<?php

namespace App\Services;

use App\Repositories\RestaurantRepository;

class RestaurantService
{
    public function __construct(
        protected RestaurantRepository $restaurantRepository
    ) {}

    public function getRestaurantForUser($user)
    {
        if ($user->hasRole('superadmin')) {
            return $this->restaurantRepository->getAll();
        }

        return $this->restaurantRepository->getByUserId($user->id);
    }
}
