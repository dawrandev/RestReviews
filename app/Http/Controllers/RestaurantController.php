<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Services\RestaurantService;
use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use App\Http\Resources\RestaurantResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RestaurantController extends Controller
{
    public function __construct(
        protected RestaurantService $restaurantService
    ) {}

    public function index(Request $request)
    {
        $restaurants = $this->restaurantService->getRestaurantForUser($request->user());

        $needsRestaurant = $request->user()->hasRole('admin') && $request->user()->restaurants->isEmpty();

        return view('pages.restaurants.index', compact('restaurants', 'needsRestaurant'));
    }

    public function create()
    {
        return view('pages.restaurants.create');
    }

    public function store(StoreRestaurantRequest $request)
    {
        $this->authorize('create', Restaurant::class);

        try {
            $data = $request->validated();
            $restaurant = $this->restaurantService->createRestaurant($data, $request->user());

            // Create operating hours
            if ($request->has('operating_hours')) {
                foreach ($request->operating_hours as $dayNumber => $hours) {
                    $restaurant->operatingHours()->create([
                        'day_of_week' => $dayNumber,
                        'opening_time' => $hours['opening_time'] ?? null,
                        'closing_time' => $hours['closing_time'] ?? null,
                        'is_closed' => isset($hours['is_closed']) ? true : false,
                    ]);
                }
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ресторан успешно создан'
                ], 201);
            }

            return redirect()->route('restaurants.index')
                ->with('success', 'Ресторан успешно создан');
        } catch (\Exception $e) {
            // Return JSON error for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка при создании ресторана: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()
                ->with('error', 'Ошибка при создании ресторана: ' . $e->getMessage());
        }
    }

    public function show(Restaurant $restaurant)
    {
        $restaurant = $this->restaurantService->findRestaurant($restaurant->id);

        return new RestaurantResource($restaurant);
    }
    public function edit(Restaurant $restaurant)
    {
        Gate::authorize('update', $restaurant);

        $restaurant = $this->restaurantService->findRestaurant($restaurant->id);

        return new RestaurantResource($restaurant);
    }

    public function update(UpdateRestaurantRequest $request, Restaurant $restaurant)
    {
        Gate::authorize('update', $restaurant);

        try {
            $data = $request->validated();

            if ($request->hasFile('images')) {
                $data['new_images'] = $request->file('images');
                unset($data['images']);
            }

            $updatedRestaurant = $this->restaurantService->updateRestaurant($restaurant, $data);

            // Update operating hours
            if ($request->has('operating_hours')) {
                // Delete existing operating hours
                $restaurant->operatingHours()->delete();

                // Create new operating hours
                foreach ($request->operating_hours as $dayNumber => $hours) {
                    $restaurant->operatingHours()->create([
                        'day_of_week' => $dayNumber,
                        'opening_time' => $hours['opening_time'] ?? null,
                        'closing_time' => $hours['closing_time'] ?? null,
                        'is_closed' => isset($hours['is_closed']) ? true : false,
                    ]);
                }
            }

            // Return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Ресторан успешно обновлен'
                ], 200);
            }

            return redirect()->route('restaurants.index')->with('success', 'Ресторан успешно обновлен');
        } catch (\Exception $e) {
            // Return JSON error for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ошибка при обновлении ресторана: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->with('error', 'Ошибка при обновлении ресторана: ' . $e->getMessage());
        }
    }

    public function destroy(Restaurant $restaurant)
    {
        // Policy: delete (Sizda return false edi, lekin superadmin baribir o'chira oladi)
        Gate::authorize('delete', $restaurant);

        try {
            $this->restaurantService->deleteRestaurant($restaurant);
            return redirect()->route('restaurants.index')->with('success', 'Ресторан был закрыт');
        } catch (\Exception $e) {
            return back()->with('error', 'Xatolik: ' . $e->getMessage());
        }
    }

    public function deleteImage($imageId)
    {
        try {
            $deleted = $this->restaurantService->deleteImage($imageId);

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Изображение успешно удалено'
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Не удалось удалить изображение'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении изображения: ' . $e->getMessage()
            ], 500);
        }
    }
}
