<?php

namespace Database\Factories;

use App\Models\ProgramLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProgramLink>
 */
class ProgramLinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'icon' => 'fas fa-seedling',
            'url' => '/layanan',
            'order' => 0,
        ];
    }
}
