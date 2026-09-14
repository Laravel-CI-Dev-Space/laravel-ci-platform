<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events\RelationManagers;

use App\Models\GuestRegistration;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GuestRegistrationsRelationManager extends RelationManager
{
    protected static string $relationship = 'guestRegistrations';

    protected static ?string $title = 'Invités externes (Luma / sans compte)';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')
                    ->label('Prénom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('last_name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->placeholder('—'),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'confirmed'  => 'success',
                        'waitlisted' => 'warning',
                        'cancelled'  => 'danger',
                        default      => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'confirmed'  => 'Confirmé',
                        'waitlisted' => 'Liste d\'attente',
                        'cancelled'  => 'Annulé',
                        default      => ucfirst($state),
                    }),

                TextColumn::make('registered_at')
                    ->label('Inscrit le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('registered_at', 'desc')
            ->searchable()
            ->paginated([25, 50, 100])
            ->emptyStateHeading('Aucun invité externe')
            ->emptyStateDescription('Les inscriptions importées depuis Luma ou via le formulaire invité apparaîtront ici.');
    }
}
