<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    public function createUser(array $data)
    {
        try {
            return DB::transaction(function () use ($data) {
                $user = $this->userRepository->create($data);
                $user->assignRole('admin');
                return $user;
            });
        } catch (\Exception $e) {
            Log::error('User creation failed', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    public function updateUser(User $user, array $data)
    {
        try {
            return DB::transaction(function () use ($user, $data) {
                return $this->userRepository->update($user, $data);
            });
        } catch (\Exception $e) {
            Log::error('User update failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'data' => $data
            ]);
            throw $e;
        }
    }
}
