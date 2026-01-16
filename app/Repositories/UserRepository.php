<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
    public function getAll(array $filters = [])
    {
        $query = User::query();

        $query->when(isset($filters['search']), function ($q) use ($filters) {
            $search = $filters['search'];

            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('name', 'like', "%{$search}%")
                    ->orWhereHas('restaurant', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
            });
        });

        return $query->with('restaurant')->paginate(10);
    }

    public function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'login' => $data['login'],
            'password' => Hash::make($data['password']),
        ]);
    }

    public function update(User $user, array $data)
    {
        $updateData = [
            'name' => $data['name'],
            'login' => $data['login'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);
        return $user;
    }
}
