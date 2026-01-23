<?php

namespace App\Repositories;

use App\Models\City;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CityRepository
{
    public function paginate(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        $query = City::with(['translations'])->withCount(['restaurants']);

        if ($search) {
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function findById(int $id): ?City
    {
        return City::with(['translations', 'restaurants'])->find($id);
    }

    public function create(array $data): City
    {
        return DB::transaction(function () use ($data) {
            $city = City::create();

            if (isset($data['translations'])) {
                foreach ($data['translations'] as $langCode => $name) {
                    if (!empty($name)) {
                        $city->translations()->create([
                            'code' => $langCode,
                            'name' => $name,
                        ]);
                    }
                }
            }

            return $city->load('translations');
        });
    }

    public function update(City $city, array $data): City
    {
        return DB::transaction(function () use ($city, $data) {

            if (isset($data['translations'])) {
                $city->translations()->delete();

                foreach ($data['translations'] as $langCode => $name) {
                    if (!empty($name)) {
                        $city->translations()->create([
                            'code' => $langCode,
                            'name' => $name,
                        ]);
                    }
                }
            }

            return $city->fresh('translations');
        });
    }

    public function delete(City $city): bool
    {
        return DB::transaction(function () use ($city) {
            $city->translations()->delete();
            return $city->delete();
        });
    }
}
