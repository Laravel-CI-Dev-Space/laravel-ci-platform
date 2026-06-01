<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventType;
use App\Services\Events\EventService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function __construct(
        private readonly EventService $eventService,
    ) {}

    public function index(Request $request): View
    {
        $period   = $request->string('period', 'upcoming')->toString();
        $typeSlug = $request->string('type')->toString() ?: null;

        $query = Event::query()
            ->with(['type'])
            ->withCount([
                'registrations as confirmed_registrations_count' => fn ($q) => $q->where('status', 'confirmed'),
            ]);

        $query = match ($period) {
            'past'  => $query->past(),
            'all'   => $query->published(),
            default => $query->upcoming(),
        };

        if ($typeSlug) {
            $query->ofType($typeSlug);
        }

        $events = $query
            ->orderBy('start_date')
            ->paginate(9)
            ->withQueryString();

        $types = EventType::query()->orderBy('name')->get();

        return view('web.events.index', [
            'events' => $events,
            'types'  => $types,
            'period' => $period,
            'type'   => $typeSlug,
        ]);
    }

    public function show(Event $event): View
    {
        $this->authorize('view', $event);

        $event->load(['type', 'speakers']);
        $event->loadCount([
            'registrations as confirmed_registrations_count' => fn ($q) => $q->where('status', 'confirmed'),
        ]);

        $user         = auth()->user();
        $registration = $event->registrationFor($user);
        $waitlist     = $event->waitlistEntryFor($user);
        $canRegister  = $user?->can('register', $event) ?? false;

        return view('web.events.show', compact(
            'event',
            'registration',
            'waitlist',
            'canRegister',
        ));
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
