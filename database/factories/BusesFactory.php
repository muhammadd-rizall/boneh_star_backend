<?php

namespace Database\Factories;

use App\Models\BusCategory;
use App\Models\Buses;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Buses>
 */
class BusesFactory extends Factory
{
    protected $model = Buses::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bus_category_id' => BusCategory::factory(),
            'plat_number' => $this->faker->unique()->regexify('[A-Z]{1,2} [0-9]{1,4} [A-Z]{1,3}'),
            'name' => $this->faker->words(2, true),
            'seat_count' => $this->faker->numberBetween(20, 60),
            'brand' => $this->faker->randomElement(['Mercedes-Benz', 'Hino', 'Scania', 'Volvo']),
            'status' => 'available',
            'notes' => $this->faker->sentence(),
        ];
    }
}
