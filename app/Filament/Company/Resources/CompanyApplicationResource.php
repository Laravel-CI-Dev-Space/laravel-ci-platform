<?php

declare(strict_types=1);

namespace App\Filament\Company\Resources;

use App\Filament\Company\Resources\CompanyApplicationResource\Pages\ListCompanyApplications;
use App\Filament\Company\Resources\CompanyApplicationResource\Pages\ViewCompanyApplication;
use App\Models\JobApplication;
use App\Models\JobOffer;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CompanyApplicationResource extends Resource
{
    protected static ?string $model = JobApplication::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = 'Candidatures reçues';

    protected static ?string $modelLabel = 'Candidature';

    protected static ?string $pluralModelLabel = 'Candidatures reçues';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Recrutement';
    }

    /** Restreint aux candidatures des offres de la company authentifiée. */
    public static function getEloquentQuery(): Builder
    {
        $account    = auth('company')->user();
        $offerIds   = JobOffer::where('company_id', $account?->company_id)->pluck('id');

        return parent::getEloquentQuery()
            ->with(['user', 'jobOffer'])
            ->whereIn('job_offer_id', $offerIds);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Candidat')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('jobOffer.title')
                    ->label('Offre')
                    ->limit(40)
                    ->sortable(),

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
                    ->sortable(),
            ])

            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending'     => 'En attente',
                        'viewed'      => 'Vue',
                        'shortlisted' => 'Présélectionnée',
                        'accepted'    => 'Acceptée',
                        'rejected'    => 'Refusée',
                    ]),

                SelectFilter::make('job_offer_id')
                    ->label('Offre')
                    ->options(function () {
                        $account = auth('company')->user();

                        return JobOffer::where('company_id', $account?->company_id)
                            ->pluck('title', 'id');
                    }),
            ])

            ->actions([
                ViewAction::make(),

                Action::make('shortlist')
                    ->label('Présélectionner')
                    ->icon('heroicon-o-star')
                    ->color('primary')
                    ->visible(fn (JobApplication $r): bool => in_array($r->status, ['pending', 'viewed']))
                    ->action(function (JobApplication $record): void {
                        $record->update(['status' => 'shortlisted']);
                        Notification::make()->title('Candidature présélectionnée')->success()->send();
                    }),

                Action::make('accept')
                    ->label('Accepter')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (JobApplication $r): bool => $r->status !== 'accepted')
                    ->requiresConfirmation()
                    ->action(function (JobApplication $record): void {
                        $record->update(['status' => 'accepted']);
                        Notification::make()->title('Candidature acceptée')->success()->send();
                    }),

                Action::make('reject')
                    ->label('Refuser')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (JobApplication $r): bool => $r->status !== 'rejected')
                    ->schema([
                        Textarea::make('employer_note')
                            ->label('Note interne (optionnel)')
                            ->rows(3),
                    ])
                    ->action(function (JobApplication $record, array $data): void {
                        $record->update([
                            'status'        => 'rejected',
                            'employer_note' => $data['employer_note'] ?? null,
                        ]);
                        Notification::make()->title('Candidature refusée')->warning()->send();
                    }),
            ])

            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompanyApplications::route('/'),
            'view'  => ViewCompanyApplication::route('/{record}'),
        ];
    }
}
