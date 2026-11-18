<?php

namespace Database\Factories;

use App\Models\VillageStatistic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VillageStatistic>
 */
class VillageStatisticFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => fake()->words(2, true),
            'value' => (string) fake()->numberBetween(1, 5000),
            'unit' => null,
            'icon' => 'fas fa-chart-bar',
            'group' => 'demografi',
            'order' => 0,
        ];
    }
}
