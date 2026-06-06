<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static bool $isDiscovered = false;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $total  = User::count();
        $active = User::where('is_active', true)
            ->where(fn ($q) => $q->whereNull('suspended_until')->orWhere('suspended_until', '<=', now()))
            ->count();
        $suspended = User::where('is_active', true)
            ->whereNotNull('suspended_until')
            ->where('suspended_until', '>', now())
            ->count();
        $banned       = User::where('is_active', false)->count();
        $newThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $newLastMonth = User::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        $trend = $newLastMonth > 0 ? round((($newThisMonth - $newLastMonth) / $newLastMonth) * 100) : 0;

        return [
            Stat::make('Membres total', number_format($total))
                ->description("{$active} actifs · {$banned} bannis")
                ->color('primary')
                ->icon('heroicon-o-users'),

            Stat::make('Actifs', number_format($active))
                ->description('Comptes en règle')
                ->color('success')
                ->icon('heroicon-o-check-circle'),

            Stat::make('Suspendus', $suspended)
                ->description('Suspension temporaire en cours')
                ->color($suspended > 0 ? 'warning' : 'gray')
                ->icon('heroicon-o-clock'),

            Stat::make('Bannis', $banned)
                ->description('Comptes désactivés définitivement')
                ->color($banned > 0 ? 'danger' : 'gray')
                ->icon('heroicon-o-x-circle'),

            Stat::make('Nouveaux ce mois', $newThisMonth)
                ->description(($trend >= 0 ? '+' : '') . $trend . '% vs mois dernier')
                ->color($trend >= 0 ? 'success' : 'warning')
                ->icon('heroicon-o-user-plus'),
        ];
    }
}
