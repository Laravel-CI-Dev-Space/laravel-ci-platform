<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Services\Analytics\AnalyticsService;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class AnalyticsDashboard extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = 'Analytics';

    protected static ?string $title = 'Analytics';

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.pages.analytics-dashboard';

    public string $period = '30d';

    public static function getNavigationGroup(): ?string
    {
        return 'Monitoring';
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        $analytics = app(AnalyticsService::class);

        $pageViews     = $analytics->getTotalPageViews($this->period);
        $unique        = $analytics->getUniqueVisitors($this->period);
        $authenticated = $analytics->getAuthenticatedVisitors($this->period);
        $breakdown     = $analytics->getEventBreakdown($this->period);
        $devices       = $analytics->getDeviceBreakdown($this->period);

        $prevPeriod    = $this->period . '_prev';
        $prevPageViews = $analytics->getTotalPageViews($prevPeriod);
        $prevUnique    = $analytics->getUniqueVisitors($prevPeriod);
        $prevBreakdown = $analytics->getEventBreakdown($prevPeriod);

        $registrations = $breakdown['event_registration'] ?? 0;
        $applications  = $breakdown['job_application']    ?? 0;
        $logins        = $breakdown['auth_login']         ?? 0;
        $newMembers    = $breakdown['auth_register']      ?? 0;
        $cancellations = $breakdown['event_cancellation'] ?? 0;

        $prevRegistrations = $prevBreakdown['event_registration'] ?? 0;
        $prevApplications  = $prevBreakdown['job_application']    ?? 0;

        $pvSparkline  = $analytics->getDailyPageViews('7d');
        $regSparkline = $analytics->getEventSparkline('event_registration', 7);
        $appSparkline = $analytics->getEventSparkline('job_application', 7);

        $desktop  = $devices['desktop'] ?? 0;
        $mobile   = $devices['mobile']  ?? 0;
        $tablet   = $devices['tablet']  ?? 0;
        $devTotal = max($desktop + $mobile + $tablet, 1);

        $periodLabel = match ($this->period) {
            '7d'    => '7 derniers jours',
            '30d'   => '30 derniers jours',
            '90d'   => '3 derniers mois',
            'today' => "Aujourd'hui",
            default => '30 derniers jours',
        };

        return [
            'period'      => $this->period,
            'periodLabel' => $periodLabel,

            'kpis' => [
                [
                    'key'        => 'pv',
                    'label'      => 'Pages vues',
                    'value'      => $pageViews,
                    'sub'        => number_format($unique) . ' visiteurs uniques',
                    'trend'      => $this->trend($pageViews, $prevPageViews),
                    'sparkline'  => $pvSparkline,
                    'chart_type' => 'line',
                    'bg'         => 'bg-emerald-500',
                    'icon'       => 'eye',
                ],
                [
                    'key'        => 'unique',
                    'label'      => 'Visiteurs uniques',
                    'value'      => $unique,
                    'sub'        => number_format($authenticated) . ' authentifiés',
                    'trend'      => $this->trend($unique, $prevUnique),
                    'sparkline'  => $pvSparkline,
                    'chart_type' => 'bar',
                    'bg'         => 'bg-blue-500',
                    'icon'       => 'users',
                ],
                [
                    'key'        => 'reg',
                    'label'      => 'Inscriptions événements',
                    'value'      => $registrations,
                    'sub'        => number_format($cancellations) . ' annulation(s)',
                    'trend'      => $this->trend($registrations, $prevRegistrations),
                    'sparkline'  => $regSparkline,
                    'chart_type' => 'bar',
                    'bg'         => 'bg-amber-500',
                    'icon'       => 'calendar',
                ],
                [
                    'key'        => 'app',
                    'label'      => 'Candidatures emploi',
                    'value'      => $applications,
                    'sub'        => number_format($newMembers) . ' nouveaux membres',
                    'trend'      => $this->trend($applications, $prevApplications),
                    'sparkline'  => $appSparkline,
                    'chart_type' => 'bar',
                    'bg'         => 'bg-violet-500',
                    'icon'       => 'briefcase',
                ],
            ],

            'visitsChart' => $analytics->getDailyPageViews($this->period),
            'eventsChart' => $analytics->getDailyEvents($this->period),
            'topPages'    => $analytics->getTopPages($this->period, 10),

            'deviceChart' => [
                'labels'      => ['Desktop', 'Mobile', 'Tablette'],
                'data'        => [$desktop, $mobile, $tablet],
                'desktop_pct' => (int) round($desktop / $devTotal * 100),
                'mobile_pct'  => (int) round($mobile / $devTotal * 100),
                'tablet_pct'  => (int) round($tablet / $devTotal * 100),
            ],

            'eventSummary' => [
                ['label' => 'Inscriptions événements', 'count' => $registrations,  'color' => 'emerald'],
                ['label' => 'Candidatures emploi',     'count' => $applications,   'color' => 'blue'],
                ['label' => 'Connexions',               'count' => $logins,         'color' => 'orange'],
                ['label' => 'Nouveaux membres',         'count' => $newMembers,     'color' => 'violet'],
                ['label' => 'Annulations',              'count' => $cancellations,  'color' => 'red'],
            ],
        ];
    }

    private function trend(int $current, int $previous): ?int
    {
        if ($previous === 0) {
            return $current > 0 ? 100 : null;
        }

        return (int) round(($current - $previous) / $previous * 100);
    }
}
