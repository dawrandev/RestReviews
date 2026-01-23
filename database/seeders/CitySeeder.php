<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use App\Models\Language;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nukus = City::create();

        $languages = Language::all();

        $translations = [
            'uz' => 'Nukus',
            'ru' => 'Нукус',
            'en' => 'Nukus',
            'kk' => 'Nókis',
        ];

        foreach ($languages as $language) {
            if (isset($translations[$language->code])) {
                $nukus->translations()->create([
                    'code' => $language->code,
                    'name' => $translations[$language->code],
                ]);
            }
        }
    }
}
