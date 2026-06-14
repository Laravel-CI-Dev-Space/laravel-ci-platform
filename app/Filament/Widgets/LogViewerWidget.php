<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Services\Monitoring\LogViewerService;
use Filament\Widgets\Widget;

class LogViewerWidget extends Widget
{
    protected string $view = 'filament.widgets.log-viewer';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '10s';

    public string $search = '';

    public string $level = '';

    public int $limit = 50;

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole([
            UserRole::SuperAdmin->value,
            UserRole::Admin->value,
        ]) ?? false;
    }

    public function getViewData(): array
    {
        $service = app(LogViewerService::class);

        return [
            'stats'   => $service->getStats(),
            'entries' => $service->getRecentEntries(limit: $this->limit, search: $this->search, level: $this->level),
            'levels'  => $service->levels(),
        ];
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->level  = '';
        $this->limit  = 50;
    }
}
