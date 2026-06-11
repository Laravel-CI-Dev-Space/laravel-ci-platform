<?php

namespace App\Filament\Resources\EventWaitlists;

use App\Filament\Resources\EventWaitlists\Pages\ListEventWaitlists;
use App\Filament\Resources\EventWaitlists\Tables\EventWaitlistsTable;
use App\Models\EventWaitlist;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EventWaitlistResource extends Resource
{
    protected static ?string $model = EventWaitlist::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static ?string $navigationLabel = 'Liste d\'attente';

    protected static ?string $modelLabel = 'Entrée liste d\'attente';

    protected static ?string $pluralModelLabel = 'Liste d\'attente';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return 'Événements';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return EventWaitlistsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEventWaitlists::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['event', 'user']);
    }
}
