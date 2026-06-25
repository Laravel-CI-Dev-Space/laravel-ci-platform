<?php

namespace App\Filament\Resources\MemberCards\Pages;

use App\Filament\Resources\MemberCards\MemberCardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMemberCards extends ListRecords
{
    protected static string $resource = MemberCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
