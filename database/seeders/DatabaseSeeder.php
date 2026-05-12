<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            BusCategorySeeder::class,
            BusFacilitySeeder::class,
            ActivityTypeSeeder::class,
            RouteTypeSeeder::class,
            PaymentMethodSeeder::class,
            CompanyProfileSeeder::class,
            DriverSeeder::class,
            BusSeeder::class,
            CustomerSeeder::class,
        ]);
    }
}
