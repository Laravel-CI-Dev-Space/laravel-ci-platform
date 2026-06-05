<?php

declare(strict_types=1);

namespace App\Filament\Company\Resources\CompanyApplicationResource\Pages;

use App\Filament\Company\Resources\CompanyApplicationResource;
use Filament\Resources\Pages\ViewRecord;

class ViewCompanyApplication extends ViewRecord
{
    protected static string $resource = CompanyApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('download_cv')
                ->label('Télécharger le CV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->visible(fn () => $this->record->cv_path !== null)
                ->url(fn () => asset('assets/cv/' . $this->record->cv_path))
                ->openUrlInNewTab(),

            \Filament\Actions\Action::make('shortlist')
                ->label('Présélectionner')
                ->icon('heroicon-o-star')
                ->color('primary')
                ->visible(fn () => in_array($this->record->status, ['pending', 'viewed']))
                ->action(function (): void {
                    $this->record->update(['status' => 'shortlisted']);
                    \Filament\Notifications\Notification::make()->title('Candidature présélectionnée')->success()->send();
                    $this->refreshFormData(['status']);
                }),

            \Filament\Actions\Action::make('accept')
                ->label('Accepter')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => $this->record->status !== 'accepted')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->record->update(['status' => 'accepted']);
                    \Filament\Notifications\Notification::make()->title('Candidature acceptée')->success()->send();
                    $this->refreshFormData(['status']);
                }),

            \Filament\Actions\Action::make('reject')
                ->label('Refuser')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => $this->record->status !== 'rejected')
                ->schema([
                    \Filament\Forms\Components\Textarea::make('employer_note')
                        ->label('Note interne (optionnel)')
                        ->rows(3),
                ])
                ->action(function (array $data): void {
                    $this->record->update(['status' => 'rejected', 'employer_note' => $data['employer_note'] ?? null]);
                    \Filament\Notifications\Notification::make()->title('Candidature refusée')->warning()->send();
                    $this->refreshFormData(['status']);
                }),
        ];
    }
}
