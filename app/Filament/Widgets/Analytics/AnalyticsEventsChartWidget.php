<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Analytics;

use App\Services\Analytics\AnalyticsService;
use Filament\Widgets\ChartWidget;

class AnalyticsEventsChartWidget extends ChartWidget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = 3;

    protected ?string $heading = 'Interactions par jour';

    protected int|string|array $columnSpan = 'full';

    public ?string $filter = '30d';

    protected function getFilters(): ?array
    {
        return [
            '7d'  => '7 derniers jours',
            '30d' => '30 derniers jours',
            '90d' => '3 derniers mois',
        ];
    }

    protected function getData(): array
    {
        return app(AnalyticsService::class)->getDailyEvents($this->filter ?? '30d');
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => true, 'position' => 'top'],
            ],
            'scales' => [
                'y' => ['beginAtZero' => true, 'stacked' => false],
                'x' => ['grid' => ['display' => false]],
            ],
        ];
    }
}
