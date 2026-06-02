<?php

declare(strict_types=1);

namespace App\Livewire\Events;

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

    #[Locked]
    public int $eventId;

    public ?string $flashMessage = null;

    public function mount(int $eventId): void
    {
        $this->eventId = $eventId;
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
            $result = $eventService->register($event, $user);

            $this->flashMessage = match ($result['status']) {
                'waitlist' => "Événement complet. Vous êtes en position #{$result['position']} sur la liste d'attente.",
                default    => 'Inscription confirmée ! Un email de confirmation vous sera envoyé.',
            };
        } catch (ValidationException $e) {
            $this->addError('register', $e->validator->errors()->first() ?? 'Inscription impossible.');
        }

        unset($this->event);
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

    public function render(): View
    {
        return view('livewire.events.event-registration');
    }
}
