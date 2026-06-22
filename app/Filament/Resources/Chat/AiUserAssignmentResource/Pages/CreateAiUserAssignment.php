<?php

namespace App\Filament\Resources\Chat\AiUserAssignmentResource\Pages;

use App\Filament\Resources\Chat\AiUserAssignmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAiUserAssignment extends CreateRecord
{
    protected static string $resource = AiUserAssignmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['assigned_by'] = auth()->id();
        return $data;
    }
}
