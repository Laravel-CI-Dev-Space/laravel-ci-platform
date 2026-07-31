<?php

namespace App\Filament\Pole\Resources;

use App\Enums\UserRole;
use App\Models\Resource as LearningResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PoleResourceResource extends Resource
{
    protected static ?string $model = LearningResource::class;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderOpen;

    protected static ?string $navigationLabel = 'Ressources';

    protected static ?string $modelLabel = 'Ressource';

    protected static ?string $pluralModelLabel = 'Ressources';

    

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasRole(UserRole::PoleTechFormation->value) ?? false;
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
                ->rows(3),

            Select::make('type')
                ->label('Type')
                ->options([
                    'pdf'     => 'PDF',
                    'video'   => 'Vidéo',
                    'link'    => 'Lien',
                    'archive' => 'Archive',
                ])
                ->required(),

            Toggle::make('is_public')
                ->label('Visible par tous les membres')
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->limit(50),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color('gray'),

                IconColumn::make('is_public')
                    ->label('Public')
                    ->boolean(),

                TextColumn::make('downloads_count')
                    ->label('Téléchargements')
                    ->badge()
                    ->color('info'),

                TextColumn::make('created_at')
                    ->label('Ajoutée le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Filament\Pole\Resources\PoleResourceResource\Pages\ListPoleResources::route('/'),
            'create' => \App\Filament\Pole\Resources\PoleResourceResource\Pages\CreatePoleResource::route('/create'),
            'edit'   => \App\Filament\Pole\Resources\PoleResourceResource\Pages\EditPoleResource::route('/{record}/edit'),
        ];
    }
}
