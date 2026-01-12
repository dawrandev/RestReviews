<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Repositories\AuthRepository;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService,
        protected AuthRepository $authRepository
    ) {}

    public function showLoginForm()
    {
        return view('pages.auth.login');
    }

    public function login(LoginRequest $request)
    {
        $redirectRoute = $this->authService->login($request->validated());

        return redirect()->to($redirectRoute);
    }

    public function logout(Request $request)
    {
        return $request;
        return $this->authRepository->logout($request);

        return redirect()->route('login.form');
    }
}
