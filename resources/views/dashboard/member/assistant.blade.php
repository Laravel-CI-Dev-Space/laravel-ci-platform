@extends('layouts.dashboard')

@section('title', 'Assistant IA — Monitoring — Laravel CI')

@push('styles')
<style>
.budget-bar-track { background: #e5e7eb; border-radius: 9999px; height: 10px; overflow: hidden; }
.budget-bar-fill  { height: 100%; border-radius: 9999px; transition: width .4s ease; }
.chart-bar        { display: inline-block; width: 100%; background: #e5e7eb; border-radius: 4px 4px 0 0; position: relative; }
.chart-bar span   { position: absolute; bottom: 0; left: 0; right: 0; border-radius: 4px 4px 0 0; }
</style>
@endpush

@section('content')

@php
    /** @var \App\Models\User $user */
    $pct = $budgetToday->usagePercent();
    $barColor = $pct >= 80 ? '#ef4444' : ($pct >= 50 ? '#f59e0b' : '#22c55e');
@endphp

<x-dashboard.breadcrumb :items="[
    ['label' => 'Tableau de bord', 'href' => route('dashboard.member.overview')],
    ['label' => 'Assistant IA'],
]" />

<div class="px-4 sm:px-6 lg:px-8 py-6 space-y-8">

    {{-- ── En-tête ── --}}
    <div class="flex items-center gap-3">
        <img src="{{ asset('assets/web/img/mascot.png') }}" class="w-12 h-12 object-contain" alt="mascot">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mon Assistant IA</h1>
            <p class="text-sm text-gray-500">Consommation de tokens et historique de sessions</p>
        </div>
    </div>

    {{-- ── Budget du jour ── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-semibold text-gray-800">Budget aujourd'hui</h2>
            <span class="text-xs text-gray-400">{{ now()->format('d/m/Y') }}</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-5">
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">{{ number_format($budgetToday->totalUsed()) }}</div>
                <div class="text-xs text-gray-500 mt-1">Tokens utilisés</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-400">{{ number_format($budgetToday->daily_limit) }}</div>
                <div class="text-xs text-gray-500 mt-1">Limite journalière</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold" style="color:{{ $barColor }}">{{ $pct }}%</div>
                <div class="text-xs text-gray-500 mt-1">Taux d'usage</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold {{ $budgetToday->isExhausted() ? 'text-red-600' : 'text-green-600' }}">
                    {{ $budgetToday->isExhausted() ? 'Épuisé' : number_format($budgetToday->remaining()) }}
                </div>
                <div class="text-xs text-gray-500 mt-1">Tokens restants</div>
            </div>
        </div>

        <div class="budget-bar-track">
            <div class="budget-bar-fill" style="width:{{ min($pct, 100) }}%; background:{{ $barColor }}"></div>
        </div>

        <div class="mt-3 grid grid-cols-2 gap-3 text-xs text-gray-500">
            <div>Public : <strong>{{ number_format($budgetToday->public_tokens_used) }}</strong> tokens</div>
            <div>Dashboard : <strong>{{ number_format($budgetToday->dashboard_tokens_used) }}</strong> tokens</div>
        </div>
    </div>

    {{-- ── Stats 30 derniers jours ── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-5">30 derniers jours</h2>

        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-blue-50 rounded-xl p-4 text-center">
                <div class="text-xl font-bold text-blue-700">{{ number_format($totalAll) }}</div>
                <div class="text-xs text-blue-500 mt-1">Total tokens</div>
            </div>
            <div class="bg-orange-50 rounded-xl p-4 text-center">
                <div class="text-xl font-bold text-orange-700">{{ number_format($totalPublic) }}</div>
                <div class="text-xs text-orange-500 mt-1">Site public</div>
            </div>
            <div class="bg-purple-50 rounded-xl p-4 text-center">
                <div class="text-xl font-bold text-purple-700">{{ number_format($totalDashboard) }}</div>
                <div class="text-xs text-purple-500 mt-1">Dashboard</div>
            </div>
        </div>

        {{-- Graphique simplifié ── --}}
        @if($last30->isNotEmpty())
        @php $maxVal = $last30->max(fn($r) => $r->public_tokens_used + $r->dashboard_tokens_used) ?: 1; @endphp
        <div class="overflow-x-auto">
            <div class="flex items-end gap-1" style="height: 80px; min-width: {{ $last30->count() * 24 }}px;">
                @foreach($last30 as $row)
                @php
                    $total = $row->public_tokens_used + $row->dashboard_tokens_used;
                    $height = max(4, round(($total / $maxVal) * 72));
                @endphp
                <div class="flex-1 relative group" style="height: 80px; display: flex; align-items: flex-end;">
                    <div class="w-full rounded-t" style="height: {{ $height }}px; background: #6366f1; opacity: 0.8;"></div>
                    <div class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">
                        {{ \Carbon\Carbon::parse($row->date)->format('d/m') }} — {{ number_format($total) }} tokens
                    </div>
                </div>
                @endforeach
            </div>
            <div class="flex justify-between text-xs text-gray-400 mt-1" style="min-width: {{ $last30->count() * 24 }}px;">
                <span>{{ \Carbon\Carbon::parse($last30->first()->date)->format('d/m') }}</span>
                <span>{{ \Carbon\Carbon::parse($last30->last()->date)->format('d/m') }}</span>
            </div>
        </div>
        @else
        <div class="text-center py-8 text-gray-400 text-sm">
            <img src="{{ asset('assets/web/img/mascot.png') }}" class="w-12 h-12 mx-auto mb-3 opacity-40" alt="">
            Aucune activité sur cette période.
        </div>
        @endif
    </div>

    {{-- ── Historique des sessions ── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-base font-semibold text-gray-800 mb-4">Historique des sessions</h2>

        @if($sessions->isEmpty())
        <div class="text-center py-8 text-gray-400 text-sm">
            <img src="{{ asset('assets/web/img/mascot.png') }}" class="w-12 h-12 mx-auto mb-3 opacity-40" alt="">
            Aucune session pour le moment.
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-xs text-gray-500 border-b border-gray-100">
                        <th class="text-left pb-2 font-medium">Titre / contexte</th>
                        <th class="text-center pb-2 font-medium">Messages</th>
                        <th class="text-right pb-2 font-medium">Tokens</th>
                        <th class="text-right pb-2 font-medium">Dernière activité</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($sessions as $session)
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 pr-4">
                            <div class="font-medium text-gray-800 truncate max-w-xs">
                                {{ $session->title ?? 'Session sans titre' }}
                            </div>
                            <span class="inline-block text-xs px-2 py-0.5 rounded-full mt-0.5
                                {{ $session->context === 'dashboard' ? 'bg-purple-100 text-purple-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ $session->context === 'dashboard' ? 'Dashboard' : 'Public' }}
                            </span>
                        </td>
                        <td class="py-2 text-center text-gray-600">{{ $session->messages_count }}</td>
                        <td class="py-2 text-right text-gray-600">{{ number_format($session->total_tokens) }}</td>
                        <td class="py-2 text-right text-gray-400 whitespace-nowrap">
                            {{ $session->last_message_at?->diffForHumans() ?? '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
@endsection
