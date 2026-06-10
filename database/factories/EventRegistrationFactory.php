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
            'event_id'       => Event::factory()->published()->upcoming(),
            'user_id'        => User::factory(),
            'status'         => EventRegistrationStatus::CONFIRMED,
            'reminder_types' => [],
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

    public function withReminders(): static
    {
        return $this->state(fn () => ['reminder_types' => ['J-7', 'J-1', 'H-1']]);
    }

    /** @param list<string> $types */
    public function withReminderTypes(array $types): static
    {
        return $this->state(fn () => ['reminder_types' => $types]);
    }
}
