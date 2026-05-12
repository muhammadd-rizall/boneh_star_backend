<?php

namespace Database\Seeders;

use App\Models\RouteType;
use Illuminate\Database\Seeder;

class RouteTypeSeeder extends Seeder
{
    public function run(): void
    {
        $routes = [
            ['name' => 'Dalam Kota', 'scope' => 'inside'],
            ['name' => 'Luar Kota', 'scope' => 'outside'],
            ['name' => 'Antar Provinsi', 'scope' => 'outside'],
        ];

        foreach ($routes as $route) {
            RouteType::updateOrCreate(['name' => $route['name']], $route);
        }
    }
}
