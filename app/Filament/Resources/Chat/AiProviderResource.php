<?php

declare(strict_types=1);

namespace App\Filament\Resources\Chat;

use App\Filament\Resources\Chat\AiProviderResource\Pages;
use App\Models\Chat\AiProvider;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class AiProviderResource extends Resource
{
    protected static ?string $model = AiProvider::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCpuChip;
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Provider IA';
    protected static ?string $pluralModelLabel = 'Providers IA';

    public static function getNavigationGroup(): string { return 'Assistant IA'; }
    public static function getNavigationLabel(): string { return 'Providers'; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label('Identifiant')
                ->required()
                ->unique(ignoreRecord: true)
                ->placeholder('grok | deepseek | openai')
                ->helperText('Utilisé en interne pour sélectionner le provider. Ne pas modifier après création.'),

            TextInput::make('display_name')
                ->label('Nom affiché')
                ->required(),

            TextInput::make('base_url')
                ->label('URL de base API')
                ->required()
                ->url()
                ->placeholder('https://api.x.ai/v1'),

            TextInput::make('api_key')
                ->label('Clé API')
                ->password()
                ->revealable()
                ->helperText('Chiffrée en base. Laissez vide pour conserver la valeur existante.')
                ->dehydrateStateUsing(fn ($state) => filled($state) ? $state : null)
                ->dehydrated(fn ($state) => filled($state)),

            TextInput::make('priority')
                ->label('Priorité de fallback')
                ->numeric()
                ->default(1)
                ->minValue(1)
                ->helperText('1 = priorité haute. En cas d\'échec, le prochain provider est utilisé.'),

            Toggle::make('is_active')
                ->label('Actif')
                ->default(false),

            KeyValue::make('extra_config')
                ->label('Configuration supplémentaire')
                ->helperText('org_id, headers custom, etc.')
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('display_name')
                    ->label('Provider')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('name')
                    ->label('Identifiant')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('base_url')
                    ->label('URL')
                    ->limit(38)
                    ->color('gray')
                    ->size('sm'),
                TextColumn::make('priority')
                    ->label('Priorité')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                IconColumn::make('api_key_set')
                    ->label('Clé API')
                    ->state(fn ($record) => $record->hasKey())
                    ->icon(fn ($state) => $state ? Heroicon::OutlinedCheckCircle : Heroicon::OutlinedXCircle)
                    ->color(fn ($state) => $state ? 'success' : 'danger'),
                TextColumn::make('models_count')
                    ->counts('models')
                    ->label('Modèles')
                    ->badge()
                    ->color('gray'),
                ToggleColumn::make('is_active')
                    ->label('Actif'),
            ])
            ->defaultSort('priority')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAiProviders::route('/'),
            'create' => Pages\CreateAiProvider::route('/create'),
            'edit'   => Pages\EditAiProvider::route('/{record}/edit'),
        ];
    }
}
