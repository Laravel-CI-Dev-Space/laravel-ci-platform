<?php

declare(strict_types=1);

namespace App\Jobs\Events;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEventReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly Event $event,
        private readonly string $reminderType,
    ) {}

    /**
     * Envoie le rappel à tous les inscrits confirmés et met à jour le flag sur l'événement.
     */
    public function handle(NotificationService $notificationService): void
    {
        $registrations = EventRegistration::where('event_id', $this->event->id)
            ->where('status', 'confirmed')
            ->with('user')
            ->get();

        foreach ($registrations as $registration) {
            $notificationService->sendEventReminder(
                user: $registration->user,
                registration: $registration,
                type: $this->reminderType,
            );

            $registration->update(['reminder_sent' => true]);
        }

        $flag = match ($this->reminderType) {
            '7d'    => 'reminder_7d_sent',
            '1d'    => 'reminder_1d_sent',
            default => null,
        };

        if ($flag !== null) {
            $this->event->update([$flag => true]);
        }
    }
}
