<?php

namespace Database\Factories;

use App\Models\Agenda;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Agenda>
 */
class AgendaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(4);
        $startsAt = fake()->dateTimeBetween('-1 month', '+1 month');
        $endsAt = fake()->optional()->dateTimeBetween($startsAt, '+1 month');

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(1, 999999),
            'description' => fake()->sentence(),
            'location' => fake()->randomElement(['Balai Desa Pakel', 'Aula Desa Pakel', 'Lapangan Desa Pakel']),
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'is_published' => true,
        ];
    }
}
