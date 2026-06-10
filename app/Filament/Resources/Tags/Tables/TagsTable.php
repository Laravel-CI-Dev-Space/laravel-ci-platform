<?php

namespace App\Filament\Resources\Tags\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TagsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('color')
                    ->label('')
                    ->copyable(false),

                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->color('gray'),

                TextColumn::make('scope')
                    ->label('Portée')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'forum' => 'info',
                        'blog'  => 'success',
                        'both'  => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'forum' => 'Forum',
                        'blog'  => 'Blog',
                        'both'  => 'Forum & Blog',
                        default => $state,
                    }),

                TextColumn::make('usage_count')
                    ->label('Utilisations')
                    ->sortable()
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('scope')
                    ->label('Portée')
                    ->options([
                        'forum' => 'Forum',
                        'blog'  => 'Blog',
                        'both'  => 'Forum & Blog',
                    ]),
            ])

            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])

            ->defaultSort('usage_count', 'desc');
    }
}
