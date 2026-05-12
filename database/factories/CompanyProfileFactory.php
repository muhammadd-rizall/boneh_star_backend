<?php

namespace Database\Factories;

use App\Models\CompanyProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompanyProfile>
 */
class CompanyProfileFactory extends Factory
{
    protected $model = CompanyProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_name' => 'Boneh Star Utama',
            'logo' => $this->faker->imageUrl(200, 200, 'business', true),
            'address' => 'Jl. Kebon Jeruk No. 123, Jakarta Barat',
            'whatsapp' => [
                ['label' => 'CS 1', 'number' => '08123456789'],
                ['label' => 'CS 2', 'number' => '08987654321'],
            ],
            'social_media' => [
                ['platform' => 'Instagram', 'url' => 'https://instagram.com/bonehstar'],
                ['platform' => 'Facebook', 'url' => 'https://facebook.com/bonehstar'],
            ],
            'email' => 'info@bonehstar.com',
            'description' => 'Perusahaan otobus terpercaya di Indonesia.',
        ];
    }
}
