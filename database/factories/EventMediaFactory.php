<?php

namespace Database\Factories;

use App\Enums\Events\EventMediaType;
use App\Models\Event;
use App\Models\EventMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<EventMedia> */
class EventMediaFactory extends Factory
{
    protected $model = EventMedia::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'type'     => EventMediaType::IMAGE,
            'url'      => 'events/media/' . fake()->uuid() . '.jpg',
        ];
    }

    public function image(?string $url = null): static
    {
        return $this->state(fn () => [
            'type' => EventMediaType::IMAGE,
            'url'  => $url ?? 'events/media/' . fake()->uuid() . '.jpg',
        ]);
    }

    public function video(?string $url = null): static
    {
        return $this->state(fn () => [
            'type' => EventMediaType::VIDEO,
            'url'  => $url ?? 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);
    }

    public function pdf(?string $url = null): static
    {
        return $this->state(fn () => [
            'type' => EventMediaType::PDF,
            'url'  => $url ?? 'events/media/' . fake()->uuid() . '.pdf',
        ]);
    }
}
