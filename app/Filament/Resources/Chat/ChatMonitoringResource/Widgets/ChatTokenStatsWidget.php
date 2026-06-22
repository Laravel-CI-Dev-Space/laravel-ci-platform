<?php

declare(strict_types=1);

namespace App\Filament\Resources\Chat\ChatMonitoringResource\Widgets;

use App\Models\Chat\ChatTokenBudget;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ChatTokenStatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $today     = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $monthStart = Carbon::now()->startOfMonth();

        // ── Tokens aujourd'hui ──────────────────────────────────
        $todayTotal = (int) ChatTokenBudget::whereDate('date', $today)
            ->sum(DB::raw('public_tokens_used + dashboard_tokens_used'));

        $todayPublic = (int) ChatTokenBudget::whereDate('date', $today)
            ->sum('public_tokens_used');

        $todayDashboard = (int) ChatTokenBudget::whereDate('date', $today)
            ->sum('dashboard_tokens_used');

        // ── Tokens cette semaine ────────────────────────────────
        $weekTotal = (int) ChatTokenBudget::whereBetween('date', [$weekStart, $today])
            ->sum(DB::raw('public_tokens_used + dashboard_tokens_used'));

        $prevWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $prevWeekEnd   = Carbon::now()->subWeek()->endOfWeek();
        $prevWeekTotal = (int) ChatTokenBudget::whereBetween('date', [$prevWeekStart, $prevWeekEnd])
            ->sum(DB::raw('public_tokens_used + dashboard_tokens_used'));

        $weekTrend  = $prevWeekTotal > 0
            ? round((($weekTotal - $prevWeekTotal) / $prevWeekTotal) * 100)
            : 0;

        // ── Tokens ce mois ──────────────────────────────────────
        $monthTotal = (int) ChatTokenBudget::whereBetween('date', [$monthStart, $today])
            ->sum(DB::raw('public_tokens_used + dashboard_tokens_used'));

        // ── Utilisateurs actifs aujourd'hui ─────────────────────
        $activeUsersToday = ChatTokenBudget::whereDate('date', $today)
            ->where(DB::raw('public_tokens_used + dashboard_tokens_used'), '>', 0)
            ->count();

        // ── Utilisateur le plus actif aujourd'hui ───────────────
        $topBudget = ChatTokenBudget::whereDate('date', $today)
            ->orderByRaw('public_tokens_used + dashboard_tokens_used DESC')
            ->with('user:id,name')
            ->first();

        $topUserName  = $topBudget?->user?->name ?? 'N/A';
        $topUserTokens = $topBudget
            ? number_format($topBudget->public_tokens_used + $topBudget->dashboard_tokens_used)
            : '0';

        // ── Taux moyen de remplissage du budget ─────────────────
        $avgUsage = ChatTokenBudget::whereDate('date', $today)
            ->where('daily_limit', '>', 0)
            ->get()
            ->avg(fn ($b) => (($b->public_tokens_used + $b->dashboard_tokens_used) / $b->daily_limit) * 100);

        $avgUsage = round($avgUsage ?? 0, 1);

        return [
            Stat::make('Tokens aujourd\'hui', number_format($todayTotal))
                ->description("Public : " . number_format($todayPublic) . " — Dashboard : " . number_format($todayDashboard))
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary')
                ->chart($this->last7DaysTotals()),

            Stat::make('Cette semaine', number_format($weekTotal))
                ->description($weekTrend >= 0 ? "+{$weekTrend}% vs semaine dernière" : "{$weekTrend}% vs semaine dernière")
                ->descriptionIcon($weekTrend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($weekTrend >= 0 ? 'success' : 'warning'),

            Stat::make('Ce mois', number_format($monthTotal))
                ->description(Carbon::now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make('Utilisateurs actifs', $activeUsersToday)
                ->description('Aujourd\'hui')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Top utilisateur', $topUserName)
                ->description("{$topUserTokens} tokens aujourd'hui")
                ->descriptionIcon('heroicon-m-trophy')
                ->color('warning'),

            Stat::make('Remplissage moyen budget', "{$avgUsage}%")
                ->description('Budget quotidien par utilisateur')
                ->descriptionIcon('heroicon-m-chart-pie')
                ->color($avgUsage > 80 ? 'danger' : ($avgUsage > 50 ? 'warning' : 'success')),
        ];
    }

    private function last7DaysTotals(): array
    {
        return collect(range(6, 0))
            ->map(fn ($i) => ChatTokenBudget::whereDate('date', Carbon::today()->subDays($i))
                ->sum(DB::raw('public_tokens_used + dashboard_tokens_used')))
            ->values()
            ->toArray();
    }
}
