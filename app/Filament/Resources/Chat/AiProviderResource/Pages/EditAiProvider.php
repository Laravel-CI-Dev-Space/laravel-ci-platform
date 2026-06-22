<?php

namespace App\Filament\Resources\Chat\AiProviderResource\Pages;

use App\Filament\Resources\Chat\AiProviderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAiProvider extends EditRecord
{
    protected static string $resource = AiProviderResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
