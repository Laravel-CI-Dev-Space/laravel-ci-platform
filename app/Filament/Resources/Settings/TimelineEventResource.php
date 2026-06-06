<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings;

use App\Filament\Resources\Settings\Pages\CreateTimelineEvent;
use App\Filament\Resources\Settings\Pages\EditTimelineEvent;
use App\Filament\Resources\Settings\Pages\ListTimelineEvents;
use App\Filament\Resources\Settings\Schemas\TimelineEventForm;
use App\Filament\Resources\Settings\Tables\TimelineEventsTable;
use App\Models\TimelineEvent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TimelineEventResource extends Resource
{
    protected static ?string $model = TimelineEvent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static ?string $navigationLabel = 'Timeline';

    protected static ?string $modelLabel = 'Étape';

    protected static ?string $pluralModelLabel = 'Timeline';

    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): ?string
    {
        return 'Configuration';
    }

    public static function form(Schema $schema): Schema
    {
        return TimelineEventForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TimelineEventsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTimelineEvents::route('/'),
            'create' => CreateTimelineEvent::route('/create'),
            'edit'   => EditTimelineEvent::route('/{record}/edit'),
        ];
    }
}
