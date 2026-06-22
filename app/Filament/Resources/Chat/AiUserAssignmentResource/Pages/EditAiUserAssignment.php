<?php

namespace App\Filament\Resources\Chat\AiUserAssignmentResource\Pages;

use App\Filament\Resources\Chat\AiUserAssignmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAiUserAssignment extends EditRecord
{
    protected static string $resource = AiUserAssignmentResource::class;
    protected function getHeaderActions(): array { return [DeleteAction::make()]; }
}
