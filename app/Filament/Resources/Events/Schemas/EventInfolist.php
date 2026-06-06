<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events\Schemas;

use App\Models\Event;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('Informations générales')
                ->columns(2)
                ->schema([
                    TextEntry::make('title')
                        ->label('Titre')
                        ->columnSpanFull(),

                    TextEntry::make('type')
                        ->label('Type')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'meetup'     => 'warning',
                            'webinar'    => 'info',
                            'hackathon'  => 'purple',
                            'conference' => 'success',
                            'workshop'   => 'cyan',
                            default      => 'gray',
                        })
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'meetup'     => 'Meetup',
                            'webinar'    => 'Webinaire',
                            'hackathon'  => 'Hackathon',
                            'conference' => 'Conférence',
                            'workshop'   => 'Workshop',
                            default      => $state,
                        }),

                    TextEntry::make('status')
                        ->label('Statut')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'draft'     => 'gray',
                            'published' => 'success',
                            'cancelled' => 'danger',
                            'completed' => 'info',
                            default     => 'gray',
                        })
                        ->formatStateUsing(fn (string $state): string => match ($state) {
                            'draft'     => 'Brouillon',
                            'published' => 'Publié',
                            'cancelled' => 'Annulé',
                            'completed' => 'Terminé',
                            default     => $state,
                        }),

                    TextEntry::make('creator.name')
                        ->label('Créé par')
                        ->placeholder('—'),

                    TextEntry::make('slug')
                        ->label('Slug')
                        ->fontFamily('mono'),
                ]),

            Section::make('Date et lieu')
                ->columns(2)
                ->schema([
                    TextEntry::make('starts_at')
                        ->label('Début')
                        ->dateTime('d/m/Y H:i'),

                    TextEntry::make('ends_at')
                        ->label('Fin')
                        ->dateTime('d/m/Y H:i'),

                    TextEntry::make('location')
                        ->label('Lieu physique')
                        ->placeholder('—'),

                    TextEntry::make('online_url')
                        ->label('Lien en ligne')
                        ->placeholder('—'),
                ]),

            Section::make('Tarification')
                ->columns(2)
                ->schema([
                    TextEntry::make('is_paid')
                        ->label('Type d\'accès')
                        ->badge()
                        ->formatStateUsing(fn (bool $state): string => $state ? 'Payant' : 'Gratuit')
                        ->color(fn (bool $state): string => $state ? 'warning' : 'success'),

                    TextEntry::make('price')
                        ->label('Prix')
                        ->placeholder('—')
                        ->visible(fn (Event $record): bool => $record->is_paid)
                        ->formatStateUsing(fn (Event $record): string => $record->price !== null
                            ? number_format((float) $record->price, 0, ',', ' ') . ' ' . ($record->currency ?? 'XOF')
                            : '—'),

                    TextEntry::make('promo_code')
                        ->label('Code promo')
                        ->placeholder('Aucun')
                        ->fontFamily('mono')
                        ->visible(fn (Event $record): bool => $record->is_paid),

                    TextEntry::make('promo_discount_value')
                        ->label('Réduction')
                        ->placeholder('—')
                        ->visible(fn (Event $record): bool => $record->is_paid && $record->promo_code !== null)
                        ->formatStateUsing(fn (Event $record): string => $record->promo_discount_value !== null
                            ? ($record->promo_discount_type === 'percent'
                                ? $record->promo_discount_value . '%'
                                : number_format((float) $record->promo_discount_value, 0, ',', ' ') . ' ' . ($record->currency ?? 'XOF'))
                            : '—'),

                    TextEntry::make('promo_expires_at')
                        ->label('Expiration du code')
                        ->placeholder('Pas d\'expiration')
                        ->dateTime('d/m/Y H:i')
                        ->visible(fn (Event $record): bool => $record->is_paid && $record->promo_code !== null),

                    TextEntry::make('promo_uses_count')
                        ->label('Utilisations du code')
                        ->visible(fn (Event $record): bool => $record->is_paid && $record->promo_code !== null)
                        ->formatStateUsing(fn (Event $record): string => $record->promo_max_uses !== null
                            ? "{$record->promo_uses_count} / {$record->promo_max_uses}"
                            : (string) $record->promo_uses_count),
                ]),

            Section::make('Ticketerie & inscriptions externes')
                ->columns(2)
                ->schema([
                    TextEntry::make('ticketing_enabled')
                        ->label('Ticketerie')
                        ->badge()
                        ->formatStateUsing(fn (bool $state): string => $state ? 'Activée' : 'Désactivée')
                        ->color(fn (bool $state): string => $state ? 'success' : 'gray'),

                    TextEntry::make('ticket_prefix')
                        ->label('Préfixe de ticket')
                        ->placeholder('LCI (défaut)')
                        ->fontFamily('mono')
                        ->visible(fn (Event $record): bool => $record->ticketing_enabled),

                    TextEntry::make('guest_registration_enabled')
                        ->label('Inscriptions sans compte')
                        ->badge()
                        ->formatStateUsing(fn (bool $state): string => $state ? 'Activées' : 'Désactivées')
                        ->color(fn (bool $state): string => $state ? 'info' : 'gray'),
                ]),

            Section::make('Inscriptions')
                ->columns(2)
                ->schema([
                    TextEntry::make('registrations_count')
                        ->label('Inscrits confirmés'),

                    TextEntry::make('capacity')
                        ->label('Capacité')
                        ->placeholder('Illimité'),

                    TextEntry::make('waitlist_enabled')
                        ->label('Liste d\'attente')
                        ->badge()
                        ->formatStateUsing(fn (bool $state): string => $state ? 'Activée' : 'Désactivée')
                        ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
                ]),

            Section::make('Image')
                ->schema([
                    ImageEntry::make('cover_image')
                        ->label('')
                        ->disk('assets')
                        ->imageHeight(200)
                        ->columnSpanFull()
                        ->visible(fn (Event $record): bool => $record->cover_image !== null),
                ]),

            Section::make('Raison d\'annulation')
                ->visible(fn (Event $record): bool => $record->status === 'cancelled')
                ->schema([
                    TextEntry::make('cancellation_reason')
                        ->label('')
                        ->columnSpanFull()
                        ->placeholder('Aucune raison renseignée.'),
                ]),

            Section::make('Description')
                ->schema([
                    TextEntry::make('description')
                        ->label('')
                        ->html()
                        ->columnSpanFull(),
                ]),

            Section::make('Programme')
                ->visible(fn (Event $record): bool => filled($record->program))
                ->schema([
                    TextEntry::make('program')
                        ->label('')
                        ->html()
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
