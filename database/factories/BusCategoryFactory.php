<?php

namespace Database\Factories;

use App\Models\BusCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BusCategory>
 */
class BusCategoryFactory extends Factory
{
    protected $model = BusCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['Micro Bus', 'Medium Bus', 'Big Bus', 'Luxury Bus']),
            'min_capacity' => $this->faker->numberBetween(10, 20),
            'max_capacity' => $this->faker->numberBetween(30, 60),
            'description' => $this->faker->sentence(),
        ];
    }
}
