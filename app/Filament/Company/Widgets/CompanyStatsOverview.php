<?php

declare(strict_types=1);

namespace App\Filament\Company\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CompanyStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $account = auth('company')->user();
        $company = $account?->company;

        $totalOffers       = $company?->jobOffers()->count() ?? 0;
        $activeOffers      = $company?->activeJobOffers()->count() ?? 0;
        $pendingOffers     = $company?->jobOffers()->where('status', 'pending')->count() ?? 0;

        $totalApplications = $company?->jobOffers()
            ->withCount('applications')
            ->get()
            ->sum('applications_count') ?? 0;

        $pendingApplications = $company?->jobOffers()
            ->with(['applications' => fn ($q) => $q->where('status', 'pending')])
            ->get()
            ->sum(fn ($offer) => $offer->applications->count()) ?? 0;

        $viewsToday = $company?->jobOffers()
            ->whereDate('updated_at', today())
            ->sum('views_count') ?? 0;

        return [
            Stat::make('Offres actives', $activeOffers)
                ->description("{$totalOffers} offres au total · {$pendingOffers} en attente de validation")
                ->descriptionIcon('heroicon-o-briefcase')
                ->color('success')
                ->chart([1, 2, $activeOffers]),

            Stat::make('Candidatures reçues', $totalApplications)
                ->description("{$pendingApplications} en attente de traitement")
                ->descriptionIcon('heroicon-o-users')
                ->color($pendingApplications > 0 ? 'warning' : 'info')
                ->chart([0, $totalApplications / 2, $totalApplications]),

            Stat::make('Vues aujourd\'hui', $viewsToday)
                ->description('Vues sur vos offres actives')
                ->descriptionIcon('heroicon-o-eye')
                ->color('primary'),
        ];
    }
}
