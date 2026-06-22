@extends('layouts.dashboard')

@section('title', 'Mon Assistant IA — Laravel CI')

@push('styles')
<style>
.budget-bar-track { background: #e5e7eb; border-radius: 9999px; height: 10px; overflow: hidden; }
.budget-bar-fill  { height: 100%; border-radius: 9999px; transition: width .4s ease; }
.ai-stat-card     { border-radius: 14px; padding: 1.25rem; text-align: center; }
.ai-stat-value    { font-size: 1.75rem; font-weight: 700; line-height: 1; }
.ai-stat-label    { font-size: 0.75rem; color: #6b7280; margin-top: 4px; }
.session-badge    { font-size: 0.7rem; padding: 2px 8px; border-radius: 9999px; display: inline-block; }
.chart-col        { display: flex; flex-direction: column; justify-content: flex-end; flex: 1; position: relative; }
.chart-col .bar   { border-radius: 4px 4px 0 0; background: #6366f1; opacity: .82; transition: opacity .2s; }
.chart-col:hover .bar { opacity: 1; }
.chart-tooltip    { display: none; position: absolute; bottom: 105%; left: 50%; transform: translateX(-50%);
                    background: #1f2937; color: #fff; font-size: 11px; padding: 3px 8px; border-radius: 6px;
                    white-space: nowrap; z-index: 10; pointer-events: none; }
.chart-col:hover .chart-tooltip { display: block; }
</style>
@endpush

@section('content')

@php
    /** @var \App\Models\User $user */
    $pct      = $budgetToday->usagePercent();
    $barColor = $pct >= 80 ? '#ef4444' : ($pct >= 50 ? '#f59e0b' : '#22c55e');
    $pctClass = $pct >= 80 ? 'text-danger' : ($pct >= 50 ? 'text-warning' : 'text-success');
@endphp

<x-dashboard.breadcrumb :items="[
    ['label' => 'Tableau de bord', 'href' => route('dashboard.member.overview')],
    ['label' => 'Mon Assistant IA'],
]" />

<div class="row g-4 mb-4">
    {{-- ── En-tête ── --}}
    <div class="col-12">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ asset('assets/web/img/mascot.png') }}" style="width:48px;height:48px;object-fit:contain" alt="mascot">
            <div>
                <h4 class="mb-0 fw-bold">Mon Assistant IA</h4>
                <small class="text-muted">Consommation de tokens et historique de sessions</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">

    {{-- ── Budget du jour ── --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-semibold mb-0">Budget aujourd'hui</h6>
                    <span class="badge bg-light text-muted fw-normal">{{ now()->format('d/m/Y') }}</span>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-6 col-sm-3">
                        <div class="ai-stat-card bg-light">
                            <div class="ai-stat-value text-dark">{{ number_format($budgetToday->totalUsed()) }}</div>
                            <div class="ai-stat-label">Tokens utilisés</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="ai-stat-card bg-light">
                            <div class="ai-stat-value text-secondary">{{ number_format($budgetToday->daily_limit) }}</div>
                            <div class="ai-stat-label">Limite journalière</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="ai-stat-card bg-light">
                            <div class="ai-stat-value {{ $pctClass }}">{{ $pct }}%</div>
                            <div class="ai-stat-label">Taux d'usage</div>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="ai-stat-card bg-light">
                            <div class="ai-stat-value {{ $budgetToday->isExhausted() ? 'text-danger' : 'text-success' }}">
                                {{ $budgetToday->isExhausted() ? 'Épuisé' : number_format($budgetToday->remaining()) }}
                            </div>
                            <div class="ai-stat-label">Tokens restants</div>
                        </div>
                    </div>
                </div>

                <div class="budget-bar-track mb-3">
                    <div class="budget-bar-fill" style="width:{{ min($pct, 100) }}%; background:{{ $barColor }}"></div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <small class="text-muted">Public : <strong>{{ number_format($budgetToday->public_tokens_used) }}</strong> tokens</small>
                    </div>
                    <div class="col-6 text-end">
                        <small class="text-muted">Dashboard : <strong>{{ number_format($budgetToday->dashboard_tokens_used) }}</strong> tokens</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Stats 30 derniers jours ── --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-4">30 derniers jours</h6>

                <div class="row g-3 mb-4">
                    <div class="col-4">
                        <div class="ai-stat-card" style="background:#eff6ff">
                            <div class="ai-stat-value" style="color:#1d4ed8">{{ number_format($totalAll) }}</div>
                            <div class="ai-stat-label" style="color:#3b82f6">Total tokens</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="ai-stat-card" style="background:#fff7ed">
                            <div class="ai-stat-value" style="color:#c2410c">{{ number_format($totalPublic) }}</div>
                            <div class="ai-stat-label" style="color:#f97316">Site public</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="ai-stat-card" style="background:#faf5ff">
                            <div class="ai-stat-value" style="color:#7e22ce">{{ number_format($totalDashboard) }}</div>
                            <div class="ai-stat-label" style="color:#a855f7">Dashboard</div>
                        </div>
                    </div>
                </div>

                {{-- Graphique barres 30j ── --}}
                @if($last30->isNotEmpty())
                @php $maxVal = $last30->max(fn($r) => $r->public_tokens_used + $r->dashboard_tokens_used) ?: 1; @endphp
                <div style="height:90px; display:flex; align-items:flex-end; gap:3px; overflow-x:auto;">
                    @foreach($last30 as $row)
                    @php
                        $total  = $row->public_tokens_used + $row->dashboard_tokens_used;
                        $height = max(4, round(($total / $maxVal) * 80));
                        $day    = \Carbon\Carbon::parse($row->date)->format('d/m');
                    @endphp
                    <div class="chart-col" style="min-width:18px;">
                        <div class="chart-tooltip">{{ $day }} — {{ number_format($total) }}</div>
                        <div class="bar" style="height:{{ $height }}px;"></div>
                    </div>
                    @endforeach
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <small class="text-muted">{{ \Carbon\Carbon::parse($last30->first()->date)->format('d/m') }}</small>
                    <small class="text-muted">{{ \Carbon\Carbon::parse($last30->last()->date)->format('d/m') }}</small>
                </div>
                @else
                <div class="text-center py-4 text-muted">
                    <img src="{{ asset('assets/web/img/mascot.png') }}" style="width:40px;opacity:.35" alt="" class="mb-2">
                    <div class="small">Aucune activité sur cette période.</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Historique des sessions ── --}}
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h6 class="fw-semibold mb-4">Historique des sessions</h6>

                @if($sessions->isEmpty())
                <div class="text-center py-4 text-muted">
                    <img src="{{ asset('assets/web/img/mascot.png') }}" style="width:40px;opacity:.35" alt="" class="mb-2">
                    <div class="small">Aucune session pour le moment.</div>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="fw-medium text-muted small">Titre / contexte</th>
                                <th class="fw-medium text-muted small text-center">Messages</th>
                                <th class="fw-medium text-muted small text-end">Tokens</th>
                                <th class="fw-medium text-muted small text-end">Dernière activité</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $session)
                            <tr>
                                <td>
                                    <div class="fw-medium text-dark small" style="max-width:280px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                                        {{ $session->title ?? 'Session sans titre' }}
                                    </div>
                                    <span class="session-badge mt-1
                                        {{ $session->context === 'dashboard' ? 'bg-purple-100' : '' }}"
                                        style="{{ $session->context === 'dashboard'
                                            ? 'background:#f3e8ff;color:#7e22ce'
                                            : 'background:#fff7ed;color:#c2410c' }}">
                                        {{ $session->context === 'dashboard' ? 'Dashboard' : 'Public' }}
                                    </span>
                                </td>
                                <td class="text-center text-muted small">{{ $session->messages_count }}</td>
                                <td class="text-end text-muted small">{{ number_format($session->totalTokens()) }}</td>
                                <td class="text-end text-muted small text-nowrap">
                                    {{ $session->last_activity_at?->diffForHumans() ?? '—' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
