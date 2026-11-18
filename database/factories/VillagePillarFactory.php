<?php

namespace Database\Factories;

use App\Models\VillagePillar;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VillagePillar>
 */
class VillagePillarFactory extends Factory
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
            'background_color' => '#f3f4f6',
            'phone' => null,
            'order' => 0,
        ];
    }
}
