<?php

declare(strict_types=1);

namespace App\Filament\Company\Widgets;

use App\Models\JobOffer;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CompanyOffersWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Mes offres d\'emploi';

    public function table(Table $table): Table
    {
        $account = auth('company')->user();

        return $table
            ->query(
                JobOffer::where('company_id', $account?->company_id)
                    ->withCount('applications')
                    ->latest()
                    ->limit(6)
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Intitulé')
                    ->limit(45)
                    ->weight('bold')
                    ->searchable(),

                TextColumn::make('contract_type')
                    ->label('Contrat')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'cdi' => 'CDI', 'cdd' => 'CDD', 'freelance' => 'Freelance',
                        'internship' => 'Stage', 'apprenticeship' => 'Alternance', default => $state,
                    }),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'active'   => 'success',
                        'pending'  => 'warning',
                        'expired'  => 'danger',
                        'filled'   => 'info',
                        'rejected' => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'active'   => 'Active',
                        'pending'  => 'En validation',
                        'expired'  => 'Expirée',
                        'filled'   => 'Pourvue',
                        'rejected' => 'Refusée',
                        default    => ucfirst($state),
                    }),

                TextColumn::make('applications_count')
                    ->label('Candidatures')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'gray'),

                TextColumn::make('views_count')
                    ->label('Vues')
                    ->sortable()
                    ->color('gray'),

                IconColumn::make('is_remote')
                    ->label('Remote')
                    ->boolean(),

                TextColumn::make('expires_at')
                    ->label('Expire le')
                    ->dateTime('d/m/Y')
                    ->color('gray')
                    ->placeholder('—'),
            ])
            ->actions([
                \Filament\Actions\Action::make('applications')
                    ->label('Candidatures')
                    ->icon('heroicon-o-users')
                    ->url(fn (JobOffer $r) => \App\Filament\Company\Resources\CompanyApplicationResource::getUrl('index') . '?tableFilters[job_offer_id][value]=' . $r->id)
                    ->color('primary'),
            ])
            ->paginated(false);
    }
}
