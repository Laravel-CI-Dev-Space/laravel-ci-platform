<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Analytics;

use App\Services\Analytics\AnalyticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnalyticsOverviewWidget extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected static ?int $sort = 1;

    public string $period = '30d';

    protected function getStats(): array
    {
        $analytics = app(AnalyticsService::class);

        $pageViews     = $analytics->getTotalPageViews($this->period);
        $unique        = $analytics->getUniqueVisitors($this->period);
        $authenticated = $analytics->getAuthenticatedVisitors($this->period);
        $events        = $analytics->getEventBreakdown($this->period);
        $devices       = $analytics->getDeviceBreakdown($this->period);

        $registrations = $events['event_registration'] ?? 0;
        $applications  = $events['job_application']    ?? 0;
        $logins        = $events['auth_login']         ?? 0;
        $newMembers    = $events['auth_register']      ?? 0;

        $mobile  = $devices['mobile']  ?? 0;
        $desktop = $devices['desktop'] ?? 0;
        $tablet  = $devices['tablet']  ?? 0;
        $total   = max($mobile + $desktop + $tablet, 1);

        $periodLabel = match ($this->period) {
            '7d'    => '7 derniers jours',
            '30d'   => '30 derniers jours',
            '90d'   => '3 derniers mois',
            'today' => "aujourd'hui",
            default => '30 derniers jours',
        };

        return [
            Stat::make('Pages vues', number_format($pageViews))
                ->description("{$unique} visiteurs uniques · {$periodLabel}")
                ->color('primary')
                ->icon('heroicon-o-eye'),

            Stat::make('Visiteurs authentifiés', number_format($authenticated))
                ->description('Membres connectés · ' . $periodLabel)
                ->color('info')
                ->icon('heroicon-o-user-circle'),

            Stat::make('Inscriptions événements', $registrations)
                ->description($periodLabel)
                ->color('success')
                ->icon('heroicon-o-calendar'),

            Stat::make('Candidatures emploi', $applications)
                ->description($periodLabel)
                ->color('warning')
                ->icon('heroicon-o-briefcase'),

            Stat::make('Connexions / Nouveaux membres', "{$logins} / {$newMembers}")
                ->description('Logins · Inscriptions · ' . $periodLabel)
                ->color('gray')
                ->icon('heroicon-o-arrow-right-on-rectangle'),

            Stat::make('Mobile · Desktop · Tablette', "{$mobile} · {$desktop} · {$tablet}")
                ->description(round($mobile / $total * 100) . '% mobile')
                ->color('gray')
                ->icon('heroicon-o-device-phone-mobile'),
        ];
    }
}
