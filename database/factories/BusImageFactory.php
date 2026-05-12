<?php

namespace Database\Factories;

use App\Models\BusImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BusImage>
 */
class BusImageFactory extends Factory
{
    protected $model = BusImage::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'image_path' => $this->faker->imageUrl(800, 600, 'bus', true, 'transport'),
            'caption' => $this->faker->sentence(),
            'is_primary' => false,
            'sort_order' => 1,
        ];
    }
}
