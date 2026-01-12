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
        if ($user->isSuperAdmin()) {
            return route('dashboard.superadmin.index');
        }

        if ($user->isAdmin()) {
            if (!$user->restaurant_id) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'login' => __('К вам не прикреплен ресторан. Пожалуйста, свяжитесь с администратором'),
                ]);
            }
            return route('dashboard.admin.index');
        }

        Auth::logout();
        throw ValidationException::withMessages([
            'login' => __('У вас нет разрешения на доступ к системе.'),
        ]);
    }
}
