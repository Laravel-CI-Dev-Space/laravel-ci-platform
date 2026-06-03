<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\Forum\QuestionStatus;
use App\Models\Question;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Question>
 */
class QuestionFactory extends Factory
{
    protected $model = Question::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(random_int(6, 12));

        return [
            'user_id'     => User::factory(),
            'title'       => $title,
            'slug'        => Question::generateSlug($title),
            'body'        => $this->faker->paragraphs(3, true),
            'status'      => QuestionStatus::Published,
            'is_pinned'   => false,
            'views_count' => $this->faker->numberBetween(0, 300),
        ];
    }

    public function pinned(): static
    {
        return $this->state(fn (): array => ['is_pinned' => true]);
    }

    public function closed(): static
    {
        return $this->state(fn (): array => ['status' => QuestionStatus::Closed]);
    }

    public function hidden(): static
    {
        return $this->state(fn (): array => ['status' => QuestionStatus::Hidden]);
    }

    public function popular(): static
    {
        return $this->state(fn (): array => [
            'views_count' => $this->faker->numberBetween(500, 5000),
            'votes_score' => $this->faker->numberBetween(10, 200),
        ]);
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (): array => ['user_id' => $user->id]);
    }
}
