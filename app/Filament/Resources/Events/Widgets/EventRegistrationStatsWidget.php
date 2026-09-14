<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events\Widgets;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\GuestRegistration;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EventRegistrationStatsWidget extends BaseWidget
{
    public ?Event $record = null;

    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        if (! $this->record) {
            return [];
        }

        $event = $this->record;

        $membres   = EventRegistration::where('event_id', $event->id)->count();
        $invites   = GuestRegistration::where('event_id', $event->id)->count();
        $confirmed = EventRegistration::where('event_id', $event->id)->where('status', 'confirmed')->count()
            + GuestRegistration::where('event_id', $event->id)->where('status', 'confirmed')->count();
        $attended  = EventRegistration::where('event_id', $event->id)->where('status', 'attended')->count();

        return [
            Stat::make('Total inscrits', $membres + $invites)
                ->description("{$membres} membres · {$invites} invités")
                ->icon('heroicon-o-user-group')
                ->color('primary'),

            Stat::make('Confirmés', $confirmed)
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Présents', $attended)
                ->description($event->capacity ? "Capacité : {$event->capacity}" : 'Pas de limite')
                ->icon('heroicon-o-identification')
                ->color($attended > 0 ? 'info' : 'gray'),

            Stat::make('Invités externes', $invites)
                ->description('Luma / sans compte')
                ->icon('heroicon-o-envelope-open')
                ->color('warning'),
        ];
    }
}
