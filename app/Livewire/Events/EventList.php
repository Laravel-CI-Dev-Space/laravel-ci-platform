<?php

declare(strict_types=1);

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\EventType;
use App\Queries\Events\EventListQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class EventList extends Component
{
    use WithPagination;

    #[Url(as: 'period', history: true)]
    public string $period = 'upcoming';

    #[Url(as: 'type', history: true)]
    public ?string $type = null;

    public function updatedPeriod(): void
    {
        $this->resetPage();
    }

    public function updatedType(): void
    {
        $this->resetPage();
    }

    public function setPeriod(string $period): void
    {
        if (! in_array($period, ['upcoming', 'past', 'all'], true)) {
            return;
        }

        $this->period = $period;
        $this->resetPage();
    }

    public function setType(?string $typeSlug): void
    {
        $this->type = $typeSlug;
        $this->resetPage();
    }

    /**
     * @return LengthAwarePaginator<int, Event>
     */
    #[Computed]
    public function events(): LengthAwarePaginator
    {
        return EventListQuery::make($this->period, $this->type)
            ->paginate(9);
    }

    /**
     * @return Collection<int, EventType>
     */
    #[Computed]
    public function types(): Collection
    {
        return EventType::query()->orderBy('name')->get();
    }

    public function render(): View
    {
        return view('livewire.events.event-list');
    }
}
