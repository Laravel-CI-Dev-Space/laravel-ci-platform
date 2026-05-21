<?php

namespace Database\Factories;

use App\Enums\Events\EventStatus;
use App\Models\Event;
use App\Models\EventType;
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
        $start = fake()->dateTimeBetween('+1 week', '+2 months');
        $end   = (clone $start)->modify('+' . fake()->numberBetween(2, 6) . ' hours');

        $title = fake()->sentence(4);

        return [
            'title'        => $title,
            'slug'         => Str::slug($title) . '-' . fake()->unique()->numerify('####'),
            'description'  => fake()->paragraphs(3, true),
            'type_id'      => EventType::factory(),
            'location'     => fake()->city() . ', Côte d\'Ivoire',
            'meeting_link' => null,
            'start_date'   => $start,
            'end_date'     => $end,
            'capacity'     => fake()->numberBetween(20, 150),
            'status'       => EventStatus::DRAFT,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => EventStatus::PUBLISHED]);
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => EventStatus::DRAFT]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => ['status' => EventStatus::CANCELLED]);
    }

    public function upcoming(): static
    {
        return $this->published()->state(function () {
            $start = fake()->dateTimeBetween('+3 days', '+2 months');
            $end   = (clone $start)->modify('+3 hours');

            return [
                'start_date' => $start,
                'end_date'   => $end,
            ];
        });
    }

    public function past(): static
    {
        return $this->published()->state(function () {
            $start = fake()->dateTimeBetween('-3 months', '-1 week');
            $end   = (clone $start)->modify('+3 hours');

            return [
                'start_date' => $start,
                'end_date'   => $end,
            ];
        });
    }

    public function webinar(): static
    {
        return $this->state(fn () => [
            'type_id'      => EventType::factory()->webinar(),
            'location'     => null,
            'meeting_link' => 'https://meet.google.com/' . fake()->bothify('???-????-???'),
        ]);
    }

    public function inPerson(): static
    {
        return $this->state(fn () => [
            'type_id'      => EventType::factory()->meetup(),
            'location'     => fake()->city() . ', Côte d\'Ivoire',
            'meeting_link' => null,
        ]);
    }

    public function full(int $capacity = 1): static
    {
        return $this->state(fn () => ['capacity' => $capacity]);
    }
}
