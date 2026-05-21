<?php

namespace App\Services\Events;

use App\Enums\Events\EventRegistrationStatus;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventWaitlist;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EventService
{
    public function __construct(
        private readonly NotificationService $notifications,
    ) {}

    /**
     * @return array{status: 'registered'|'waitlist', position: int|null}
     */
    public function register(Event $event, User $user): array
    {
        if ($event->registrationFor($user) !== null) {
            throw ValidationException::withMessages([
                'event' => 'Vous êtes déjà inscrit à cet événement.',
            ]);
        }

        if ($event->waitlistEntryFor($user) !== null) {
            throw ValidationException::withMessages([
                'event' => 'Vous êtes déjà sur la liste d\'attente.',
            ]);
        }

        return DB::transaction(function () use ($event, $user) {
            $event->refresh();

            if ($event->isFull()) {
                $position = (int) $event->waitlists()->max('position') + 1;

                EventWaitlist::create([
                    'event_id' => $event->id,
                    'user_id'  => $user->id,
                    'position' => $position,
                ]);

                return ['status' => 'waitlist', 'position' => $position];
            }

            EventRegistration::create([
                'event_id' => $event->id,
                'user_id'  => $user->id,
                'status'   => EventRegistrationStatus::CONFIRMED,
            ]);

            $this->notifications->sendEventConfirmation($user, $event);

            return ['status' => 'registered', 'position' => null];
        });
    }
}
