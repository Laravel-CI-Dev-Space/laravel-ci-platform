<?php

namespace Database\Factories;

use App\Models\EventType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<EventType>
 */
class EventTypeFactory extends Factory
{
    protected $model = EventType::class;

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
        ];
    }

    public function meetup(): static
    {
        return $this->state(fn () => ['name' => 'Meetup', 'slug' => 'meetup']);
    }

    public function webinar(): static
    {
        return $this->state(fn () => ['name' => 'Webinar', 'slug' => 'webinar']);
    }

    public function hackathon(): static
    {
        return $this->state(fn () => ['name' => 'Hackathon', 'slug' => 'hackathon']);
    }
}
