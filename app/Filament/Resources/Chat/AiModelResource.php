<?php

declare(strict_types=1);

namespace App\Filament\Resources\Chat;

use App\Filament\Resources\Chat\AiModelResource\Pages;
use App\Models\Chat\AiModel;
use App\Models\Chat\AiProvider;
use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AiModelResource extends Resource
{
    protected static ?string $model = AiModel::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Modèle IA';
    protected static ?string $pluralModelLabel = 'Modèles IA';

    public static function getNavigationGroup(): string { return 'Assistant IA'; }
    public static function getNavigationLabel(): string { return 'Modèles'; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('provider_id')
                ->label('Provider')
                ->options(AiProvider::pluck('display_name', 'id'))
                ->required()
                ->searchable(),

            TextInput::make('model_name')
                ->label('Identifiant du modèle')
                ->required()
                ->placeholder('grok-3 | deepseek-chat | gpt-4o'),

            TextInput::make('display_name')
                ->label('Nom affiché')
                ->required(),

            TextInput::make('max_tokens')
                ->label('Max tokens par réponse')
                ->numeric()
                ->default(4096)
                ->minValue(256),

            TextInput::make('cost_input_per_1k')
                ->label('Coût entrée / 1K tokens ($)')
                ->numeric()
                ->step(0.000001)
                ->default(0),

            TextInput::make('cost_output_per_1k')
                ->label('Coût sortie / 1K tokens ($)')
                ->numeric()
                ->step(0.000001)
                ->default(0),

            Toggle::make('supports_tools')
                ->label('Supporte les tools (function calling)')
                ->default(true),
            Toggle::make('supports_streaming')
                ->label('Supporte le streaming')
                ->default(true),
            Toggle::make('is_active')
                ->label('Actif')
                ->default(false),
            Toggle::make('is_default')
                ->label('Modèle par défaut global')
                ->helperText('Un seul modèle peut être défini par défaut. Les autres seront désactivés automatiquement.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('provider.display_name')
                    ->label('Provider')
                    ->badge()
                    ->sortable()
                    ->color(fn ($record) => match($record->provider->name) {
                        'grok'     => 'warning',
                        'deepseek' => 'info',
                        'openai'   => 'success',
                        default    => 'gray',
                    }),
                TextColumn::make('display_name')
                    ->label('Modèle')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('model_name')
                    ->label('Identifiant')
                    ->color('gray')
                    ->size('sm'),
                TextColumn::make('cost_input_per_1k')
                    ->label('$/1K in')
                    ->numeric(decimalPlaces: 5)
                    ->color('gray'),
                TextColumn::make('cost_output_per_1k')
                    ->label('$/1K out')
                    ->numeric(decimalPlaces: 5)
                    ->color('gray'),
                IconColumn::make('supports_tools')
                    ->label('Tools')
                    ->boolean(),
                ToggleColumn::make('is_active')
                    ->label('Actif'),
                IconColumn::make('is_default')
                    ->label('Défaut')
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedStar)
                    ->falseIcon(Heroicon::OutlinedMinus)
                    ->trueColor('warning')
                    ->falseColor('gray'),
            ])
            ->filters([
                SelectFilter::make('provider_id')
                    ->label('Provider')
                    ->options(AiProvider::pluck('display_name', 'id')),
            ])
            ->actions([
                Action::make('set_default')
                    ->label('Défaut global')
                    ->icon(Heroicon::OutlinedStar)
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Définir comme modèle par défaut')
                    ->modalDescription('Tous les autres modèles perdront leur statut de défaut global.')
                    ->action(function (AiModel $record) {
                        AiModel::where('id', '!=', $record->id)->update(['is_default' => false]);
                        $record->update(['is_default' => true, 'is_active' => true]);
                        Notification::make()
                            ->title("{$record->display_name} défini comme modèle par défaut")
                            ->success()
                            ->send();
                    })
                    ->hidden(fn (AiModel $record) => $record->is_default),
                EditAction::make(),
                DeleteAction::make()
                    ->requiresConfirmation(),
            ])
            ->defaultSort('provider_id');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAiModels::route('/'),
            'create' => Pages\CreateAiModel::route('/create'),
            'edit'   => Pages\EditAiModel::route('/{record}/edit'),
        ];
    }
}
