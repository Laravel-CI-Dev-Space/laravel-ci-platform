<?php

declare(strict_types=1);

namespace App\Filament\Widgets\Analytics;

use App\Services\Analytics\AnalyticsService;
use Filament\Widgets\Widget;

class AnalyticsTopPagesWidget extends Widget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = 4;

    protected string $view = 'filament.widgets.analytics.top-pages';

    protected int|string|array $columnSpan = 'full';

    public string $period = '30d';

    protected function getViewData(): array
    {
        $pages = app(AnalyticsService::class)->getTopPages($this->period, limit: 15);

        $maxViews = $pages->max('views') ?: 1;

        return [
            'pages'    => $pages,
            'maxViews' => $maxViews,
            'period'   => $this->period,
        ];
    }
}
