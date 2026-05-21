<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventWaitlist;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventWaitlist>
 */
class EventWaitlistFactory extends Factory
{
    protected $model = EventWaitlist::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory()->published()->upcoming()->full(),
            'user_id'  => User::factory(),
            'position' => fake()->numberBetween(1, 20),
        ];
    }
}
