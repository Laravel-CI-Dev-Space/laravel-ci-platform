<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\UserRole;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Throwable;

class ServerHealthWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected ?string $pollingInterval = '30s';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole([
            UserRole::SuperAdmin->value,
            UserRole::Admin->value,
        ]) ?? false;
    }

    protected function getStats(): array
    {
        // Mis en cache pour éviter de recalculer charge/disque/jobs à chaque
        // poll (toutes les 30s) sur tous les onglets admin ouverts.
        [$load1, $cpuCount, $loadRatio, $diskUsedPct, $diskTotal, $diskFree, $failedJobs, $pendingJobs, $dbStatus, $dbColor] = Cache::remember(
            'server_health_widget_stats',
            now()->addSeconds(15),
            function (): array {
                $cpuCount  = max((int) (shell_exec('nproc') ?: 1), 1);
                $load      = sys_getloadavg();
                $load1     = $load[0] ?? 0.0;
                $loadRatio = $load1 / $cpuCount;

                $diskFree    = disk_free_space(base_path()) ?: 0;
                $diskTotal   = disk_total_space(base_path()) ?: 1;
                $diskUsedPct = (int) round((($diskTotal - $diskFree) / $diskTotal) * 100);

                $failedJobs  = DB::table('failed_jobs')->count();
                $pendingJobs = DB::table('jobs')->count();

                try {
                    DB::select('select 1');
                    $dbStatus = 'OK';
                    $dbColor  = 'success';
                } catch (Throwable) {
                    $dbStatus = 'Erreur';
                    $dbColor  = 'danger';
                }

                return [$load1, $cpuCount, $loadRatio, $diskUsedPct, $diskTotal, $diskFree, $failedJobs, $pendingJobs, $dbStatus, $dbColor];
            }
        );

        return [
            Stat::make('Charge serveur (1 min)', number_format($load1, 2))
                ->description($cpuCount . ' CPU(s) · ' . round($loadRatio * 100) . '% utilisation')
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color($loadRatio > 1 ? 'danger' : ($loadRatio > 0.7 ? 'warning' : 'success')),

            Stat::make('Espace disque utilisé', $diskUsedPct . '%')
                ->description(self::formatBytes($diskTotal - $diskFree) . ' / ' . self::formatBytes($diskTotal))
                ->descriptionIcon('heroicon-m-circle-stack')
                ->color($diskUsedPct > 90 ? 'danger' : ($diskUsedPct > 75 ? 'warning' : 'success')),

            Stat::make('Base de données', $dbStatus)
                ->description(config('database.default') . ' · connexion')
                ->descriptionIcon('heroicon-m-server')
                ->color($dbColor),

            Stat::make('File d\'attente (jobs)', number_format($pendingJobs))
                ->description($failedJobs . ' job(s) échoué(s)')
                ->descriptionIcon('heroicon-m-queue-list')
                ->color($failedJobs > 0 ? 'danger' : 'success')
                ->extraAttributes([
                    'wire:click' => '$dispatch("monitoring-open", { type: "jobs" })',
                    'style'      => 'cursor:pointer',
                ]),

            Stat::make('PHP / Laravel', PHP_VERSION)
                ->description('Laravel ' . app()->version())
                ->descriptionIcon('heroicon-m-code-bracket')
                ->color('gray'),

            Stat::make('Environnement', config('app.env'))
                ->description('Debug ' . (config('app.debug') ? 'activé' : 'désactivé'))
                ->descriptionIcon('heroicon-m-cog-6-tooth')
                ->color(config('app.debug') ? 'warning' : 'success'),
        ];
    }

    private static function formatBytes(int|float $bytes): string
    {
        $units = ['o', 'Ko', 'Mo', 'Go', 'To'];
        $i     = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 1) . ' ' . $units[$i];
    }
}
