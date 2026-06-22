<?php

namespace App\Filament\Resources\Chat\AiKnowledgeFileResource\Pages;

use App\Filament\Resources\Chat\AiKnowledgeFileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAiKnowledgeFiles extends ListRecords
{
    protected static string $resource = AiKnowledgeFileResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
