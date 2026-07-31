<?php

namespace App\Filament\Resources\MemberCards\Pages;

use App\Filament\Pages\CardSettings;
use App\Filament\Resources\MemberCards\MemberCardResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMemberCards extends ListRecords
{
    protected static string $resource = MemberCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),

            Action::make('card_settings')
                ->label('Configuration')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('gray')
                ->url(fn (): string => CardSettings::getUrl()),
        ];
    }
}
