<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Auth;

class AuthRepository
{
    public function logout($request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Auth::logout();
    }
}
