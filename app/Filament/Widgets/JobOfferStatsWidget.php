<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\JobApplication;
use App\Models\JobOffer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class JobOfferStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $total        = JobOffer::count();
        $active       = JobOffer::where('status', 'active')->count();
        $pending      = JobOffer::where('status', 'pending')->count();
        $expired      = JobOffer::where('status', 'expired')->count();
        $filled       = JobOffer::where('status', 'filled')->count();
        $applications = JobApplication::count();
        $appThisMonth = JobApplication::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return [
            Stat::make('Offres total', $total)
                ->description("{$active} actives · {$expired} expirées")
                ->color('primary')
                ->icon('heroicon-o-briefcase'),

            Stat::make('En attente de validation', $pending)
                ->description($pending > 0 ? 'Action requise' : 'Aucune offre en attente')
                ->color($pending > 0 ? 'warning' : 'success')
                ->icon('heroicon-o-clock'),

            Stat::make('Actives', $active)
                ->description("{$filled} pourvues ce cycle")
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Candidatures reçues', number_format($applications))
                ->description("{$appThisMonth} ce mois · " . now()->translatedFormat('F Y'))
                ->color('info')
                ->icon('heroicon-o-inbox'),

            Stat::make('Offres pourvues', $filled)
                ->description('Postes comblés')
                ->color('gray')
                ->icon('heroicon-o-user-plus'),
        ];
    }
}
