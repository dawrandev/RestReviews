<?php

namespace App\Services;

use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        protected AuthRepository $authRepository
    ) {}

    public function login(array $data): string
    {
        if (!Auth::attempt([
            'login' => $data['login'],
            'password' => $data['password'],
        ])) {
            throw ValidationException::withMessages([
                'login' => __('auth.failed'),
            ]);
        }

        $user = Auth::user();

        return $this->determineRedirect($user);
    }

    protected function determineRedirect($user): string
    {
        if ($user->hasRole('superadmin')) {
            return route('superadmin.dashboard');
        }

        if ($user->hasRole('admin')) {
            if (!$user->restaurant) {
                return route('admin.restaurants.create');
            }

            return route('admin.dashboard');
        }

        Auth::logout();
        throw ValidationException::withMessages(['login' => __('Access denied.')]);
    }
}
