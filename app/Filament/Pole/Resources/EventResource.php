<?php

namespace App\Filament\Pole\Resources;

use App\Enums\EventStatus;
use App\Enums\UserRole;
use App\Models\Event;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static ?string $navigationLabel = 'Événements';

    protected static ?string $modelLabel = 'Événement';

    protected static ?string $pluralModelLabel = 'Événements';

    

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole(UserRole::PoleEvenements->value) ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->label('Titre')
                ->required()
                ->maxLength(255),

            Textarea::make('description')
                ->label('Description')
                ->rows(4)
                ->required(),

            TextInput::make('location')
                ->label('Lieu')
                ->maxLength(255),

            DateTimePicker::make('starts_at')
                ->label('Début')
                ->required(),

            DateTimePicker::make('ends_at')
                ->label('Fin'),

            TextInput::make('capacity')
                ->label('Capacité')
                ->numeric()
                ->minValue(1),

            Select::make('status')
                ->label('Statut')
                ->options([
                    EventStatus::Draft->value     => 'Brouillon',
                    EventStatus::Published->value => 'Publié',
                    EventStatus::Cancelled->value => 'Annulé',
                ])
                ->required()
                ->default(EventStatus::Draft->value),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->limit(45),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (EventStatus $state) => match ($state) {
                        EventStatus::Published => 'success',
                        EventStatus::Cancelled => 'danger',
                        EventStatus::Completed => 'info',
                        default                => 'gray',
                    })
                    ->formatStateUsing(fn (EventStatus $state) => match ($state) {
                        EventStatus::Draft      => 'Brouillon',
                        EventStatus::Published  => 'Publié',
                        EventStatus::Cancelled  => 'Annulé',
                        EventStatus::Completed  => 'Terminé',
                    }),

                TextColumn::make('starts_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('registrations_count')
                    ->label('Inscrits')
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        EventStatus::Draft->value     => 'Brouillon',
                        EventStatus::Published->value => 'Publié',
                        EventStatus::Cancelled->value => 'Annulé',
                        EventStatus::Completed->value => 'Terminé',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('publish')
                    ->label('Publier')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->visible(fn (Event $record) => $record->status === EventStatus::Draft)
                    ->action(fn (Event $record) => $record->update(['status' => EventStatus::Published]))
                    ->requiresConfirmation(),
                Action::make('cancel')
                    ->label('Annuler')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->visible(fn (Event $record) => $record->status === EventStatus::Published)
                    ->action(fn (Event $record) => $record->update(['status' => EventStatus::Cancelled]))
                    ->requiresConfirmation(),
                DeleteAction::make(),
            ])
            ->defaultSort('starts_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Filament\Pole\Resources\EventResource\Pages\ListEvents::route('/'),
            'create' => \App\Filament\Pole\Resources\EventResource\Pages\CreateEvent::route('/create'),
            'edit'   => \App\Filament\Pole\Resources\EventResource\Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
