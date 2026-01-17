<?php

namespace App\Repositories;

use App\Models\Restaurant;

class RestaurantRepository
{
    public function getAll()
    {
        return Restaurant::paginate(10);
    }

    public function getByUserId($userId)
    {
        return Restaurant::where('user_id', $userId)->paginate(10);
    }
}
