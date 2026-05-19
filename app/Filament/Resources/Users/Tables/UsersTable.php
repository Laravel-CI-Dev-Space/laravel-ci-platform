<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action as TableAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(asset('assets/logo.jpeg')),

                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('github_username')
                    ->label('GitHub')
                    ->prefix('@')
                    ->searchable(),

                TextColumn::make('roles.name')
                    ->label('Rôle')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super-admin'  => 'danger',
                        'admin'        => 'warning',
                        'moderateur'   => 'info',
                        'membre-actif' => 'success',
                        default        => 'gray',
                    }),

                ToggleColumn::make('is_active')
                    ->label('Actif')
                    ->onColor('success')
                    ->offColor('danger'),

                TextColumn::make('suspended_until')
                    ->label('Suspendu jusqu\'au')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->color('warning'),

                TextColumn::make('last_login_at')
                    ->label('Dernière connexion')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Jamais')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Inscrit le')
                    ->dateTime('d/m/Y')
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('roles')
                    ->label('Rôle')
                    ->relationship('roles', 'name'),

                Filter::make('actifs')
                    ->label('Membres actifs')
                    ->query(fn (Builder $q) => $q->where('is_active', true)),

                Filter::make('suspendus')
                    ->label('Membres suspendus')
                    ->query(fn (Builder $q) => $q
                        ->where('is_active', true)
                        ->whereNotNull('suspended_until')
                        ->where('suspended_until', '>', now())
                    ),

                Filter::make('bannis')
                    ->label('Membres bannis')
                    ->query(fn (Builder $q) => $q->where('is_active', false)),
            ])

            ->actions([
                ViewAction::make(),
                EditAction::make(),

                TableAction::make('suspend')
                    ->label('Suspendre')
                    ->icon('heroicon-o-clock')
                    ->color('warning')
                    ->visible(fn (User $record) => $record->isActive())
                    ->form([
                        DateTimePicker::make('suspended_until')
                            ->label('Suspendu jusqu\'au')
                            ->required()
                            ->minDate(now()->addDay()),
                    ])
                    ->action(fn (User $record, array $data) => $record->update([
                        'suspended_until' => $data['suspended_until'],
                    ])),

                TableAction::make('unsuspend')
                    ->label('Lever suspension')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (User $record) => $record->isSuspended())
                    ->action(fn (User $record) => $record->update(['suspended_until' => null])),

                TableAction::make('ban')
                    ->label('Bannir')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (User $record) => $record->is_active)
                    ->requiresConfirmation()
                    ->modalHeading('Bannir ce membre ?')
                    ->modalDescription('Cette action désactive définitivement le compte.')
                    ->action(fn (User $record) => $record->update([
                        'is_active'       => false,
                        'suspended_until' => null,
                    ])),

                TableAction::make('reactivate')
                    ->label('Réactiver')
                    ->icon('heroicon-o-arrow-path')
                    ->color('success')
                    ->visible(fn (User $record) => $record->isBanned())
                    ->requiresConfirmation()
                    ->action(fn (User $record) => $record->update(['is_active' => true])),
            ])

            ->defaultSort('created_at', 'desc');
    }
}
