<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Event;
use Carbon\Carbon;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class EventCalendarWidget extends Widget
{
    protected string $view = 'filament.widgets.event-calendar';

    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public int $month;
    public int $year;

    public bool $showModal = false;
    public string $selectedDate = '';
    public array $selectedEvents = [];

    public function mount(): void
    {
        $this->month = now()->month;
        $this->year  = now()->year;
    }

    public function prevMonth(): void
    {
        $dt = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->month = $dt->month;
        $this->year  = $dt->year;
        $this->closeModal();
    }

    public function nextMonth(): void
    {
        $dt = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->month = $dt->month;
        $this->year  = $dt->year;
        $this->closeModal();
    }

    public function selectDate(string $date): void
    {
        $events = $this->getEventsForMonth()
            ->filter(fn (Event $e) => Carbon::parse($e->starts_at)->format('Y-m-d') === $date)
            ->values();

        if ($events->isEmpty()) {
            return;
        }

        $this->selectedDate   = $date;
        $this->selectedEvents = $events->map(fn (Event $e) => [
            'id'           => $e->id,
            'title'        => $e->title,
            'slug'         => $e->slug,
            'type'         => $e->type instanceof \BackedEnum ? $e->type->value : (string) $e->type,
            'status'       => $e->status instanceof \BackedEnum ? $e->status->value : (string) $e->status,
            'starts_at'    => $e->starts_at->format('d/m/Y H:i'),
            'ends_at'      => $e->ends_at?->format('H:i'),
            'location'     => $e->location,
            'is_paid'      => $e->is_paid ?? false,
            'price'        => $e->price ?? null,
            'currency'     => $e->currency ?? 'XOF',
            'capacity'     => $e->capacity,
            'registered'   => $e->registrations_count ?? 0,
            'edit_url'     => route('filament.admin.resources.events.edit', $e->id),
            'view_url'     => route('filament.admin.resources.events.view', $e->id),
        ])->toArray();

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal      = false;
        $this->selectedEvents = [];
        $this->selectedDate   = '';
    }

    public function getCalendarDaysProperty(): array
    {
        $firstDay  = Carbon::create($this->year, $this->month, 1);
        $lastDay   = $firstDay->copy()->endOfMonth();
        $startDow  = ($firstDay->dayOfWeek + 6) % 7; // Monday = 0
        $totalDays = $lastDay->day;

        $eventsByDate = $this->getEventsForMonth()
            ->groupBy(fn (Event $e) => Carbon::parse($e->starts_at)->format('Y-m-d'));

        $days = [];

        // Padding before first day
        for ($i = 0; $i < $startDow; $i++) {
            $days[] = ['day' => null, 'date' => null, 'events' => []];
        }

        for ($d = 1; $d <= $totalDays; $d++) {
            $date   = Carbon::create($this->year, $this->month, $d)->format('Y-m-d');
            $events = $eventsByDate->get($date, collect())->map(fn (Event $e) => [
                'title' => $e->title,
                'type'  => $e->type instanceof \BackedEnum ? $e->type->value : (string) $e->type,
            ])->toArray();

            $days[] = [
                'day'     => $d,
                'date'    => $date,
                'today'   => $date === now()->format('Y-m-d'),
                'events'  => $events,
            ];
        }

        return $days;
    }

    private function getEventsForMonth(): Collection
    {
        return Event::whereYear('starts_at', $this->year)
            ->whereMonth('starts_at', $this->month)
            ->orderBy('starts_at')
            ->get();
    }
}
