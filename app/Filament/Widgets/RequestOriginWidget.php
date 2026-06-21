<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use App\Models\Analytics\PageView;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class RequestOriginWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected static ?string $heading = 'Origine → Destination des requêtes (24h)';

    protected int | string | array $columnSpan = 'full';

    protected ?string $pollingInterval = '60s';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole([
            UserRole::SuperAdmin->value,
            UserRole::Admin->value,
        ]) ?? false;
    }

    protected function getTableQuery(): Builder
    {
        $since = now()->subHours(24);

        $appHost = parse_url(config('app.url'), PHP_URL_HOST) ?? '127.0.0.1';

        // CHAR(63) = '?' — évite l'interprétation PDO du caractère '?' comme paramètre lié
        return PageView::query()
            ->select(
                DB::raw("MD5(CONCAT(COALESCE(referrer,''), COALESCE(route_name,''), path, method)) as id"),
                DB::raw("
                    CASE
                        WHEN referrer IS NULL OR referrer = '' THEN 'Direct / aucun'
                        WHEN referrer LIKE '%{$appHost}%'       THEN 'Interne (site)'
                        ELSE SUBSTRING_INDEX(
                                 SUBSTRING_INDEX(
                                     REPLACE(REPLACE(referrer, 'https://', ''), 'http://', ''),
                                     '/', 1
                                 ),
                                 CHAR(63), 1
                             )
                    END as origin
                "),
                DB::raw('COALESCE(route_name, path) as destination'),
                DB::raw('method'),
                DB::raw('COUNT(*) as hits'),
                DB::raw('ROUND(AVG(duration_ms)) as avg_ms'),
                DB::raw('SUM(CASE WHEN duration_ms > 500 THEN 1 ELSE 0 END) as slow_count'),
            )
            ->where('created_at', '>=', $since)
            ->whereNotNull('duration_ms')
            // referrer doit être dans le GROUP BY (MySQL ONLY_FULL_GROUP_BY)
            ->groupBy('referrer', 'route_name', 'path', 'method')
            ->orderByDesc('hits')
            ->limit(50);
    }

    public function getTableRecordKey(\Illuminate\Database\Eloquent\Model|array $record): string
    {
        $r = is_array($record) ? (object) $record : $record;

        return (string) ($r->id ?? md5(($r->origin ?? '') . ($r->destination ?? '') . ($r->method ?? '')));
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('origin')
                ->label('Origine (Referrer)')
                ->icon('heroicon-m-arrow-right-end-on-rectangle')
                ->badge()
                ->color(fn ($state) => match (true) {
                    str_contains((string) $state, 'Direct')  => 'gray',
                    str_contains((string) $state, 'Interne') => 'info',
                    default                                   => 'warning',
                })
                ->wrap(),

            TextColumn::make('destination')
                ->label('Destination (Route)')
                ->icon('heroicon-m-arrow-left-end-on-rectangle')
                ->wrap(),

            TextColumn::make('method')
                ->label('Méthode')
                ->badge()
                ->color(fn ($state) => match ($state) {
                    'GET'    => 'info',
                    'POST'   => 'success',
                    'PUT',
                    'PATCH'  => 'warning',
                    'DELETE' => 'danger',
                    default  => 'gray',
                })
                ->width('80px')
                ->alignCenter(),

            TextColumn::make('hits')
                ->label('Requêtes')
                ->numeric()
                ->sortable()
                ->alignCenter(),

            TextColumn::make('avg_ms')
                ->label('Latence moy.')
                ->suffix(' ms')
                ->badge()
                ->color(fn ($state) => match (true) {
                    $state <= 150 => 'success',
                    $state <= 500 => 'warning',
                    default       => 'danger',
                })
                ->alignCenter(),

            TextColumn::make('slow_count')
                ->label('Lentes >500ms')
                ->badge()
                ->color(fn ($state) => $state > 0 ? 'warning' : 'success')
                ->alignCenter(),
        ];
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [20, 50];
    }

    protected function getTableEmptyStateHeading(): ?string
    {
        return 'Aucune donnée d\'origine';
    }

    protected function getTableEmptyStateDescription(): ?string
    {
        return 'Les données apparaîtront après les premières requêtes enregistrées avec latence.';
    }
}
