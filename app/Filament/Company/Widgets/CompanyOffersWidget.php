<?php

declare(strict_types=1);

namespace App\Filament\Company\Widgets;

use App\Enums\JobContractType;
use App\Enums\JobOfferStatus;
use App\Filament\Company\Resources\CompanyApplicationResource;
use App\Models\JobOffer;
use Filament\Actions\Action;
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
                    ->formatStateUsing(fn (JobContractType $state): string => $state->label()),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (JobOfferStatus $state): string => $state->color())
                    ->formatStateUsing(fn (JobOfferStatus $state): string => match ($state) {
                        JobOfferStatus::Pending => 'En validation',
                        default                 => $state->label(),
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
                Action::make('applications')
                    ->label(fn (JobOffer $r) => $r->applications_count . ' candidature' . ($r->applications_count !== 1 ? 's' : ''))
                    ->icon('heroicon-o-users')
                    ->color(fn (JobOffer $r) => $r->applications_count > 0 ? 'primary' : 'gray')
                    ->url(CompanyApplicationResource::getUrl('index')),
            ])
            ->paginated(false);
    }
}
