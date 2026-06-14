<?php

declare(strict_types=1);

namespace App\Filament\Company\Resources\CompanyApplicationResource\Pages;

use App\Enums\JobApplicationStatus;
use App\Filament\Company\Resources\CompanyApplicationResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewCompanyApplication extends ViewRecord
{
    protected static string $resource = CompanyApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_cv')
                ->label('Télécharger le CV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->visible(fn () => $this->record->cv_path !== null)
                ->url(fn () => route('company.applications.cv', $this->record))
                ->openUrlInNewTab(),

            Action::make('shortlist')
                ->label('Présélectionner')
                ->icon('heroicon-o-star')
                ->color('primary')
                ->visible(fn () => in_array($this->record->status, [JobApplicationStatus::Pending, JobApplicationStatus::Viewed], true))
                ->action(function (): void {
                    $this->record->update(['status' => 'shortlisted']);
                    Notification::make()->title('Candidature présélectionnée')->success()->send();
                    $this->refreshFormData(['status']);
                }),

            Action::make('accept')
                ->label('Accepter')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->status !== JobApplicationStatus::Accepted)
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->update(['status' => 'accepted']);
                    Notification::make()->title('Candidature acceptée')->success()->send();
                    $this->refreshFormData(['status']);
                }),

            Action::make('reject')
                ->label('Refuser')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => $this->record->status !== JobApplicationStatus::Rejected)
                ->schema([
                    Textarea::make('employer_note')
                        ->label('Note interne (optionnel)')
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $this->record->update(['status' => 'rejected', 'employer_note' => $data['employer_note'] ?? null]);
                    Notification::make()->title('Candidature refusée')->warning()->send();
                    $this->refreshFormData(['status']);
                }),
        ];
    }
}
