<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\AllEventRegistration;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RegistrationStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static bool $isDiscovered = false;

    /** Optionally filter by event_id (set by the list page via $filters). */
    public ?int $eventId = null;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $query = AllEventRegistration::query();

        if ($this->eventId !== null) {
            $query->where('event_id', $this->eventId);
        }

        $total      = (clone $query)->count();
        $members    = (clone $query)->where('participant_type', 'member')->count();
        $guests     = (clone $query)->where('participant_type', 'guest')->count();
        $confirmed  = (clone $query)->where('status', 'confirmed')->count();
        $waitlisted = (clone $query)->where('status', 'waitlisted')->count();
        $cancelled  = (clone $query)->where('status', 'cancelled')->count();
        $paid       = (clone $query)->where('payment_status', 'paid')->count();
        $ticketed   = (clone $query)->whereNotNull('ticket_number')->count();

        return [
            Stat::make('Total inscrits', $total)
                ->description("{$members} membres · {$guests} invités")
                ->color('primary')
                ->icon('heroicon-o-users'),

            Stat::make('Confirmés', $confirmed)
                ->description("{$waitlisted} en liste d'attente")
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Annulés', $cancelled)
                ->color($cancelled > 0 ? 'danger' : 'gray')
                ->icon('heroicon-o-x-circle'),

            Stat::make('Paiements reçus', $paid)
                ->color('warning')
                ->icon('heroicon-o-credit-card'),

            Stat::make('Tickets générés', $ticketed)
                ->color('info')
                ->icon('heroicon-o-ticket'),
        ];
    }
}
