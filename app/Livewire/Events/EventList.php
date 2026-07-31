<?php

declare(strict_types=1);

namespace App\Livewire\Events;

use App\Services\Events\EventService;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class EventList extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    #[Url]
    public string $type = 'all';

    #[Url]
    public string $period = 'upcoming';

    #[Url]
    public string $sort = 'soonest';

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function updatedPeriod(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function render(EventService $eventService): View
    {
        $events = $eventService->getEvents(
            type: $this->type,
            period: $this->period,
            sort: $this->sort,
        );

        return view('livewire.events.event-list', compact('events'));
    }
}
