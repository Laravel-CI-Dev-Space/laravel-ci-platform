<?php

declare(strict_types=1);

namespace App\Livewire\Events;

use App\Models\EventRegistration;
use App\Services\Events\EventService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class EventDetail extends Component
{
    public string $eventSlug = '';

    public ?EventRegistration $registration = null;

    public function mount(string $slug, EventService $eventService): void
    {
        $this->eventSlug = $slug;

        if (Auth::check()) {
            $event              = $eventService->getBySlug($slug);
            $this->registration = $eventService->getRegistration(Auth::user(), $event);
        }
    }

    /**
     * Vérifie si l'utilisateur connecté est inscrit.
     */
    public function isRegistered(): bool
    {
        return $this->registration !== null;
    }

    /**
     * Nombre de places restantes (null si illimité).
     */
    public function spotsLeft(EventService $eventService): ?int
    {
        $event = $eventService->getBySlug($this->eventSlug);

        return $eventService->spotsLeft($event);
    }

    /**
     * Vérifie si l'événement est complet.
     */
    public function isFull(EventService $eventService): bool
    {
        $event = $eventService->getBySlug($this->eventSlug);

        return $eventService->isFull($event);
    }

    public function render(EventService $eventService): View
    {
        $event = $eventService->getBySlug($this->eventSlug);

        return view('livewire.events.event-detail', compact('event'));
    }
}
