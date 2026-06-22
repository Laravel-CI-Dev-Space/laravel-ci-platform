<?php

namespace App\Filament\Resources\Chat\AiKnowledgeFileResource\Pages;

use App\Filament\Resources\Chat\AiKnowledgeFileResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAiKnowledgeFile extends EditRecord
{
    protected static string $resource = AiKnowledgeFileResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
}
