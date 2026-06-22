<?php

declare(strict_types=1);

namespace App\Filament\Resources\Chat;

use App\Filament\Resources\Chat\AiUserAssignmentResource\Pages;
use App\Models\Chat\AiModel;
use App\Models\Chat\AiUserAssignment;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AiUserAssignmentResource extends Resource
{
    protected static ?string $model = AiUserAssignment::class;
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;
    protected static ?int $navigationSort = 4;
    protected static ?string $modelLabel = 'Assignation';
    protected static ?string $pluralModelLabel = 'Assignations modèle / utilisateur';

    public static function getNavigationGroup(): string { return 'Assistant IA'; }
    public static function getNavigationLabel(): string { return 'Assignations'; }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('model_id')
                ->label('Modèle IA')
                ->options(
                    AiModel::with('provider')
                        ->where('is_active', true)
                        ->get()
                        ->mapWithKeys(fn ($m) => [$m->id => $m->fullName()])
                )
                ->required()
                ->searchable(),

            Select::make('user_id')
                ->label('Utilisateur (optionnel)')
                ->options(User::pluck('name', 'id'))
                ->searchable()
                ->nullable()
                ->helperText('Laisser vide pour une règle par rôle ou globale.'),

            Select::make('role')
                ->label('Rôle (optionnel)')
                ->options([
                    'member'      => 'Membre',
                    'moderator'   => 'Modérateur',
                    'admin'       => 'Administrateur',
                    'super-admin' => 'Super Admin',
                ])
                ->nullable()
                ->helperText('Si renseigné, s\'applique à tous les users de ce rôle (sauf assignation individuelle).'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Utilisateur')->default('— Tous —')->searchable(),
                TextColumn::make('role')->label('Rôle')->default('— Global —')->badge()->color('info'),
                TextColumn::make('model.display_name')->label('Modèle assigné'),
                TextColumn::make('model.provider.display_name')->label('Provider')->badge(),
                TextColumn::make('assignedBy.name')->label('Assigné par'),
                TextColumn::make('created_at')->label('Date')->since()->sortable(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAiUserAssignments::route('/'),
            'create' => Pages\CreateAiUserAssignment::route('/create'),
            'edit'   => Pages\EditAiUserAssignment::route('/{record}/edit'),
        ];
    }
}
