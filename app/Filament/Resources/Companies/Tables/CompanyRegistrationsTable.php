<?php

declare(strict_types=1);

namespace App\Filament\Resources\Companies\Tables;

use App\Models\CompanyRegistrationRequest;
use App\Models\User;
use App\Services\Company\CompanyAccountService;
use Filament\Actions\Action as TableAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CompanyRegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_name')
                    ->label('Entreprise')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('first_name')
                    ->label('Responsable')
                    ->formatStateUsing(fn ($state, CompanyRegistrationRequest $r): string => "{$r->first_name} {$r->last_name}")
                    ->searchable(['first_name', 'last_name']),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('business_domain')
                    ->label("Domaine d'activité")
                    ->limit(30),

                TextColumn::make('country')
                    ->label('Pays')
                    ->placeholder('-'),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'  => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending'  => 'En attente',
                        'approved' => 'Approuvée',
                        'rejected' => 'Refusée',
                        default    => $state,
                    }),

                TextColumn::make('created_at')
                    ->label('Soumise le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending'  => 'En attente',
                        'approved' => 'Approuvée',
                        'rejected' => 'Refusée',
                    ]),
            ])

            ->actions([
                ViewAction::make(),

                TableAction::make('approve')
                    ->label('Approuver')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (CompanyRegistrationRequest $r): bool => $r->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Approuver cette demande ?')
                    ->modalDescription('Un compte entreprise sera créé avec un mot de passe temporaire envoyé par email.')
                    ->action(function (CompanyRegistrationRequest $record) {
                        /** @var User $admin */
                        $admin = auth()->user();
                        app(CompanyAccountService::class)->approveRegistrationRequest($admin, $record);

                        Notification::make()
                            ->title('Demande approuvée - accès envoyés par email')
                            ->success()
                            ->send();
                    }),

                TableAction::make('reject')
                    ->label('Refuser')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (CompanyRegistrationRequest $r): bool => $r->status === 'pending')
                    ->schema([
                        Textarea::make('rejection_reason')
                            ->label('Raison du refus')
                            ->required()
                            ->minLength(10)
                            ->rows(3),
                    ])
                    ->action(function (CompanyRegistrationRequest $record, array $data) {
                        /** @var User $admin */
                        $admin = auth()->user();
                        app(CompanyAccountService::class)->rejectRegistrationRequest($admin, $record, $data['rejection_reason']);

                        Notification::make()
                            ->title('Demande refusée')
                            ->warning()
                            ->send();
                    }),
            ])

            ->defaultSort('created_at', 'desc');
    }
}
