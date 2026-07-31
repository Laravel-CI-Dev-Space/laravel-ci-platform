<?php

namespace App\Filament\Resources\Poles\RelationManagers;

use App\Models\PoleMember;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PoleMembersRelationManager extends RelationManager
{
    protected static string $relationship = 'members';

    protected static ?string $title = 'Membres';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('user_id')
                ->label('Compte plateforme')
                ->placeholder('Lier un compte Laravel CI (optionnel)')
                ->relationship('user', 'name')
                ->searchable()
                ->preload(false)
                ->getSearchResultsUsing(
                    fn (string $search) => User::where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->limit(20)
                        ->pluck('name', 'id')
                )
                ->getOptionLabelUsing(fn ($value) => User::find($value)?->name)
                ->nullable()
                ->unique(table: 'pole_members', column: 'user_id', ignoreRecord: true)
                ->helperText('Ce compte recevra automatiquement le rôle de gestionnaire du pôle.'),

            Select::make('role')
                ->label('Rôle dans le pôle')
                ->options([
                    'responsable' => 'Responsable',
                    'adjoint'     => 'Adjoint',
                    'membre'      => 'Membre',
                ])
                ->default('membre')
                ->required()
                ->helperText('1 responsable max · 2 adjoints max par pôle'),

            TextInput::make('first_name')
                ->label('Prénom')
                ->required()
                ->maxLength(100),

            TextInput::make('last_name')
                ->label('Nom')
                ->required()
                ->maxLength(100),

            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->maxLength(150),

            TextInput::make('poste')
                ->label('Titre / Spécialité')
                ->maxLength(150)
                ->placeholder('ex: Développeur senior, Designer UI'),

            Select::make('status')
                ->label('Statut')
                ->options([
                    'actif'   => 'Actif',
                    'inactif' => 'Inactif',
                ])
                ->default('actif')
                ->required(),

            TextInput::make('order')
                ->label('Ordre d\'affichage')
                ->numeric()
                ->default(0),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Nom complet')
                    ->getStateUsing(fn ($record) => $record->fullName())
                    ->searchable(['first_name', 'last_name']),

                TextColumn::make('user.name')
                    ->label('Compte plateforme')
                    ->placeholder('Non lié')
                    ->badge()
                    ->color('success'),

                TextColumn::make('role')
                    ->label('Rôle')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'responsable' => 'warning',
                        'adjoint'     => 'info',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'responsable' => 'Responsable',
                        'adjoint'     => 'Adjoint',
                        default       => 'Membre',
                    }),

                TextColumn::make('poste')
                    ->label('Spécialité')
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('email')
                    ->label('Email')
                    ->copyable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'actif'   => 'success',
                        'inactif' => 'danger',
                        default   => 'gray',
                    }),

                TextColumn::make('order')
                    ->label('Ordre')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon(Heroicon::OutlinedPlus)
                    ->before(function (array $data, CreateAction $action) {
                        $this->enforceRoleLimit($data['role'] ?? 'membre', null, $action);
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->before(function (array $data, PoleMember $record, EditAction $action) {
                        $this->enforceRoleLimit($data['role'] ?? 'membre', $record, $action);
                    }),
                DeleteAction::make(),
            ])
            ->defaultSort('order');
    }

    private function enforceRoleLimit(string $role, ?PoleMember $record, mixed $action): void
    {
        $poleId = $this->getOwnerRecord()->id;

        // Max 3 membres par pôle
        if (! $record) {
            $total = PoleMember::where('pole_id', $poleId)->count();
            if ($total >= 3) {
                Notification::make()
                    ->danger()
                    ->title('Pôle complet')
                    ->body('Un pôle ne peut pas avoir plus de 3 membres.')
                    ->send();
                $action->halt();
                return;
            }
        }

        // 1 responsable max · 2 adjoints max
        [$max, $label] = match ($role) {
            'responsable' => [1, '1 responsable'],
            'adjoint'     => [2, '2 adjoints'],
            default       => [PHP_INT_MAX, ''],
        };

        if ($max === PHP_INT_MAX) {
            return;
        }

        $count = PoleMember::where('pole_id', $poleId)
            ->where('role', $role)
            ->when($record, fn ($q) => $q->where('id', '!=', $record->id))
            ->count();

        if ($count >= $max) {
            Notification::make()
                ->danger()
                ->title('Limite atteinte')
                ->body("Ce pôle accepte au maximum {$label}.")
                ->send();
            $action->halt();
        }
    }
}
