<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Restaurant;

class UserSeeder extends Seeder
{
    public function run()
    {
        // 1. SuperAdmin yaratish
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'login' => 'superadmin',
            'password' => Hash::make('password'),
            'restaurant_id' => null,
        ]);
        // Rolni biriktiramiz
        $superAdmin->assignRole('superadmin');

        // 2. Birinchi Restoran Admini
        $restaurant1 = Restaurant::find(1);
        if ($restaurant1) {
            $admin1 = User::create([
                'name' => 'Osh Markazi Admini',
                'login' => 'osh_admin',
                'password' => Hash::make('password'),
                'restaurant_id' => $restaurant1->id,
            ]);
            $admin1->assignRole('admin');
        }

        // 3. Ikkinchi Restoran Admini
        $restaurant2 = Restaurant::find(2);
        if ($restaurant2) {
            $admin2 = User::create([
                'name' => 'Fast Food Admini',
                'login' => 'fastfood_admin',
                'password' => Hash::make('password'),
                'restaurant_id' => $restaurant2->id,
            ]);
            $admin2->assignRole('admin');
        }
    }
}
