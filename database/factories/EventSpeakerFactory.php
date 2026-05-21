<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventSpeaker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventSpeaker>
 */
class EventSpeakerFactory extends Factory
{
    protected $model = EventSpeaker::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory()->published()->upcoming(),
            'name'     => fake()->name(),
            'bio'      => fake()->paragraph(),
            'avatar'   => fake()->imageUrl(200, 200, 'people'),
            'linkedin' => 'https://linkedin.com/in/' . fake()->userName(),
            'github'   => 'https://github.com/' . fake()->userName(),
        ];
    }
}
