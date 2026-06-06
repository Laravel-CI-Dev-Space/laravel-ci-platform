<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Analytics;

use App\Services\Analytics\AnalyticsService;
use Filament\Widgets\ChartWidget;

class AnalyticsVisitsChartWidget extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = 2;

    protected ?string $heading = 'Visites quotidiennes';

    protected int|string|array $columnSpan = 'full';

    public ?string $filter = '30d';

    protected function getFilters(): ?array
    {
        return [
            'today' => "Aujourd'hui",
            '7d'    => '7 derniers jours',
            '30d'   => '30 derniers jours',
            '90d'   => '3 derniers mois',
        ];
    }

    protected function getData(): array
    {
        $result = app(AnalyticsService::class)->getDailyPageViews($this->filter ?? '30d');

        return [
            'datasets' => [
                [
                    'label'           => 'Pages vues',
                    'data'            => $result['data'],
                    'borderColor'     => 'rgba(249,115,22,1)',
                    'backgroundColor' => 'rgba(249,115,22,0.1)',
                    'fill'            => true,
                    'tension'         => 0.4,
                    'pointRadius'     => 3,
                ],
            ],
            'labels' => $result['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend'  => ['display' => false],
                'tooltip' => ['mode' => 'index'],
            ],
            'scales' => [
                'y' => ['beginAtZero' => true, 'grid' => ['display' => true]],
                'x' => ['grid' => ['display' => false]],
            ],
        ];
    }
}
