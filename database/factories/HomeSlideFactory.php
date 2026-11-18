<?php

namespace Database\Factories;

use App\Models\HomeSlide;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HomeSlide>
 */
class HomeSlideFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'image' => 'https://picsum.photos/seed/'.fake()->slug(2).'/900/420',
            'order' => fake()->numberBetween(1, 10),
            'is_active' => true,
        ];
    }
}
