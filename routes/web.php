<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;



Route::get('/', [AuthController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::prefix('superadmin/dashboard')->middleware(['auth', 'role:superadmin'])->name('superadmin.')->group(function () {
    Route::get('/', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'dashboard'])->name('dashboard');
});

Route::prefix('admin/dashboard')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'dashboard'])->name('dashboard');
});

Route::prefix('/restaurants')->middleware(['auth'])->name('restaurants.')->group(function () {
    Route::get('/', [App\Http\Controllers\RestaurantController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\RestaurantController::class, 'create'])->name('create');
    Route::get('/{restaurant}/show', [App\Http\Controllers\RestaurantController::class, 'show'])->name('show');
    Route::post('/store', [App\Http\Controllers\RestaurantController::class, 'store'])->name('store');
    Route::get('/{restaurant}/edit', [App\Http\Controllers\RestaurantController::class, 'edit'])->name('edit');
    Route::put('/{restaurant}/update', [App\Http\Controllers\RestaurantController::class, 'update'])->name('update');
    Route::delete('/{restaurant}/delete', [App\Http\Controllers\RestaurantController::class, 'destroy'])->name('destroy');
    Route::delete('/images/{image}', [App\Http\Controllers\RestaurantController::class, 'deleteImage'])->name('images.delete');
});

Route::prefix('/users')->middleware(['auth', 'role:superadmin'])->name('users.')->group(function () {
    Route::get('/', [App\Http\Controllers\UserController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\UserController::class, 'create'])->name('create');
    Route::get('/{user}/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('edit');
    Route::delete('/destroy/{user}', [App\Http\Controllers\UserController::class, 'destroy'])->name('destroy');
    Route::post('/store', [App\Http\Controllers\UserController::class, 'store'])->name('store');
    Route::put('/{user}/update', [App\Http\Controllers\UserController::class, 'update'])->name('update');
});

Route::middleware(['auth'])->group(function () {
    Route::resource('categories', CategoryController::class);
});

Route::middleware(['auth'])->group(function () {
    Route::resource('brands', App\Http\Controllers\BrandController::class);
});
Route::middleware(['auth'])->group(function () {
    Route::resource('cities', App\Http\Controllers\CityController::class);
});
