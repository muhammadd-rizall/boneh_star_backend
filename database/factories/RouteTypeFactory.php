<?php

namespace Database\Factories;

use App\Models\RouteType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RouteType>
 */
class RouteTypeFactory extends Factory
{
    protected $model = RouteType::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Dalam Kota', 'Luar Kota', 'Antar Provinsi']),
            'scope' => $this->faker->randomElement(['inside', 'outside']),
            'description' => $this->faker->sentence(),
        ];
    }
}
