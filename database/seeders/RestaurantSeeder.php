<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Brand;
use App\Models\City;
use Illuminate\Support\Facades\DB;

class RestaurantSeeder extends Seeder
{
    public function run()
    {
        //     $oshAdmin = User::where('login', 'osh_admin')->first();
        //     $cityAdmin = User::where('login', 'city_admin')->first();

        //     $oshBrand = Brand::where('name', 'Osh Markazi Tarmog\'i')->first();
        //     $cityBrand = Brand::where('name', 'City Cafe Chain')->first();

        //     // Nukus shahrini translations orqali topish
        //     $nukus = City::whereHas('translations', function ($query) {
        //         $query->where('name', 'Nukus');
        //     })->first();

        //     if ($oshAdmin && $oshBrand && $nukus) {
        //         Restaurant::create([
        //             'user_id'     => $oshAdmin->id,
        //             'brand_id'    => $oshBrand->id,
        //             'city_id'     => $nukus->id,
        //             'branch_name' => 'Nukus Markaziy Filiali',
        //             'phone'       => '998612234567',
        //             'description' => 'Nukusdagi eng mazali Buxoro oshi',
        //             'address'     => 'Nukus sh., Doslik ko\'chasi, 25-uy',
        //             'location'    => DB::raw("ST_GeomFromText('POINT(59.6112 42.4653)')"), // Nukus koordinatalari
        //             'is_active'   => true,
        //         ]);
        //     }

        //     if ($cityAdmin && $cityBrand && $nukus) {
        //         Restaurant::create([
        //             'user_id'     => $cityAdmin->id,
        //             'brand_id'    => $cityBrand->id,
        //             'city_id'     => $nukus->id,
        //             'branch_name' => 'Nukus City Cafe',
        //             'phone'       => '998612876543',
        //             'description' => 'Nukus markazidagi zamonaviy kafe',
        //             'address'     => 'Nukus sh., Amir Temur ko\'chasi, 10-uy',
        //             'location'    => DB::raw("ST_GeomFromText('POINT(59.6167 42.4611)')"), // Nukus markaziy koordinatalari
        //             'is_active'   => true,
        //         ]);
        //     }
    }
}
