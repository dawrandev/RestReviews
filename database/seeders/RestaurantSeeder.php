<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Restaurant;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Restaurant::create([
            'name' => 'Osh Markazi "Buxoro"',
            'phone' => 998901234567,
            'description' => 'An\'anaviy o\'zbek taomlari, ayniqsa, Buxoro uslubidagi plov bilan mashhur. Ko\'k va zaytun ranglarida bezatilgan ichki makon.',
            'address' => 'Toshkent sh., O\'zbekiston ko\'chasi, 15-uy',
            'longitude' => '69.279702',
            'latitude' => '41.311151',
            'is_active' => true,
            'qr_code' => 'buxoro_osh_qr.png',
        ]);

        Restaurant::create([
            'name' => 'City Cafe',
            'phone' => 998939876543,
            'description' => 'Zamonaviy kafe, yevropa va o\'zbek taomlarining aralashmasi. Qulay muhit va tez xizmat.',
            'address' => 'Toshkent sh., Amir Temur xiyoboni, 5-uy',
            'longitude' => '69.240156',
            'latitude' => '41.299485',
            'is_active' => true,
            'qr_code' => 'city_cafe_qr.png',
        ]);

        Restaurant::create([
            'name' => 'Samarqand Lag\'moni',
            'phone' => null,
            'description' => 'Turli xil lag\'monlar va tez tayyorlanadigan milliy taomlar. Registon yaqinida joylashgan.',
            'address' => 'Samarqand sh., Registon ko\'chasi, 2-uy',
            'longitude' => '66.974963',
            'latitude' => '39.654166',
            'is_active' => true,
            'qr_code' => null,
        ]);

        Restaurant::create([
            'name' => 'Farg\'ona Taomlari',
            'phone' => 998945551122,
            'description' => 'Farg\'ona vodiysining o\'ziga xos taomlari. Hozirda ta\'mirlash ishlari olib borilmoqda.',
            'address' => 'Farg\'ona sh., Mustaqillik ko\'chasi, 101-uy',
            'longitude' => '71.784823',
            'latitude' => '40.382786',
            'is_active' => false,
            'qr_code' => 'fargona_taomlari_qr.png',
        ]);
    }
}
