<?php

declare(strict_types=1);

namespace App\Filament\Resources\Companies\Tables;

use App\Enums\CompanyAccountStatus;
use App\Models\CompanyAccount;
use App\Services\Company\CompanyAccountService;
use Filament\Actions\Action as TableAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CompanyAccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')
                    ->label('Entreprise')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('first_name')
                    ->label('Responsable')
                    ->formatStateUsing(fn ($state, CompanyAccount $r): string => $r->fullName())
                    ->searchable(['first_name', 'last_name']),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (CompanyAccountStatus $state): string => $state->color())
                    ->formatStateUsing(fn (CompanyAccountStatus $state): string => $state->label()),

                TextColumn::make('last_login_at')
                    ->label('Dernière connexion')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('Jamais'),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(CompanyAccountStatus::options()),
            ])

            ->actions([
                TableAction::make('suspend')
                    ->label('Suspendre')
                    ->icon('heroicon-o-no-symbol')
                    ->color('warning')
                    ->visible(fn (CompanyAccount $r): bool => $r->isActive())
                    ->schema([
                        Textarea::make('reason')
                            ->label('Raison de la suspension')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (CompanyAccount $record, array $data) {
                        app(CompanyAccountService::class)->suspend($record, $data['reason']);

                        Notification::make()->title('Compte suspendu')->warning()->send();
                    }),

                TableAction::make('reactivate')
                    ->label('Réactiver')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (CompanyAccount $r): bool => $r->isSuspended())
                    ->requiresConfirmation()
                    ->action(function (CompanyAccount $record) {
                        app(CompanyAccountService::class)->reactivate($record);

                        Notification::make()->title('Compte réactivé')->success()->send();
                    }),

                DeleteAction::make()->requiresConfirmation(),
            ])

            ->defaultSort('created_at', 'desc');
    }
}
