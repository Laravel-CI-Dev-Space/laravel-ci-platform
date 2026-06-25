<?php

namespace App\Filament\Resources\MemberCards\Pages;

use App\Filament\Resources\MemberCards\MemberCardResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMemberCard extends EditRecord
{
    protected static string $resource = MemberCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
