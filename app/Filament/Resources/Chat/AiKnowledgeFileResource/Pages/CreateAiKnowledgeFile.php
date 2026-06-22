<?php

namespace App\Filament\Resources\Chat\AiKnowledgeFileResource\Pages;

use App\Filament\Resources\Chat\AiKnowledgeFileResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAiKnowledgeFile extends CreateRecord
{
    protected static string $resource = AiKnowledgeFileResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uploaded_by'] = auth()->id();
        if (empty($data['filename'])) {
            $data['filename'] = 'manual-' . now()->format('Ymd_His') . '.md';
        }
        if (empty($data['disk_path'])) {
            $data['disk_path'] = 'ai-knowledge/' . $data['filename'];
        }
        return $data;
    }
}
