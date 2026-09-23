<?php

declare(strict_types=1);

namespace App\Livewire\Events;

use App\Models\Event;
use App\Models\EventMedia;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class EventMediaGallery extends Component
{
    use WithPagination;

    public Event $event;

    /** IDs des médias sélectionnés pour le téléchargement groupé */
    public array $selectedIds = [];

    /** Filtre actif : 'all' | 'photo' | 'video' */
    public string $filter = 'all';

    public function mount(Event $event): void
    {
        $this->event = $event;
    }

    // ── Sélection ──────────────────────────────────────────────────────────

    public function toggleSelect(int $id): void
    {
        if (in_array($id, $this->selectedIds, true)) {
            $this->selectedIds = array_values(array_filter(
                $this->selectedIds,
                fn ($v) => $v !== $id
            ));
        } else {
            $this->selectedIds[] = $id;
        }
    }

    public function selectAll(): void
    {
        $this->selectedIds = $this->mediaQuery()->pluck('id')->map(fn ($id) => (int) $id)->toArray();
    }

    public function clearSelection(): void
    {
        $this->selectedIds = [];
    }

    public function isSelected(int $id): bool
    {
        return in_array($id, $this->selectedIds, true);
    }

    // ── Filtre ─────────────────────────────────────────────────────────────

    public function setFilter(string $filter): void
    {
        $this->filter     = $filter;
        $this->selectedIds = [];
        $this->resetPage();
    }

    // ── Requête ────────────────────────────────────────────────────────────

    private function mediaQuery()
    {
        $query = EventMedia::where('event_id', $this->event->id)->orderBy('order');

        if ($this->filter !== 'all') {
            $query->where('type', $this->filter);
        }

        return $query;
    }

    // ── Rendu ──────────────────────────────────────────────────────────────

    public function render(): View
    {
        $media = $this->mediaQuery()->paginate(48);

        $mediaCounts = [
            'all'   => EventMedia::where('event_id', $this->event->id)->count(),
            'photo' => EventMedia::where('event_id', $this->event->id)->where('type', 'photo')->count(),
            'video' => EventMedia::where('event_id', $this->event->id)->where('type', 'video')->count(),
        ];

        return view('livewire.events.event-media-gallery', [
            'media'       => $media,
            'mediaCounts' => $mediaCounts,
        ]);
    }
}
