<?php

namespace App\Filament\Resources\JobApplications\Tables;

use App\Enums\Jobs\JobApplicationStatus;
use App\Filament\Resources\JobApplications\JobApplicationResource;
use App\Filament\Resources\JobApplications\Support\JobApplicationTableActions;
use App\Filament\Support\EnumFormatter;
use App\Filament\Support\FrenchActions;
use App\Models\JobApplication;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class JobApplicationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Reçue le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('jobOffer.title')
                    ->label('Offre')
                    ->searchable()
                    ->sortable()
                    ->limit(35),

                TextColumn::make('jobOffer.company.name')
                    ->label('Entreprise')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('user.name')
                    ->label('Candidat')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),

                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->formatStateUsing(fn ($state) => EnumFormatter::label($state))
                    ->color(fn (JobApplicationStatus $state): string => match ($state) {
                        JobApplicationStatus::PENDING  => 'warning',
                        JobApplicationStatus::ACCEPTED => 'success',
                        JobApplicationStatus::REJECTED => 'danger',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options(JobApplicationStatus::options()),

                SelectFilter::make('job_offer_id')
                    ->label('Offre')
                    ->relationship('jobOffer', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->recordUrl(fn (JobApplication $record): string => JobApplicationResource::getUrl('view', ['record' => $record]))
            ->actions([
                FrenchActions::viewPage(JobApplicationResource::class),

                ...JobApplicationTableActions::reviewActions(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
