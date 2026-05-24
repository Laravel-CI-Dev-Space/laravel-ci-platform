<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Http;

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
                        ->required()
                        ->suffixAction(
                            Action::make('resolveGithubId')
                                ->icon('heroicon-o-magnifying-glass')
                                ->tooltip('Récupérer le GitHub ID depuis ce username')
                                ->action(function (Get $get, Set $set) {
                                    $username = trim((string) $get('github_username'));

                                    if (! $username) {
                                        Notification::make()
                                            ->title('Entrez un username GitHub d\'abord')
                                            ->warning()
                                            ->send();

                                        return;
                                    }

                                    $response = Http::withHeaders([
                                        'Accept'     => 'application/vnd.github.v3+json',
                                        'User-Agent' => 'LaravelCI-Admin',
                                    ])->get("https://api.github.com/users/{$username}");

                                    if ($response->successful()) {
                                        $githubId = (string) $response->json('id');
                                        $set('github_id', $githubId);

                                        Notification::make()
                                            ->title("GitHub ID résolu : {$githubId}")
                                            ->success()
                                            ->send();
                                    } else {
                                        Notification::make()
                                            ->title("Utilisateur GitHub « {$username} » introuvable")
                                            ->danger()
                                            ->send();
                                    }
                                })
                        ),

                    TextInput::make('github_id')
                        ->label('GitHub ID')
                        ->placeholder('Cliquer sur 🔍 pour résoudre depuis le username')
                        ->required()
                        ->unique(ignoreRecord: true),
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
