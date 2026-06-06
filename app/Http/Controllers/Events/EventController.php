<?php

declare(strict_types=1);

namespace App\Http\Controllers\Events;

use App\Http\Controllers\Controller;
use App\Services\Events\EventService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function __construct(private readonly EventService $eventService) {}

    /**
     * Liste paginée des événements publiés avec filtres type et période.
     */
    public function index(Request $request): View
    {
        $type   = $request->get('type', 'all');
        $period = $request->get('period', 'upcoming');
        $sort   = $request->get('sort', 'soonest');

        $events = $this->eventService->getEvents(
            type: $type,
            period: $period,
            sort: $sort,
        );

        return view('web.events.index', compact('events', 'type', 'period', 'sort'));
    }

    /**
     * Affiche le détail d'un événement publié par son slug.
     */
    public function show(string $slug): View
    {
        return view('web.events.show', compact('slug'));
    }
}
