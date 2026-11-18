<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\ArticleComment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ArticleComment>
 */
class ArticleCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'article_id' => Article::factory(),
            'name' => fake()->name(),
            'email' => fake()->safeEmail(),
            'body' => fake()->paragraph(),
            'status' => ArticleComment::STATUS_PENDING,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }

    /**
     * Indicate that the comment has been approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ArticleComment::STATUS_APPROVED,
        ]);
    }

    /**
     * Indicate that the comment has been rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => ArticleComment::STATUS_REJECTED,
        ]);
    }
}
