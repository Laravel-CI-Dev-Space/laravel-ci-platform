<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\UserRole;
use App\Filament\Widgets\LogViewerWidget;
use App\Filament\Widgets\RecentActivityFeedWidget;
use App\Filament\Widgets\RequestLatencyStatsWidget;
use App\Filament\Widgets\RequestLatencyWidget;
use App\Filament\Widgets\RequestOriginWidget;
use App\Filament\Widgets\ServerHealthWidget;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class SystemMonitoring extends Page
{
    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedServerStack;

    protected static ?string $navigationLabel = 'Performances serveur';

    protected static ?string $title = 'Monitoring système';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'Monitoring';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole([
            UserRole::SuperAdmin->value,
            UserRole::Admin->value,
        ]) ?? false;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ServerHealthWidget::class,
            RequestLatencyStatsWidget::class,
            RequestLatencyWidget::class,
            RequestOriginWidget::class,
            RecentActivityFeedWidget::class,
            LogViewerWidget::class,
        ];
    }
}
