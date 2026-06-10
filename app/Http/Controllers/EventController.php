<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Queries\Events\EventDetailQuery;
use App\Services\Events\EventIcsService;
use App\Services\Events\EventService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** HTTP layer for public event pages (Sprint Roger — M4). Delegates to EventService. */
class EventController extends Controller
{
    public function __construct(
        private readonly EventService $eventService,
        private readonly EventIcsService $eventIcsService,
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

    public function register(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('register', $event);

        $result = $this->eventService->register(
            $event,
            auth()->user(),
            EventRegistration::sanitizeReminderTypes($request->input('reminder_types', [])),
        );

        $message = match ($result['status']) {
            'waitlist' => "Événement complet. Vous êtes en position #{$result['position']} sur la liste d'attente.",
            default    => 'Inscription confirmée ! Un email de confirmation vous sera envoyé.',
        };

        return redirect()
            ->route('events.show', $event)
            ->with('success', $message);
    }

    public function cancel(Event $event): RedirectResponse
    {
        $this->authorize('cancelRegistration', $event);

        $this->eventService->cancelRegistration($event, auth()->user());

        // Supports cancel from dashboard (modal) or event detail page.
        return redirect()
            ->back(fallback: route('events.show', $event))
            ->with('success', 'Votre inscription a été annulée.');
    }

    public function calendar(Event $event): StreamedResponse
    {
        $this->authorize('downloadIcs', $event);

        return $this->eventIcsService->downloadResponse($event, auth()->user());
    }
}
