<?php

declare(strict_types=1);

namespace App\Filament\Company\Widgets;

use App\Models\JobApplication;
use App\Models\JobOffer;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class CompanyRecentApplicationsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Candidatures récentes';

    public function table(Table $table): Table
    {
        $account  = auth('company')->user();
        $offerIds = JobOffer::where('company_id', $account?->company_id)->pluck('id');

        return $table
            ->query(
                JobApplication::with(['user', 'jobOffer'])
                    ->whereIn('job_offer_id', $offerIds)
                    ->latest()
                    ->limit(8)
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Candidat')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('jobOffer.title')
                    ->label('Poste')
                    ->limit(35)
                    ->color('gray'),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending'     => 'gray',
                        'viewed'      => 'info',
                        'shortlisted' => 'primary',
                        'accepted'    => 'success',
                        'rejected'    => 'danger',
                        default       => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending'     => 'En attente',
                        'viewed'      => 'Vue',
                        'shortlisted' => 'Présélectionnée',
                        'accepted'    => 'Acceptée',
                        'rejected'    => 'Refusée',
                        default       => ucfirst($state),
                    }),

                TextColumn::make('created_at')
                    ->label('Reçue le')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->color('gray'),
            ])
            ->actions([
                \Filament\Actions\Action::make('view')
                    ->label('Voir')
                    ->icon('heroicon-o-eye')
                    ->url(fn (JobApplication $record) => \App\Filament\Company\Resources\CompanyApplicationResource::getUrl('view', ['record' => $record]))
                    ->color('gray'),
            ])
            ->paginated(false);
    }
}
