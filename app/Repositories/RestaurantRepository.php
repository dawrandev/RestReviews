<?php

namespace App\Repositories;

use App\Models\Restaurant;

class RestaurantRepository
{
    public function getAll()
    {
        return Restaurant::paginate(10);
    }

    public function getById($id)
    {
        return Restaurant::where('id', $id)->paginate(10);
    }
}
