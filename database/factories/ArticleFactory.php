<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
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
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(5)),
            'excerpt' => fake()->sentence(12),
            'body' => collect(fake()->paragraphs(4))
                ->map(fn (string $paragraph): string => '<p>'.$paragraph.'</p>')
                ->implode("\n"),
            'cover_image' => null,
            'is_pinned' => false,
            'is_featured' => false,
            'status' => 'draft',
            'published_at' => null,
            'meta_title' => null,
            'meta_description' => null,
        ];
    }

    /**
     * Indicate that the article is published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);
    }

    /**
     * Indicate that the article is pinned on the homepage.
     */
    public function pinned(): static
    {
        return $this->state(fn (array $attributes): array => [
            'is_pinned' => true,
        ]);
    }
}
