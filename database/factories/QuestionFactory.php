<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\QuestionStatus;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    protected $model = Question::class;

    public function definition(): array
    {
        $title = fake()->sentence(8);
        $body  = fake()->paragraphs(3, true);

        return [
            'user_id'          => User::factory(),
            'title'            => $title,
            'slug'             => Str::slug($title) . '-' . fake()->unique()->numberBetween(1, 1000000),
            'body'             => $body,
            'body_html'        => "<p>{$body}</p>",
            'status'           => QuestionStatus::Published,
            'last_activity_at' => now(),
        ];
    }

    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => QuestionStatus::Hidden,
        ]);
    }
}
