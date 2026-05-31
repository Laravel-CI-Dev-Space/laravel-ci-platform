<?php

declare(strict_types=1);

namespace Database\Factories;

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
            'user_id' => User::factory(),
            'title'   => $title,
            'slug'    => Question::generateSlug($title),
            'content' => $this->faker->paragraphs(3, true),
            'pinned'  => false,
            'closed'  => false,
            'views'   => $this->faker->numberBetween(0, 300),
        ];
    }

    public function pinned(): static
    {
        return $this->state(fn (array $_): array => ['pinned' => true]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $_): array => ['closed' => true]);
    }

    public function popular(): static
    {
        return $this->state(fn (array $_): array => [
            'views' => $this->faker->numberBetween(500, 5000),
        ]);
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (array $_): array => ['user_id' => $user->id]);
    }
}
