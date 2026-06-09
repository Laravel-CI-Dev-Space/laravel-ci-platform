<?php

namespace App\Services\Events;

use App\Enums\Events\EventRegistrationStatus;
use App\Models\EventReminder;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class EventReminderService
{
    public function __construct(
        private readonly NotificationService $notifications,
    ) {}

    /** Envoie les relances J-7, J-1 et H-1 dont l'heure est échue. */
    public function sendDueReminders(): int
    {
        $reminders = EventReminder::query()
            ->whereNull('sent_at')
            ->where('scheduled_at', '<=', now())
            ->with(['event.registrations.user'])
            ->get();

        $sentCount = 0;

        foreach ($reminders as $reminder) {
            $event = $reminder->event;

            if ($event === null || ! $event->isUpcoming()) {
                $reminder->update(['sent_at' => now()]);

                continue;
            }

            DB::transaction(function () use ($reminder, $event, &$sentCount) {
                $registrants = $event->registrations()
                    ->where('status', EventRegistrationStatus::CONFIRMED)
                    ->with('user')
                    ->get()
                    ->filter(fn ($registration) => $registration->hasReminderType($reminder->type));

                foreach ($registrants as $registration) {
                    if ($registration->user === null) {
                        continue;
                    }

                    $this->notifications->sendEventReminder(
                        $registration->user,
                        $event,
                        $reminder->type,
                    );
                    $sentCount++;
                }

                $reminder->update(['sent_at' => now()]);
            });
        }

        return $sentCount;
    }
}
