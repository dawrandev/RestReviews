<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;



Route::get('/', [AuthController::class, 'showLoginForm'])->middleware('guest')->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::middleware(['auth', 'role:superadmin'])
    ->prefix('superadmin')
    ->name('dashboard.superadmin.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('pages.dashboard.superadmin.index');
        })->name('index');
    });

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('dashboard.admin.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('pages.dashboard.admin.index');
        })->name('index');
    });

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
