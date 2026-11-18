<?php

namespace Database\Factories;

use App\Models\VillageOfficial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VillageOfficial>
 */
class VillageOfficialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'position' => fake()->jobTitle(),
            'photo' => null,
            'phone' => null,
            'order' => 0,
        ];
    }
}
