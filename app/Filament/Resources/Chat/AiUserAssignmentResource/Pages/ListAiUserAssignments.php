<?php

namespace App\Filament\Resources\Chat\AiUserAssignmentResource\Pages;

use App\Filament\Resources\Chat\AiUserAssignmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAiUserAssignments extends ListRecords
{
    protected static string $resource = AiUserAssignmentResource::class;
    protected function getHeaderActions(): array { return [CreateAction::make()]; }
}
