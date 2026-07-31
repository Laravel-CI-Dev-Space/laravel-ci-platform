<?php

namespace App\Filament\Resources\MemberCards\Tables;

use App\Models\MemberCard;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MemberCardsTable
{
    public static function configure(Table $table): Table
    {
        $levelNames = config('member-card.level_names', [1 => 'Initié', 2 => 'Bâtisseur', 3 => 'Maître Artisan']);

        return $table
            ->columns([
                ImageColumn::make('user.avatar')
                    ->label('Avatar')
                    ->circular()
                    ->size(36),

                TextColumn::make('user.name')
                    ->label('Membre')
                    ->searchable()
                    ->sortable()
                    ->description(fn (MemberCard $r) => '@' . $r->user->github_username),

                TextColumn::make('user.matricule')
                    ->label('Matricule')
                    ->fontFamily('mono')
                    ->copyable()
                    ->searchable(),

                TextColumn::make('level')
                    ->label('Niveau')
                    ->formatStateUsing(fn (int $state) => $levelNames[$state] ?? "Niveau {$state}")
                    ->badge()
                    ->color(fn (int $state) => match ($state) {
                        1 => 'warning',
                        2 => 'gray',
                        3 => 'success',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('poste')
                    ->label('Poste')
                    ->default('-')
                    ->searchable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                IconColumn::make('forced_by_admin')
                    ->label('Forcée')
                    ->boolean()
                    ->trueColor('info')
                    ->trueIcon('heroicon-o-shield-check'),

                TextColumn::make('activated_at')
                    ->label('Activée le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('user.profile.points')
                    ->label('Réputation')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('activated_at', 'desc')
            ->filters([
                SelectFilter::make('level')
                    ->label('Niveau')
                    ->options($levelNames),

                TernaryFilter::make('is_active')
                    ->label('Statut')
                    ->trueLabel('Actives seulement')
                    ->falseLabel('Inactives seulement'),

                TernaryFilter::make('forced_by_admin')
                    ->label('Forcée par admin'),
            ])
            ->recordActions([
                Action::make('activate')
                    ->label('Activer')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (MemberCard $r) => ! $r->is_active)
                    ->action(fn (MemberCard $r) => $r->activate(auth()->id())),

                Action::make('force_activate')
                    ->label('Forcer activation')
                    ->icon('heroicon-o-shield-check')
                    ->color('info')
                    ->visible(fn (MemberCard $r) => ! $r->is_active)
                    ->requiresConfirmation()
                    ->modalHeading('Forcer l\'activation')
                    ->modalDescription('Activer cette carte même si le membre n\'a pas atteint le seuil de réputation requis.')
                    ->action(fn (MemberCard $r) => $r->activate(auth()->id(), forced: true)),

                Action::make('deactivate')
                    ->label('Désactiver')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (MemberCard $r) => $r->is_active)
                    ->requiresConfirmation()
                    ->action(fn (MemberCard $r) => $r->deactivate()),

                Action::make('preview')
                    ->label('Aperçu')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->url(fn (MemberCard $r) => route('member-card.preview', [$r->user->github_username, $r->level]))
                    ->openUrlInNewTab(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
