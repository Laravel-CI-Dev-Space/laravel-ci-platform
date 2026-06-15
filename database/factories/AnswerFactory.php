<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Answer;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Answer>
 */
class AnswerFactory extends Factory
{
    protected $model = Answer::class;

    public function definition(): array
    {
        $body = fake()->paragraphs(2, true);

        return [
            'question_id' => Question::factory(),
            'user_id'     => User::factory(),
            'body'        => $body,
            'body_html'   => "<p>{$body}</p>",
            'is_accepted' => false,
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_accepted' => true,
        ]);
    }
}
