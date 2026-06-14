<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Tables;

use App\Enums\MediaType;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class AboutOriginSectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('media_type')
                    ->label('Média')
                    ->badge()
                    ->color(fn (MediaType $state): string => match ($state) {
                        MediaType::Image   => 'success',
                        MediaType::Video   => 'info',
                        MediaType::Youtube => 'danger',
                        default            => 'gray',
                    })
                    ->formatStateUsing(fn (MediaType $state): string => $state->label()),

                TextColumn::make('media_position')
                    ->label('Position')
                    ->formatStateUsing(fn (string $state): string => $state === 'left' ? 'Gauche' : 'Droite')
                    ->color('gray'),

                ToggleColumn::make('is_active')
                    ->label('Actif')
                    ->alignCenter(),
            ])

            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
