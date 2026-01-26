<?php

namespace Database\Seeders;

use App\Models\MenuSection;
use Illuminate\Database\Seeder;

class TestMenuSeeder extends Seeder
{
    public function run(): void
    {
        $brand = \App\Models\Brand::where('name', 'like', '%Grand Lavash%')->first()
            ?? \App\Models\Brand::first();

        if (!$brand) {
            $this->command->warn('No brands found. Skipping TestMenuSeeder.');
            return;
        }

        $sections = [
            [
                'ru' => 'Салаты',
                'uz' => 'Salatlar',
                'kk' => 'Salatlar',
                'en' => 'Salads',
                'sort_order' => 1
            ],
            [
                'ru' => 'Супы',
                'uz' => 'Sho\'rvalar',
                'kk' => 'Sorpalar',
                'en' => 'Soups',
                'sort_order' => 2
            ],
            [
                'ru' => 'Основные блюда',
                'uz' => 'Asosiy taomlar',
                'kk' => 'Tiykarǵı awqatlar',
                'en' => 'Main Dishes',
                'sort_order' => 3
            ],
            [
                'ru' => 'Напитки',
                'uz' => 'Ichimliklar',
                'kk' => 'Ishimlikler',
                'en' => 'Beverages',
                'sort_order' => 4
            ],
            [
                'ru' => 'Десерты',
                'uz' => 'Shirinliklar',
                'kk' => 'Shirinlikler',
                'en' => 'Desserts',
                'sort_order' => 5
            ],
        ];

        foreach ($sections as $sectionData) {
            $section = MenuSection::create([
                'brand_id' => $brand->id,
                'sort_order' => $sectionData['sort_order']
            ]);

            $languages = ['ru', 'uz', 'kk', 'en'];

            foreach ($languages as $lang) {
                $section->translations()->create([
                    'lang_code' => $lang,
                    'name' => $sectionData[$lang]
                ]);
            }
        }

        $this->command->info("Test menu sections created successfully for {$brand->name}!");
    }
}
