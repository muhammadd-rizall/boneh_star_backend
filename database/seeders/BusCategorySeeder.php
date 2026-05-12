<?php

namespace Database\Seeders;

use App\Models\BusCategory;
use Illuminate\Database\Seeder;

class BusCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Micro Bus', 'min_capacity' => 12, 'max_capacity' => 19],
            ['name' => 'Medium Bus', 'min_capacity' => 25, 'max_capacity' => 35],
            ['name' => 'Big Bus', 'min_capacity' => 45, 'max_capacity' => 59],
            ['name' => 'Luxury Bus', 'min_capacity' => 20, 'max_capacity' => 30],
        ];

        foreach ($categories as $category) {
            BusCategory::updateOrCreate(['name' => $category['name']], $category);
        }
    }
}
