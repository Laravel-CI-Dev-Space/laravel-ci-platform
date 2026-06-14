<?php

declare(strict_types=1);

namespace App\Livewire\Events;

use App\Models\Event;
use App\Services\Events\EventService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class RegistrationButton extends Component
{
    public int $eventId;

    public bool $isRegistered = false;

    public ?string $status = null;

    public ?int $registrationId = null;

    public ?int $spotsLeft = null;

    public bool $isFull = false;

    public bool $waitlistEnabled = false;

    public bool $isPast = false;

    public bool $guestRegistrationEnabled = false;

    public ?string $ticketNumber = null;

    public function mount(Event $event): void
    {
        $this->eventId                  = $event->id;
        $this->isFull                   = $event->isFull();
        $this->spotsLeft                = $event->spotsLeft();
        $this->waitlistEnabled          = $event->waitlist_enabled;
        $this->isPast                   = $event->isPast();
        $this->guestRegistrationEnabled = $event->guest_registration_enabled;

        if (Auth::check()) {
            /** @var EventService $eventService */
            $eventService = app(EventService::class);
            $registration = $eventService->getRegistration(Auth::user(), $event);

            if ($registration !== null) {
                $this->isRegistered   = true;
                $this->status         = $registration->status->value;
                $this->registrationId = $registration->id;
                $this->ticketNumber   = $registration->ticket_number;
            }
        }
    }

    public function render(): View
    {
        return view('livewire.events.registration-button');
    }
}
