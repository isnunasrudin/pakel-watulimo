<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->sentence(4),
            'slug' => fake()->unique()->slug(3),
            'excerpt' => fake()->sentence(20),
            'content' => '<p>'.fake()->paragraph().'</p>',
            'meta_title' => null,
            'meta_description' => null,
            'is_published' => true,
        ];
    }
}
