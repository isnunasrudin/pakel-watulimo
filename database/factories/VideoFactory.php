<?php

namespace Database\Factories;

use App\Models\Video;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Video>
 */
class VideoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(6);

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(5)),
            'video_url' => fake()->url(),
            'thumbnail' => null,
            'description' => fake()->paragraph(),
            'published_at' => null,
        ];
    }

    /**
     * Indicate that the video is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes): array => [
            'published_at' => now()->subDay(),
        ]);
    }
}
