<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RestaurantDiscoveryController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\SearchController;

/*
|--------------------------------------------------------------------------
| Public API Routes (No Authentication Required)
|--------------------------------------------------------------------------
*/

// Authentication
Route::prefix('auth')->group(function () {
    Route::post('/send-code', [AuthController::class, 'sendCode']);
    Route::post('/verify-code', [AuthController::class, 'verifyCode']);
});

// Restaurant Discovery
Route::prefix('restaurants')->group(function () {
    Route::get('/', [RestaurantDiscoveryController::class, 'index']);
    Route::get('/nearby', [RestaurantDiscoveryController::class, 'nearby']);
    Route::get('/map', [RestaurantDiscoveryController::class, 'map']);
    Route::get('/{id}', [RestaurantDiscoveryController::class, 'show']);

    Route::get('/{id}/menu', [MenuController::class, 'getRestaurantMenu']);

    Route::get('/{id}/reviews', [ReviewController::class, 'index']);
});

Route::get('/menu-items/{id}', [MenuController::class, 'show']);

Route::get('/search', [SearchController::class, 'search']);

// Reviews (now guest-friendly with device tracking)
Route::prefix('restaurants')->group(function () {
    Route::post('/{id}/reviews', [ReviewController::class, 'store'])->middleware('throttle:10,1'); // 10 requests per minute
});

// Favorites (guest-friendly with device tracking)
Route::prefix('restaurants')->group(function () {
    Route::post('/{id}/favorite', [FavoriteController::class, 'toggle'])->middleware('throttle:20,1');
});

/*
|--------------------------------------------------------------------------
| Protected API Routes (Authentication Required)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::prefix('auth')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    // Reviews (update/delete only for authenticated users)
    Route::prefix('reviews')->group(function () {
        Route::put('/{id}', [ReviewController::class, 'update']);
        Route::delete('/{id}', [ReviewController::class, 'destroy']);
    });

    // Favorites list (authenticated only)
    Route::get('/favorites', [FavoriteController::class, 'index']);
});
