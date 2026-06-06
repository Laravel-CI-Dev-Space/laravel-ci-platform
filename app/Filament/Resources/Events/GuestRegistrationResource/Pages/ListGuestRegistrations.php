<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events\GuestRegistrationResource\Pages;

use App\Filament\Resources\Events\GuestRegistrationResource;
use App\Filament\Widgets\RegistrationStatsWidget;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListGuestRegistrations extends ListRecords
{
    protected static string $resource = GuestRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_excel')
                ->label('Exporter Excel')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->url(fn (): string => $this->buildExportUrl('excel'))
                ->openUrlInNewTab(),

            Action::make('export_pdf')
                ->label('Exporter PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->url(fn (): string => $this->buildExportUrl('pdf'))
                ->openUrlInNewTab(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [RegistrationStatsWidget::class];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 5;
    }

    /** Builds the export URL forwarding active table filters. */
    private function buildExportUrl(string $format): string
    {
        $params = [];

        $filters = $this->getTableFiltersForm()->getRawState();

        if (! empty($filters['event_id']['value'])) {
            $params['event_id'] = $filters['event_id']['value'];
        }

        if (! empty($filters['participant_type']['value'])) {
            $params['type'] = $filters['participant_type']['value'];
        }

        if (! empty($filters['status']['value'])) {
            $params['status'] = $filters['status']['value'];
        }

        $routeName = $format === 'pdf' ? 'admin.registrations.pdf' : 'admin.registrations.excel';

        return route($routeName, $params);
    }
}
