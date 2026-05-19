<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Informations GitHub')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Nom complet')
                        ->required(),

                    TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required(),

                    TextInput::make('github_username')
                        ->label('Username GitHub')
                        ->prefix('@')
                        ->required(),

                    TextInput::make('github_id')
                        ->label('GitHub ID')
                        ->disabled(),
                ]),

            Section::make('Statut du compte')
                ->columns(2)
                ->schema([
                    Toggle::make('is_active')
                        ->label('Compte actif')
                        ->helperText('Désactiver = bannissement définitif')
                        ->onColor('success')
                        ->offColor('danger'),

                    DateTimePicker::make('suspended_until')
                        ->label('Suspendu jusqu\'au')
                        ->helperText('Laisser vide pour aucune suspension')
                        ->nullable(),

                    Select::make('roles')
                        ->label('Rôle')
                        ->relationship('roles', 'name')
                        ->preload()
                        ->searchable(),

                    DateTimePicker::make('last_login_at')
                        ->label('Dernière connexion')
                        ->disabled(),
                ]),
        ]);
    }
}
