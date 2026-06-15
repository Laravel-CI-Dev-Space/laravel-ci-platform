<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ArticleLevel;
use App\Enums\ArticleStatus;
use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Article>
 */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        $title = fake()->sentence(6);
        $body  = fake()->paragraphs(5, true);

        return [
            'user_id'   => User::factory(),
            'title'     => $title,
            'slug'      => Str::slug($title) . '-' . fake()->unique()->numberBetween(1, 1000000),
            'excerpt'   => fake()->paragraph(),
            'body'      => $body,
            'body_html' => "<p>{$body}</p>",
            'level'     => ArticleLevel::Beginner,
            'status'    => ArticleStatus::Draft,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'       => ArticleStatus::Published,
            'published_at' => now(),
        ]);
    }
}
