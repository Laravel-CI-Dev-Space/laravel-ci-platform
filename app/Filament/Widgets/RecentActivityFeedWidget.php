<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity;

class RecentActivityFeedWidget extends BaseWidget
{
    protected static ?string $heading = 'Activité récente';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $pollingInterval = '15s';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole([
            UserRole::SuperAdmin->value,
            UserRole::SuperAdmin->value,
        ]) ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Activity::with('causer')->latest('created_at'))
            ->columns([
                TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('log_name')
                    ->label('Type')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'article'              => 'success',
                        'question'             => 'info',
                        'answer'               => 'warning',
                        'comment'              => 'gray',
                        'user'                 => 'danger',
                        'company_account'      => 'danger',
                        'company_registration' => 'warning',
                        'job_offer'            => 'info',
                        'event'                => 'success',
                        default                => 'gray',
                    }),

                TextColumn::make('event')
                    ->label('Événement')
                    ->badge(),

                TextColumn::make('causer.name')
                    ->label('Auteur')
                    ->placeholder('Système')
                    ->searchable(),

                TextColumn::make('subject_type')
                    ->label('Sur')
                    ->formatStateUsing(fn (?string $state): string => $state ? class_basename($state) : '-'),

                TextColumn::make('description')
                    ->label('Description')
                    ->wrap()
                    ->limit(80),

                TextColumn::make('attribute_changes')
                    ->label('Modifications')
                    ->formatStateUsing(function (Collection|array|null $state): string {
                        $state      = $state instanceof Collection ? $state : collect($state);
                        $attributes = $state->get('attributes', []);

                        if (empty($attributes)) {
                            return '-';
                        }

                        return collect($attributes)
                            ->map(fn ($value, $key) => "{$key}: {$value}")
                            ->implode(', ');
                    })
                    ->wrap()
                    ->limit(100)
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('log_name')
                    ->label('Type')
                    ->options([
                        'article'              => 'Article',
                        'question'             => 'Question',
                        'answer'               => 'Réponse',
                        'comment'              => 'Commentaire',
                        'user'                 => 'Utilisateur',
                        'company_account'      => 'Compte entreprise',
                        'company_registration' => 'Demande entreprise',
                        'job_offer'            => 'Offre d\'emploi',
                        'event'                => 'Événement',
                    ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
