<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Pages\CardSettings;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Widgets\UserStatsWidget;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),

            Action::make('card_settings')
                ->label('Configuration')
                ->icon('heroicon-o-cog-6-tooth')
                ->color('gray')
                ->url(fn (): string => CardSettings::getUrl()),

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
        return [UserStatsWidget::class];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 5;
    }

    /** Builds the export URL forwarding active table filters. */
    private function buildExportUrl(string $format): string
    {
        $params  = [];
        $filters = $this->getTableFiltersForm()->getRawState();

        if (! empty($filters['roles']['value'])) {
            $params['role'] = $filters['roles']['value'];
        }

        if (! empty($filters['actifs']['isActive'])) {
            $params['status'] = 'active';
        } elseif (! empty($filters['suspendus']['isActive'])) {
            $params['status'] = 'suspended';
        } elseif (! empty($filters['bannis']['isActive'])) {
            $params['status'] = 'banned';
        }

        $routeName = $format === 'pdf' ? 'admin.members.pdf' : 'admin.members.excel';

        return route($routeName, $params);
    }
}
