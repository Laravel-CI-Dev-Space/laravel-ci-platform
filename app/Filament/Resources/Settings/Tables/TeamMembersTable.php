<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class TeamMembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order')
                    ->label('#')
                    ->sortable()
                    ->alignCenter()
                    ->width('60px'),

                ImageColumn::make('avatar')
                    ->label('Avatar')
                    ->disk('assets')
                    ->circular()
                    ->height(40)
                    ->defaultImageUrl(fn ($record) => null)
                    ->placeholder(fn ($record) => $record->initials()),

                TextColumn::make('first_name')
                    ->label('Prénom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('last_name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('role')
                    ->label('Rôle')
                    ->color('gray'),

                TextColumn::make('github_url')
                    ->label('GitHub')
                    ->url(fn ($record) => $record->github_url)
                    ->openUrlInNewTab()
                    ->limit(30)
                    ->color('gray'),

                ToggleColumn::make('is_active')
                    ->label('Actif')
                    ->alignCenter(),
            ])

            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])

            ->defaultSort('order')
            ->reorderable('order');
    }
}
