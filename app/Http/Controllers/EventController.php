<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use App\Queries\Events\EventDetailQuery;
use App\Services\Events\EventService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EventController extends Controller
{
    public function __construct(
        private readonly EventService $eventService,
    ) {}

    public function index(): View
    {
        return view('web.events.index');
    }

    public function show(Event $event): View
    {
        $this->authorize('view', $event);

        $event = EventDetailQuery::findBySlug($event->slug, auth()->id());

        return view('web.events.show', compact('event'));
    }

    public function register(Event $event): RedirectResponse
    {
        $this->authorize('register', $event);

        $result = $this->eventService->register($event, auth()->user());

        $message = match ($result['status']) {
            'waitlist' => "Événement complet. Vous êtes en position #{$result['position']} sur la liste d'attente.",
            default    => 'Inscription confirmée ! Un email de confirmation vous sera envoyé.',
        };

        return redirect()
            ->route('events.show', $event)
            ->with('success', $message);
    }
}
