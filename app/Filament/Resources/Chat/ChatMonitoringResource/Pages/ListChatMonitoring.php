<?php

namespace App\Filament\Resources\Chat\ChatMonitoringResource\Pages;

use App\Filament\Resources\Chat\ChatMonitoringResource;
use App\Models\Chat\ChatTokenBudget;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Text;

class ListChatMonitoring extends ListRecords
{
    protected static string $resource = ChatMonitoringResource::class;

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
