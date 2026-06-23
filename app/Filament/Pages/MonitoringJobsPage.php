<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\UserRole;
use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\DB;

class MonitoringJobsPage extends Page
{
    protected static BackedEnum|string|null $navigationIcon = Heroicon::OutlinedQueueList;

    protected static ?string $navigationLabel = 'Jobs / File d\'attente';

    protected static ?string $title = 'File d\'attente & Jobs échoués';

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.pages.monitoring-jobs';

    public static function getNavigationGroup(): ?string
    {
        return 'Monitoring';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole([
            UserRole::SuperAdmin->value,
            UserRole::Admin->value,
        ]) ?? false;
    }

    public function getPendingJobsProperty(): array
    {
        return DB::table('jobs')
            ->orderBy('created_at')
            ->limit(200)
            ->get()
            ->map(function (object $job): array {
                $payload = json_decode($job->payload, true) ?? [];
                return [
                    'id'           => $job->id,
                    'queue'        => $job->queue,
                    'class'        => $payload['displayName'] ?? ($payload['job'] ?? 'Unknown'),
                    'attempts'     => $job->attempts,
                    'created_at'   => Carbon::createFromTimestamp($job->created_at)->diffForHumans(),
                    'available_at' => $job->available_at
                        ? Carbon::createFromTimestamp($job->available_at)->diffForHumans()
                        : '—',
                ];
            })
            ->toArray();
    }

    public function getFailedJobsProperty(): array
    {
        return DB::table('failed_jobs')
            ->orderBy('failed_at', 'desc')
            ->limit(200)
            ->get()
            ->map(function (object $job): array {
                $payload = json_decode($job->payload, true) ?? [];
                $exception = $job->exception ?? '';
                $firstLine = trim(strtok($exception, "\n") ?: $exception);
                return [
                    'id'         => $job->id,
                    'uuid'       => $job->uuid,
                    'connection' => $job->connection,
                    'queue'      => $job->queue,
                    'class'      => $payload['displayName'] ?? 'Unknown',
                    'error'      => strlen($firstLine) > 120 ? substr($firstLine, 0, 120) . '…' : $firstLine,
                    'failed_at'  => Carbon::parse($job->failed_at)->diffForHumans(),
                ];
            })
            ->toArray();
    }

    public function retryAll(): void
    {
        \Artisan::call('queue:retry', ['id' => ['all']]);
        $this->dispatch('$refresh');
    }

    public function flushFailed(): void
    {
        DB::table('failed_jobs')->truncate();
        $this->dispatch('$refresh');
    }
}
