<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class CommunityValuesTable
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

                TextColumn::make('icon')
                    ->label('Icône')
                    ->fontFamily('mono')
                    ->color('gray'),

                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Description')
                    ->limit(80)
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
