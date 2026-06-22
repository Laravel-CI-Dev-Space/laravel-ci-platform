<?php

namespace App\Filament\Resources\Chat\ChatMonitoringResource\Pages;

use App\Filament\Resources\Chat\ChatMonitoringResource;
use App\Filament\Resources\Chat\ChatMonitoringResource\Widgets\ChatTokenStatsWidget;
use App\Filament\Resources\Chat\ChatMonitoringResource\Widgets\ChatTokenTrendWidget;
use App\Filament\Resources\Chat\ChatMonitoringResource\Widgets\ChatTopUsersWidget;
use Filament\Resources\Pages\ListRecords;

class ListChatMonitoring extends ListRecords
{
    protected static string $resource = ChatMonitoringResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            ChatTokenStatsWidget::class,
            ChatTokenTrendWidget::class,
            ChatTopUsersWidget::class,
        ];
    }
}
