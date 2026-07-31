<?php

namespace App\Filament\Resources\Poles;

use App\Filament\Resources\Poles\Pages\CreatePole;
use App\Filament\Resources\Poles\Pages\EditPole;
use App\Filament\Resources\Poles\Pages\ListPoles;
use App\Filament\Resources\Poles\Pages\ViewPole;
use App\Filament\Resources\Poles\RelationManagers\PoleMembersRelationManager;
use App\Models\Pole;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PoleResource extends Resource
{
    protected static ?string $model = Pole::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Pôles';

    protected static ?string $modelLabel = 'Pôle';

    protected static ?string $pluralModelLabel = 'Pôles';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Communauté';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Nom du pôle')
                ->required()
                ->maxLength(150)
                ->placeholder('ex: Tech & Formation'),

            TextInput::make('slug')
                ->label('Identifiant technique (slug)')
                ->required()
                ->maxLength(50)
                ->unique(ignoreRecord: true)
                ->placeholder('ex: tech-formation')
                ->helperText('Utilisé pour lier automatiquement le rôle de gestionnaire. Valeurs attendues : communication, evenements, tech-formation, employabilite, partenariat'),

            TextInput::make('position')
                ->label("Position (ordre d'affichage)")
                ->numeric()
                ->default(0),

            Toggle::make('is_active')
                ->label('Actif')
                ->default(true),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([

            Section::make()
                ->columns(3)
                ->schema([
                    TextEntry::make('name')
                        ->label('Nom du pôle')
                        ->weight('bold')
                        ->size('lg'),

                    TextEntry::make('members_count')
                        ->label('Nombre de membres')
                        ->getStateUsing(fn ($record) => $record->members()->count() . ' / 3')
                        ->badge()
                        ->color('info'),

                    IconEntry::make('is_active')
                        ->label('Statut')
                        ->boolean()
                        ->trueIcon(Heroicon::OutlinedCheckCircle)
                        ->falseIcon(Heroicon::OutlinedXCircle)
                        ->trueColor('success')
                        ->falseColor('danger'),
                ]),

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom du pôle')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('members_count')
                    ->label('Membres')
                    ->counts('members')
                    ->badge()
                    ->color('info'),

                TextColumn::make('active_members_count')
                    ->label('Membres actifs')
                    ->counts('activeMembers')
                    ->badge()
                    ->color('success'),

                TextColumn::make('position')
                    ->label('Ordre')
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label('Actif'),
            ])
            ->recordUrl(fn (Pole $record): string => static::getUrl('view', ['record' => $record]))
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('position');
    }

    public static function getRelations(): array
    {
        return [
            PoleMembersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPoles::route('/'),
            'create' => CreatePole::route('/create'),
            'view'   => ViewPole::route('/{record}'),
            'edit'   => EditPole::route('/{record}/edit'),
        ];
    }
}
