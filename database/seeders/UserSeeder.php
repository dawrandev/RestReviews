<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Restaurant;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // $brandOsh = Brand::firstOrCreate(
        //     ['name' => 'Osh Markazi Tarmog\'i'],
        //     ['logo' => 'osh.png', 'description' => 'Osh Markazi Tarmog\'i']
        // );

        // $brandCity = Brand::firstOrCreate(
        //     ['name' => 'City Cafe Chain'],
        //     ['logo' => 'city.png', 'description' => 'City Cafe Chain']
        // );

        $superAdminUser = User::create([
            'brand_id' => null,
            'name'     => 'Asosiy Boshqaruvchi',
            'login'    => 'superadmin',
            'password' => Hash::make('password'),
        ]);
        $superAdminUser->assignRole('superadmin');

        // $adminOsh = User::create([
        //     'brand_id' => $brandOsh->id,
        //     'name'     => 'Osh Markazi Admini',
        //     'login'    => 'osh_admin',
        //     'password' => Hash::make('password'),
        // ]);
        // $adminOsh->assignRole('admin');

        // $adminCity = User::create([
        //     'brand_id' => $brandCity->id,
        //     'name'     => 'City Cafe Admini',
        //     'login'    => 'city_admin',
        //     'password' => Hash::make('password'),
        // ]);
        // $adminCity->assignRole('admin');
    }
}
