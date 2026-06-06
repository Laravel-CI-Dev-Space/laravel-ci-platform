<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\CompanyAccount;
use App\Models\CompanyRegistrationRequest;
use App\Models\JobApplication;
use App\Models\JobOffer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CompanyStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $totalAccounts     = CompanyAccount::count();
        $activeAccounts    = CompanyAccount::where('status', 'active')->count();
        $suspendedAccounts = CompanyAccount::where('status', 'suspended')->count();
        $pendingRequests   = CompanyRegistrationRequest::where('status', 'pending')->count();
        $approvedRequests  = CompanyRegistrationRequest::where('status', 'approved')->count();
        $activeOffers      = JobOffer::where('status', 'active')->count();
        $totalApplications = JobApplication::count();

        return [
            Stat::make('Comptes entreprise', $totalAccounts)
                ->description("{$activeAccounts} actifs · {$suspendedAccounts} suspendus")
                ->color('primary')
                ->icon('heroicon-o-building-office'),

            Stat::make('Actifs', $activeAccounts)
                ->description('Entreprises avec accès au portail')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Demandes en attente', $pendingRequests)
                ->description("{$approvedRequests} approuvées au total")
                ->color($pendingRequests > 0 ? 'warning' : 'gray')
                ->icon('heroicon-o-clock'),

            Stat::make("Offres d'emploi actives", $activeOffers)
                ->description('Publiées par les entreprises')
                ->color('info')
                ->icon('heroicon-o-briefcase'),

            Stat::make('Candidatures reçues', number_format($totalApplications))
                ->description('Cumulées toutes offres')
                ->color('gray')
                ->icon('heroicon-o-inbox'),
        ];
    }
}
