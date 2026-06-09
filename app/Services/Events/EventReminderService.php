<?php

namespace App\Services\Events;

use App\Enums\Events\EventRegistrationStatus;
use App\Models\EventReminder;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

/**
 * Sends due event reminder emails (Sprint Roger — M4).
 *
 * Reads event_reminders (when to send) and filters registrants by their
 * reminder_types opt-in (who wants that specific slot).
 */
class EventReminderService
{
    public function __construct(
        private readonly NotificationService $notifications,
    ) {}

    /** Dispatch emails for reminder slots whose scheduled_at has passed. */
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
                // Mark as sent so we do not retry for cancelled/past events.
                $reminder->update(['sent_at' => now()]);

                continue;
            }

            DB::transaction(function () use ($reminder, $event, &$sentCount) {
                $registrants = $event->registrations()
                    ->where('status', EventRegistrationStatus::CONFIRMED)
                    ->with('user')
                    ->get()
                    // Per-user opt-in: reminder_types must include this slot (e.g. 'J-1').
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
