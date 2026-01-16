<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService,
        protected UserRepository $userRepository,
    ) {}

    public function index(Request $request)
    {
        $users = $this->userRepository->getAll($request->all());

        return view('pages.users.index', compact('users'));
    }

    public function store(StoreUserRequest $request)
    {
        $user = $this->userService->createUser($request->validated());

        return redirect()->route('users.index')->with('success', 'Администратор успешно создан');
    }

    public function update(UpdateUserRequest $request, User $user)
    {

        $user = $this->userService->updateUser($user, $request->validated());

        return redirect()->route('users.index')
            ->with('success', 'Администратор успешно обновлен')
            ->with('edit_user_id', $user->id);
    }

    public function edit(User $user)
    {
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'login' => $user->login
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Администратор успешно удален');
    }
}
