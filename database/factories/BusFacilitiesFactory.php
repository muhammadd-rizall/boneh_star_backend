<?php

namespace Database\Factories;

use App\Models\BusFacilities;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BusFacilities>
 */
class BusFacilitiesFactory extends Factory
{
    protected $model = BusFacilities::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->randomElement(['AC', 'TV', 'Karaoke', 'Reclining Seat', 'USB Charger', 'Toilet', 'Smoking Area', 'Wi-Fi']),
        ];
    }
}
