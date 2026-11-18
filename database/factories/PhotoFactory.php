<?php

namespace Database\Factories;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Photo>
 */
class PhotoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'album_id' => Album::factory(),
            'path' => 'gallery/photos/'.Str::uuid().'.jpg',
            'caption' => fake()->sentence(5),
            'taken_at' => fake()->dateTimeBetween('-2 years'),
            'order' => 0,
        ];
    }
}
