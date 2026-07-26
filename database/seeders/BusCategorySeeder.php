<?php

namespace Database\Seeders;

use App\Models\BusCategory;
use Illuminate\Database\Seeder;

class BusCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Micro Bus', 
                'min_capacity' => 12, 
                'max_capacity' => 19,
                'price_inside_min' => null,
                'price_inside_max' => null,
                'price_outside' => null,
            ],
            [
                'name' => 'Medium Bus', 
                'min_capacity' => 25, 
                'max_capacity' => 35,
                'price_inside_min' => 2000000,
                'price_inside_max' => 2800000,
                'price_outside' => 3000000,
            ],
            [
                'name' => 'Big Bus', 
                'min_capacity' => 45, 
                'max_capacity' => 59,
                'price_inside_min' => 3000000,
                'price_inside_max' => 4800000,
                'price_outside' => 4000000,
            ],
            [
                'name' => 'Luxury Bus', 
                'min_capacity' => 20, 
                'max_capacity' => 30,
                'price_inside_min' => null,
                'price_inside_max' => null,
                'price_outside' => null,
            ],
        ];

        foreach ($categories as $category) {
            BusCategory::updateOrCreate(['name' => $category['name']], $category);
        }
    }
}
