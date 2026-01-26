<?php

namespace App\Repositories;

use App\Models\MenuItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class MenuItemRepository
{
    public function getAllBySection(int $menuSectionId): Collection
    {
        return MenuItem::where('menu_section_id', $menuSectionId)
            ->with('translations')
            ->withCount('restaurantMenuItems')
            ->latest()
            ->get();
    }

    public function getAllByBrand(int $brandId): Collection
    {
        return MenuItem::whereHas('menuSection', function ($query) use ($brandId) {
            $query->where('brand_id', $brandId);
        })
            ->with(['menuSection.translations', 'translations'])
            ->latest()
            ->get();
    }

    public function paginate(int $brandId, int $perPage = 15, ?string $search = null, ?int $sectionId = null): LengthAwarePaginator
    {
        $query = MenuItem::whereHas('menuSection', function ($q) use ($brandId) {
            $q->where('brand_id', $brandId);
        })->with(['menuSection.translations', 'translations']);

        if ($search) {
            $query->whereHas('translations', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($sectionId) {
            $query->where('menu_section_id', $sectionId);
        }

        return $query->latest()->paginate($perPage);
    }

    public function findById(int $id): ?MenuItem
    {
        return MenuItem::with(['menuSection.translations', 'restaurantMenuItems', 'translations'])->find($id);
    }

    public function create(array $data): MenuItem
    {
        return DB::transaction(function () use ($data) {
            $menuItem = MenuItem::create([
                'menu_section_id' => $data['menu_section_id'],
                'image_path' => $data['image_path'] ?? null,
                'base_price' => $data['base_price'] ?? null,
                'weight' => $data['weight'] ?? null,
            ]);

            // Create translations
            if (isset($data['translations'])) {
                foreach ($data['translations'] as $langCode => $translation) {
                    $menuItem->translations()->create([
                        'lang_code' => $langCode,
                        'name' => $translation['name'],
                        'description' => $translation['description'] ?? null,
                    ]);
                }
            }

            return $menuItem->fresh('translations');
        });
    }

    public function update(MenuItem $menuItem, array $data): MenuItem
    {
        return DB::transaction(function () use ($menuItem, $data) {
            $menuItem->update([
                'menu_section_id' => $data['menu_section_id'] ?? $menuItem->menu_section_id,
                'image_path' => $data['image_path'] ?? $menuItem->image_path,
                'base_price' => $data['base_price'] ?? $menuItem->base_price,
                'weight' => $data['weight'] ?? $menuItem->weight,
            ]);

            // Update translations
            if (isset($data['translations'])) {
                foreach ($data['translations'] as $langCode => $translation) {
                    $menuItem->translations()->updateOrCreate(
                        ['lang_code' => $langCode],
                        [
                            'name' => $translation['name'],
                            'description' => $translation['description'] ?? null,
                        ]
                    );
                }
            }

            return $menuItem->fresh('translations');
        });
    }

    public function delete(MenuItem $menuItem): bool
    {
        return DB::transaction(function () use ($menuItem) {
            return $menuItem->delete();
        });
    }
}
