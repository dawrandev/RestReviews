<?php

namespace App\Http\Controllers;

use App\Repositories\RestaurantRepository;
use App\Services\RestaurantService;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function __construct(
        protected RestaurantService $restaurantService,
        protected RestaurantRepository $restaurantRepository
    ) {
        // 
    }

    public function index(Request $request)
    {
        $restaurants = $this->restaurantService->getRestaurantForUser($request->user());

        return view('pages.restaurants.index', compact('restaurants'));
    }

    public function create()
    {
        return view('pages.restaurants.modals.create');
    }
}
