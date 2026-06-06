<?php

declare(strict_types=1);

namespace App\Filament\Resources\Settings\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PartnersTable
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

                ImageColumn::make('logo')
                    ->label('Logo')
                    ->disk('assets')
                    ->height(32)
                    ->defaultImageUrl(fn ($record) => null)
                    ->placeholder('-'),

                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'community'     => 'success',
                        'sponsor'       => 'warning',
                        'institutional' => 'info',
                        default         => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'community'     => 'Communauté',
                        'sponsor'       => 'Sponsor',
                        'institutional' => 'Institutionnel',
                        default         => $state,
                    }),

                TextColumn::make('url')
                    ->label('URL')
                    ->url(fn ($record) => $record->url)
                    ->openUrlInNewTab()
                    ->limit(40)
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
