<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events;

use App\Filament\Resources\Concerns\AuthorizesViaPermission;
use App\Filament\Resources\Events\GuestRegistrationResource\Pages\ListGuestRegistrations;
use App\Models\AllEventRegistration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GuestRegistrationResource extends Resource
{
    use AuthorizesViaPermission;

    protected static ?string $model = AllEventRegistration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Inscriptions';

    protected static ?string $modelLabel = 'Inscription';

    protected static ?string $pluralModelLabel = 'Inscriptions';

    protected static ?string $slug = 'inscriptions';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Événements';
    }

    public static function getNavigationBadge(): ?string
    {
        $total = static::getModel()::count();
        $new   = static::getModel()::where('registered_at', '>=', now()->subDays(7))->count();
        return $new > 0 ? "{$total} +{$new}" : (string) $total;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $new = static::getModel()::where('registered_at', '>=', now()->subDays(7))->count();
        return $new > 0 ? 'warning' : 'gray';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                AllEventRegistration::query()
                    ->with('event')
                    ->orderBy('registered_at', 'desc')
            )
            ->columns([
                ImageColumn::make('photo')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(fn (AllEventRegistration $record): string => 'https://ui-avatars.com/api/?name=' . urlencode($record->display_name) . '&background=e2e8f0&color=64748b&size=40')
                    ->width(40)
                    ->imageHeight(40),

                TextColumn::make('participant_type')
                    ->label('Type')
                    ->badge()
                    ->state(fn (AllEventRegistration $record): string => $record->isMember() ? 'Membre' : 'Invité')
                    ->color(fn (AllEventRegistration $record): string => $record->isMember() ? 'primary' : 'warning'),

                TextColumn::make('display_name')
                    ->label('Participant')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope'),

                TextColumn::make('whatsapp')
                    ->label('WhatsApp')
                    ->placeholder('—')
                    ->icon('heroicon-o-phone')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('event.title')
                    ->label('Événement')
                    ->searchable()
                    ->sortable()
                    ->limit(30)
                    ->tooltip(fn (AllEventRegistration $record): string => $record->event?->title ?? ''),

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
                        'waitlisted' => 'En attente',
                        'cancelled'  => 'Annulé',
                        default      => $state,
                    }),

                TextColumn::make('payment_status')
                    ->label('Paiement')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid'    => 'success',
                        'pending' => 'warning',
                        'free'    => 'gray',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'paid'    => 'Payé',
                        'pending' => 'En attente',
                        'free'    => 'Gratuit',
                        default   => $state,
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('amount_paid')
                    ->label('Montant')
                    ->placeholder('—')
                    ->formatStateUsing(fn (?string $state, AllEventRegistration $record): string => $state !== null && (float) $state > 0
                        ? number_format((float) $state, 0, ',', ' ') . ' ' . ($record->event?->currency ?? 'XOF')
                        : '—')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('ticket_number')
                    ->label('Ticket')
                    ->placeholder('—')
                    ->fontFamily('mono')
                    ->copyable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('registered_at')
                    ->label('Inscrit le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('event_id')
                    ->label('Événement')
                    ->relationship('event', 'title')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('participant_type')
                    ->label('Type')
                    ->options([
                        'member' => 'Membres',
                        'guest'  => 'Invités',
                    ]),

                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'confirmed'  => 'Confirmé',
                        'waitlisted' => 'En attente',
                        'cancelled'  => 'Annulé',
                    ]),

                SelectFilter::make('payment_status')
                    ->label('Paiement')
                    ->options([
                        'free'    => 'Gratuit',
                        'paid'    => 'Payé',
                        'pending' => 'En attente de paiement',
                    ]),
            ])

            ->defaultSort('registered_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGuestRegistrations::route('/'),
        ];
    }
}
