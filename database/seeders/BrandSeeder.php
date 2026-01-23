<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Brand::firstOrCreate(['name' => 'Osh Markazi Tarmog\'i'], ['logo' => 'osh.png'], ['description' => 'Osh Markazi Tarmog\'i']);
        // Brand::firstOrCreate(['name' => 'City Cafe Chain'], ['logo' => 'city.png'], ['description' => 'City Cafe Chain']);
    }
}
