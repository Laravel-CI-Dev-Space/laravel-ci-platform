<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\AllEventRegistration;
use App\Models\Event;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EventStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $total     = Event::count();
        $published = Event::where('status', 'published')->count();
        $upcoming  = Event::where('status', 'published')->where('starts_at', '>', now())->count();
        $cancelled = Event::where('status', 'cancelled')->count();
        $paid      = Event::where('is_paid', true)->count();

        $registrationsThisMonth = AllEventRegistration::query()
            ->where('status', 'confirmed')
            ->whereMonth('registered_at', now()->month)
            ->whereYear('registered_at', now()->year)
            ->count();

        return [
            Stat::make('Événements total', $total)
                ->description("{$published} publiés · {$cancelled} annulés")
                ->color('primary')
                ->icon('heroicon-o-calendar'),

            Stat::make('À venir', $upcoming)
                ->description('Événements publiés et futurs')
                ->color($upcoming > 0 ? 'success' : 'gray')
                ->icon('heroicon-o-clock'),

            Stat::make('Brouillons', Event::where('status', 'draft')->count())
                ->description('En cours de préparation')
                ->color('warning')
                ->icon('heroicon-o-pencil'),

            Stat::make('Événements payants', $paid)
                ->description('Sur ' . $total . ' événements')
                ->color('info')
                ->icon('heroicon-o-currency-dollar'),

            Stat::make('Inscrits ce mois', $registrationsThisMonth)
                ->description('Confirmés · ' . now()->translatedFormat('F Y'))
                ->color('success')
                ->icon('heroicon-o-user-plus'),
        ];
    }
}
