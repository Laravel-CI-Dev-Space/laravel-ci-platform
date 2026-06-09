<?php

declare(strict_types=1);

namespace App\Livewire\Events;

use App\Livewire\Concerns\ManagesEventReminderPreferences;
use App\Models\Event;
use App\Models\EventRegistration as EventRegistrationModel;
use App\Models\EventWaitlist;
use App\Models\User;
use App\Queries\Events\EventDetailQuery;
use App\Services\Events\EventService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;

class EventRegistration extends Component
{
    use AuthorizesRequests;
    use ManagesEventReminderPreferences;

    #[Locked]
    public int $eventId;

    public ?string $flashMessage = null;

    public function mount(int $eventId): void
    {
        $this->eventId = $eventId;

        $event                   = EventDetailQuery::findById($eventId, auth()->id());
        $registration            = $event->registrationFor(auth()->user());
        $this->selectedReminders = $registration?->reminder_types ?? [];
    }

    public function register(EventService $eventService): void
    {
        $event = $this->event;
        $user  = auth()->user();

        if (! $user instanceof User) {
            $this->redirect(route('login'));

            return;
        }

        $this->authorize('register', $event);

        try {
            $result = $eventService->register($event, $user, $this->selectedReminders);

            $this->flashMessage = match ($result['status']) {
                'waitlist' => "Événement complet. Vous êtes en position #{$result['position']} sur la liste d'attente.",
                default    => 'Inscription confirmée ! Un email de confirmation vous sera envoyé.',
            };
        } catch (ValidationException $e) {
            $this->addError('register', $e->validator->errors()->first() ?? 'Inscription impossible.');
        }

        $this->resetComputed();
    }

    public function saveReminderPreferences(EventService $eventService): void
    {
        $event = $this->event;
        $user  = auth()->user();

        if (! $user instanceof User) {
            $this->redirect(route('login'));

            return;
        }

        $this->selectedReminders = EventRegistration::sanitizeReminderTypes($this->selectedReminders);

        if ($this->registration === null) {
            $this->showReminderModal = false;

            return;
        }

        $this->authorize('manageReminders', $event);

        try {
            $this->selectedReminders = $eventService->updateReminderPreferences(
                $event,
                $user,
                $this->selectedReminders,
            );

            $this->showReminderModal = false;
            $this->flashMessage      = $this->reminderSaveSuccessMessage();
        } catch (ValidationException $e) {
            $this->addError('reminders', $e->validator->errors()->first() ?? 'Action impossible.');
        }

        $this->resetComputed();
    }

    public function cancelRegistration(EventService $eventService): void
    {
        $event = $this->event;
        $user  = auth()->user();

        if (! $user instanceof User) {
            $this->redirect(route('login'));

            return;
        }

        $this->authorize('cancelRegistration', $event);

        try {
            $eventService->cancelRegistration($event, $user);
            $this->flashMessage = 'Votre inscription a été annulée.';
        } catch (ValidationException $e) {
            $this->addError('cancel', $e->validator->errors()->first() ?? 'Annulation impossible.');
        }

        $this->resetComputed();
    }

    public function leaveWaitlist(EventService $eventService): void
    {
        $event = $this->event;
        $user  = auth()->user();

        if (! $user instanceof User) {
            $this->redirect(route('login'));

            return;
        }

        $this->authorize('leaveWaitlist', $event);

        try {
            $eventService->leaveWaitlist($event, $user);
            $this->flashMessage = 'Vous avez quitté la liste d\'attente.';
        } catch (ValidationException $e) {
            $this->addError('cancel', $e->validator->errors()->first() ?? 'Action impossible.');
        }

        $this->resetComputed();
    }

    protected function currentReminderTypes(): array
    {
        return $this->registration?->reminder_types ?? [];
    }

    #[Computed]
    public function event(): Event
    {
        return EventDetailQuery::findById($this->eventId, auth()->id());
    }

    #[Computed]
    public function registration(): ?EventRegistrationModel
    {
        return $this->event->registrationFor(auth()->user());
    }

    #[Computed]
    public function waitlist(): ?EventWaitlist
    {
        return $this->event->waitlistEntryFor(auth()->user());
    }

    #[Computed]
    public function canRegister(): bool
    {
        $user = auth()->user();

        return $user !== null && $user->can('register', $this->event);
    }

    #[Computed]
    public function canCancel(): bool
    {
        $user = auth()->user();

        return $user !== null && $user->can('cancelRegistration', $this->event);
    }

    #[Computed]
    public function canLeaveWaitlist(): bool
    {
        $user = auth()->user();

        return $user !== null && $user->can('leaveWaitlist', $this->event);
    }

    #[Computed]
    public function canDownloadCalendar(): bool
    {
        $user = auth()->user();

        return $user !== null && $user->can('downloadIcs', $this->event);
    }

    #[Computed]
    public function canManageReminders(): bool
    {
        $user = auth()->user();

        return $user !== null && $user->can('manageReminders', $this->event);
    }

    private function resetComputed(): void
    {
        unset($this->event, $this->registration, $this->waitlist, $this->canRegister, $this->canCancel, $this->canLeaveWaitlist, $this->canDownloadCalendar, $this->canManageReminders);
        $this->selectedReminders = $this->currentReminderTypes();
    }

    public function render(): View
    {
        return view('livewire.events.event-registration');
    }
}
