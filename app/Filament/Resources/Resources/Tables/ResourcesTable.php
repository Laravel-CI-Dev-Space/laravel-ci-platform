<?php

declare(strict_types=1);

namespace App\Filament\Resources\Resources\Tables;

use App\Enums\ResourceType;
use App\Models\Resource as ResourceModel;
use App\Services\Blog\ResourceService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ResourcesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(fn (ResourceModel $record): string => $record->title),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (ResourceType $state): string => $state->color())
                    ->formatStateUsing(fn (ResourceType $state): string => $state->label()),

                TextColumn::make('user.name')
                    ->label('Déposé par')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('file_size')
                    ->label('Taille')
                    ->formatStateUsing(fn (mixed $_, ResourceModel $record): string => $record->fileSizeHuman())
                    ->sortable(),

                TextColumn::make('downloads_count')
                    ->label('Téléchargements')
                    ->sortable(),

                ToggleColumn::make('is_public')
                    ->label('Public')
                    ->onColor('success')
                    ->offColor('danger'),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])

            ->actions([
                ViewAction::make()
                    ->url(fn (ResourceModel $record): string => route('resources.download', $record))
                    ->openUrlInNewTab(),

                DeleteAction::make()
                    ->requiresConfirmation()
                    ->using(fn (ResourceModel $record) => app(ResourceService::class)->deleteResource($record)),
            ])

            ->defaultSort('created_at', 'desc');
    }
}
