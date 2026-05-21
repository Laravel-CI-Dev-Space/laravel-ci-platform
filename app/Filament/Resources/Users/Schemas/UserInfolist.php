<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Profile;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Informations GitHub')
                ->columns(2)
                ->schema([
                    ImageEntry::make('avatar')
                        ->label('Avatar')
                        ->circular(),

                    TextEntry::make('name')
                        ->label('Nom complet'),

                    TextEntry::make('email')
                        ->label('Email')
                        ->copyable(),

                    TextEntry::make('github_username')
                        ->label('GitHub')
                        ->prefix('@')
                        ->url(fn ($record) => $record->githubUrl())
                        ->openUrlInNewTab(),
                ]),

            Section::make('Statut du compte')
                ->columns(2)
                ->schema([
                    TextEntry::make('is_active')
                        ->label('Statut')
                        ->badge()
                        ->formatStateUsing(fn ($state) => $state ? 'Actif' : 'Banni')
                        ->color(fn ($state) => $state ? 'success' : 'danger'),

                    TextEntry::make('suspended_until')
                        ->label('Suspendu jusqu\'au')
                        ->dateTime('d/m/Y à H:i')
                        ->placeholder('Aucune suspension'),

                    TextEntry::make('roles.name')
                        ->label('Rôle')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'super-admin'  => 'danger',
                            'admin'        => 'warning',
                            'moderateur'   => 'info',
                            'membre-actif' => 'success',
                            default        => 'gray',
                        }),

                    TextEntry::make('last_login_at')
                        ->label('Dernière connexion')
                        ->dateTime('d/m/Y à H:i')
                        ->placeholder('Jamais connecté'),

                    TextEntry::make('created_at')
                        ->label('Inscrit le')
                        ->dateTime('d/m/Y'),
                ]),

            Section::make('Profil')
                ->columns(2)
                ->schema([
                    TextEntry::make('profile.pays')
                        ->label('Pays')
                        ->placeholder('—'),

                    TextEntry::make('profile.ville')
                        ->label('Ville')
                        ->placeholder('—'),

                    TextEntry::make('profile.niveau_laravel')
                        ->label('Niveau Laravel')
                        ->formatStateUsing(fn ($state) => Profile::$niveauxLaravel[$state] ?? '—')
                        ->placeholder('—'),

                    TextEntry::make('profile.poste')
                        ->label('Situation')
                        ->formatStateUsing(fn ($state) => Profile::$postes[$state] ?? '—')
                        ->placeholder('—'),
                ]),
        ]);
    }
}
