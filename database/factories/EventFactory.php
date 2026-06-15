<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EventStatus;
use App\Enums\EventType;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'created_by'  => User::factory(),
            'title'       => $title,
            'slug'        => Str::slug($title) . '-' . fake()->unique()->numberBetween(1, 1000000),
            'description' => fake()->paragraph(),
            'type'        => EventType::Meetup,
            'starts_at'   => now()->addWeek(),
            'ends_at'     => now()->addWeek()->addHours(2),
            'status'      => EventStatus::Draft,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => EventStatus::Published,
        ]);
    }

    public function past(): static
    {
        return $this->state(fn (array $attributes) => [
            'starts_at' => now()->subWeek(),
            'ends_at'   => now()->subWeek()->addHours(2),
            'status'    => EventStatus::Completed,
        ]);
    }
}
