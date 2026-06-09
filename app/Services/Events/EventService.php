<?php

namespace App\Services\Events;

use App\Enums\Events\EventRegistrationStatus;
use App\Enums\Events\EventReminderType;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventReminder;
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
     * @param  list<string>  $reminderTypes
     * @return array{status: 'registered'|'waitlist', position: int|null}
     */
    public function register(Event $event, User $user, array $reminderTypes = []): array
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

        $reminderTypes = EventRegistration::sanitizeReminderTypes($reminderTypes);

        return DB::transaction(function () use ($event, $user, $reminderTypes) {
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

            $cancelled = $event->registrations()
                ->where('user_id', $user->id)
                ->where('status', EventRegistrationStatus::CANCELLED)
                ->first();

            if ($cancelled !== null) {
                $cancelled->update([
                    'status'         => EventRegistrationStatus::CONFIRMED,
                    'reminder_types' => $reminderTypes,
                ]);
            } else {
                EventRegistration::create([
                    'event_id'       => $event->id,
                    'user_id'        => $user->id,
                    'status'         => EventRegistrationStatus::CONFIRMED,
                    'reminder_types' => $reminderTypes,
                ]);
            }

            if ($reminderTypes !== []) {
                $this->scheduleRemindersForTypes($event, $reminderTypes);
            }

            $this->notifications->sendEventConfirmation($user, $event);

            return ['status' => 'registered', 'position' => null];
        });
    }

    public function cancelRegistration(Event $event, User $user): void
    {
        $registration = $event->registrationFor($user);

        if ($registration === null || $registration->status === EventRegistrationStatus::CANCELLED) {
            throw ValidationException::withMessages([
                'event' => 'Aucune inscription active trouvée pour cet événement.',
            ]);
        }

        if (! $event->isUpcoming()) {
            throw ValidationException::withMessages([
                'event' => 'Impossible d\'annuler une inscription à un événement passé.',
            ]);
        }

        DB::transaction(function () use ($event, $registration) {
            $registration->update(['status' => EventRegistrationStatus::CANCELLED]);
            $this->promoteNextFromWaitlist($event);
        });

        $this->notifications->sendEventCancellation($user, $event);
    }

    public function leaveWaitlist(Event $event, User $user): void
    {
        $waitlist = $event->waitlistEntryFor($user);

        if ($waitlist === null) {
            throw ValidationException::withMessages([
                'event' => 'Vous n\'êtes pas sur la liste d\'attente.',
            ]);
        }

        DB::transaction(function () use ($event, $waitlist) {
            $removedPosition = $waitlist->position;
            $waitlist->delete();

            $event->waitlists()
                ->where('position', '>', $removedPosition)
                ->decrement('position');
        });
    }

    /**
     * @param  list<string>  $reminderTypes
     * @return list<string>
     */
    public function updateReminderPreferences(Event $event, User $user, array $reminderTypes): array
    {
        $registration = $event->registrationFor($user);

        if ($registration === null || $registration->status !== EventRegistrationStatus::CONFIRMED) {
            throw ValidationException::withMessages([
                'event' => 'Aucune inscription active trouvée pour cet événement.',
            ]);
        }

        if (! $event->isUpcoming()) {
            throw ValidationException::withMessages([
                'event' => 'Les rappels ne sont disponibles que pour les événements à venir.',
            ]);
        }

        $reminderTypes = EventRegistration::sanitizeReminderTypes($reminderTypes);

        $registration->update(['reminder_types' => $reminderTypes]);

        if ($reminderTypes !== []) {
            $this->scheduleRemindersForTypes($event, $reminderTypes);
        }

        return $reminderTypes;
    }

    /** Planifie tous les créneaux de relance pour l'événement (idempotent). */
    public function scheduleReminders(Event $event): void
    {
        $this->scheduleRemindersForTypes(
            $event,
            array_column(EventReminderType::cases(), 'value'),
        );
    }

    /**
     * @param  list<string>  $types
     */
    public function scheduleRemindersForTypes(Event $event, array $types): void
    {
        $types = EventRegistration::sanitizeReminderTypes($types);

        foreach (EventReminderType::cases() as $type) {
            if (! in_array($type->value, $types, true)) {
                continue;
            }

            $scheduledAt = match ($type) {
                EventReminderType::J_7 => $event->start_date->copy()->subDays(7),
                EventReminderType::J_1 => $event->start_date->copy()->subDay(),
                EventReminderType::H_1 => $event->start_date->copy()->subHour(),
            };

            if ($scheduledAt->isPast()) {
                continue;
            }

            EventReminder::firstOrCreate(
                ['event_id' => $event->id, 'type' => $type->value],
                ['scheduled_at' => $scheduledAt],
            );
        }
    }

    private function promoteNextFromWaitlist(Event $event): void
    {
        if ($event->isFull()) {
            return;
        }

        $next = $event->waitlists()->orderBy('position')->first();

        if ($next === null) {
            return;
        }

        EventRegistration::create([
            'event_id'       => $event->id,
            'user_id'        => $next->user_id,
            'status'         => EventRegistrationStatus::CONFIRMED,
            'reminder_types' => [],
        ]);

        $next->delete();

        $event->waitlists()
            ->where('position', '>', 1)
            ->decrement('position');
    }
}
