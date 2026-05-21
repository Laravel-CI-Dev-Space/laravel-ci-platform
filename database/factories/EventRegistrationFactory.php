<?php

namespace Database\Factories;

use App\Enums\Events\EventRegistrationStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EventRegistration>
 */
class EventRegistrationFactory extends Factory
{
    protected $model = EventRegistration::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory()->published()->upcoming(),
            'user_id'  => User::factory(),
            'status'   => EventRegistrationStatus::CONFIRMED,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => EventRegistrationStatus::PENDING]);
    }

    public function confirmed(): static
    {
        return $this->state(fn () => ['status' => EventRegistrationStatus::CONFIRMED]);
    }
}
