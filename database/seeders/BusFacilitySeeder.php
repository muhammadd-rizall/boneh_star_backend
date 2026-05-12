<?php

namespace Database\Seeders;

use App\Models\BusFacilities;
use Illuminate\Database\Seeder;

class BusFacilitySeeder extends Seeder
{
    public function run(): void
    {
        $facilities = ['AC', 'TV', 'Karaoke', 'Reclining Seat', 'USB Charger', 'Toilet', 'Smoking Area', 'Wi-Fi'];

        foreach ($facilities as $facility) {
            BusFacilities::updateOrCreate(['name' => $facility]);
        }
    }
}
