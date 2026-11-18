<?php

namespace Database\Factories;

use App\Models\HeaderMenuItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HeaderMenuItem>
 */
class HeaderMenuItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'parent_id' => null,
            'label' => fake()->unique()->words(2, true),
            'url' => '/'.fake()->slug(),
            'open_in_new_tab' => false,
            'order' => 0,
            'is_active' => true,
        ];
    }
}
