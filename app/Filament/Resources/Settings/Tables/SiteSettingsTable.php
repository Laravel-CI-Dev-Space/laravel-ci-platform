<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SiteSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->label('Clé')
                    ->searchable()
                    ->sortable()
                    ->fontFamily('mono')
                    ->copyable(),

                TextColumn::make('group')
                    ->label('Groupe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'general'       => 'gray',
                        'identity'      => 'primary',
                        'home'          => 'info',
                        'about'         => 'success',
                        'footer'        => 'warning',
                        'social'        => 'danger',
                        'seo'           => 'warning',
                        'notifications' => 'success',
                        default         => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'general'       => 'Général',
                        'identity'      => 'Identité & Logos',
                        'home'          => 'Accueil',
                        'about'         => 'À propos',
                        'footer'        => 'Footer',
                        'social'        => 'Réseaux sociaux',
                        'seo'           => 'SEO',
                        'notifications' => 'Notifications',
                        default         => $state,
                    }),

                TextColumn::make('label')
                    ->label('Libellé')
                    ->searchable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('value')
                    ->label('Valeur')
                    ->limit(50)
                    ->color('gray'),

                TextColumn::make('updated_at')
                    ->label('Mis à jour')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('group')
                    ->label('Groupe')
                    ->options([
                        'general'       => 'Général',
                        'identity'      => 'Identité & Logos',
                        'home'          => 'Accueil',
                        'about'         => 'À propos',
                        'footer'        => 'Footer',
                        'social'        => 'Réseaux sociaux',
                        'seo'           => 'SEO',
                        'notifications' => 'Notifications',
                    ]),
            ])

            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])

            ->defaultSort('group')
            ->striped();
    }
}
