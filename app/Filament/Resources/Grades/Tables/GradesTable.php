<?php

declare(strict_types=1);

namespace App\Filament\Resources\Grades\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GradesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('color')
                    ->label('')
                    ->copyable(false),

                TextColumn::make('icon')
                    ->label('')
                    ->formatStateUsing(fn (string $state): string => '<i class="' . e($state) . '" style="font-size:1.25rem"></i>')
                    ->html(),

                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('min_points')
                    ->label('Points minimum')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('order')
                    ->label('Ordre')
                    ->sortable(),

                TextColumn::make('profiles_count')
                    ->label('Membres')
                    ->counts('profiles')
                    ->alignCenter(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('order');
    }
}
