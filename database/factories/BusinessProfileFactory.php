<?php

namespace Database\Factories;

use App\Models\BusinessProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

class BusinessProfileFactory extends Factory
{
    protected $model = BusinessProfile::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'address' => fake()->address(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->companyEmail(),
            'website' => fake()->url(),
            'logo' => null,
            'facebook_url' => fake()->url(),
            'youtube_url' => fake()->url(),
            'instagram_url' => fake()->url(),
            'whatsapp_number' => fake()->phoneNumber(),
        ];
    }
}
