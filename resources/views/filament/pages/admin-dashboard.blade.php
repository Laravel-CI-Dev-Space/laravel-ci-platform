<x-filament-panels::page>

<style>
/* ── Reset & base ────────────────────────────── */
.adm-dash { font-family: inherit; }

/* ── KPI Cards ───────────────────────────────── */
.kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
@media (max-width: 900px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 500px) { .kpi-grid { grid-template-columns: 1fr; } }

.kpi-card {
    background: #fff;
    border-radius: 16px;
    padding: 1.25rem 1.5rem;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    border: 1px solid #f0f0f0;
    display: flex; flex-direction: column; gap: .5rem;
    transition: box-shadow .2s, transform .2s;
}
.kpi-card:hover { box-shadow: 0 6px 24px rgba(0,0,0,.1); transform: translateY(-2px); }

.kpi-icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.2rem; margin-bottom: .25rem;
}
.kpi-label { font-size: .78rem; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: .06em; }
.kpi-value { font-size: 2rem; font-weight: 800; color: #111827; line-height: 1.1; }
.kpi-sub   { font-size: .75rem; color: #6b7280; margin-top: .1rem; }
.kpi-badge {
    display: inline-flex; align-items: center; gap: .25rem;
    font-size: .72rem; font-weight: 600; padding: .2rem .6rem;
    border-radius: 2rem; width: fit-content;
}
.badge-up   { background: #dcfce7; color: #16a34a; }
.badge-down { background: #fee2e2; color: #dc2626; }
.badge-warn { background: #fef9c3; color: #ca8a04; }
.badge-info { background: #dbeafe; color: #2563eb; }

/* ── Grid principal ──────────────────────────── */
.main-grid  { display: grid; grid-template-columns: 2fr 1fr; gap: 1rem; margin-bottom: 1rem; }
.bottom-grid{ display: grid; grid-template-columns: 1fr 2fr; gap: 1rem; }
@media (max-width: 900px) { .main-grid, .bottom-grid { grid-template-columns: 1fr; } }

/* ── Panels ──────────────────────────────────── */
.panel {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    border: 1px solid #f0f0f0;
    overflow: hidden;
}
.panel-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1.1rem 1.5rem;
    border-bottom: 1px solid #f3f4f6;
}
.panel-title { font-size: .92rem; font-weight: 700; color: #111827; }
.panel-sub   { font-size: .75rem; color: #9ca3af; margin-top: .1rem; }
.panel-body  { padding: 1.25rem 1.5rem; }

/* ── Chart container ─────────────────────────── */
.chart-wrap { position: relative; width: 100%; }

/* ── Pending actions ─────────────────────────── */
.action-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: .75rem 1.5rem;
    border-bottom: 1px solid #f9fafb;
    transition: background .15s;
}
.action-row:hover { background: #fafafa; }
.action-row:last-child { border-bottom: none; }
.action-icon {
    width: 36px; height: 36px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: .9rem; flex-shrink: 0;
}
.action-label { font-size: .85rem; font-weight: 600; color: #374151; }
.action-desc  { font-size: .73rem; color: #9ca3af; }
.action-count {
    font-size: 1.1rem; font-weight: 800;
    min-width: 36px; text-align: right;
}

/* ── Members list ────────────────────────────── */
.member-row {
    display: flex; align-items: center; gap: .75rem;
    padding: .65rem 1.5rem;
    border-bottom: 1px solid #f9fafb;
    transition: background .15s;
}
.member-row:hover { background: #fafafa; }
.member-row:last-child { border-bottom: none; }
.member-avatar {
    width: 36px; height: 36px; border-radius: 50%; object-fit: cover;
    flex-shrink: 0;
}
.member-avatar-init {
    width: 36px; height: 36px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700; font-size: .78rem; flex-shrink: 0;
    background: linear-gradient(135deg, #e8590c, #c44508); color: #fff;
}
.member-name { font-size: .85rem; font-weight: 600; color: #111827; }
.member-handle { font-size: .72rem; color: #9ca3af; }
.role-pill {
    font-size: .65rem; font-weight: 700; padding: .15rem .5rem;
    border-radius: 2rem; margin-left: auto; flex-shrink: 0;
}

/* ── Donut chart ─────────────────────────────── */
.donut-wrap {
    display: flex; flex-direction: column; align-items: center; gap: .75rem;
}
.donut-legend { display: flex; flex-direction: column; gap: .4rem; width: 100%; }
.legend-item  { display: flex; align-items: center; gap: .5rem; font-size: .8rem; color: #374151; }
.legend-dot   { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
.legend-val   { margin-left: auto; font-weight: 700; color: #111827; }

/* ── Stats inline ────────────────────────────── */
.stat-row { display: flex; gap: 1rem; }
.stat-box {
    flex: 1; background: #f9fafb; border-radius: 12px; padding: .85rem;
    text-align: center; border: 1px solid #f0f0f0;
}
.stat-box .val { font-size: 1.4rem; font-weight: 800; color: #111827; }
.stat-box .lbl { font-size: .72rem; color: #9ca3af; margin-top: .15rem; }

/* ── Page header ─────────────────────────────── */
.dash-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 1.5rem; flex-wrap: wrap; gap: .75rem;
}
.dash-title { font-size: 1.6rem; font-weight: 800; color: #111827; }
.dash-sub   { font-size: .85rem; color: #6b7280; margin-top: .1rem; }
.btn-export {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .6rem 1.25rem; border-radius: 10px;
    background: #e8590c; color: #fff; font-weight: 600; font-size: .85rem;
    border: none; cursor: pointer; text-decoration: none; transition: background .2s;
}
.btn-export:hover { background: #c44508; color: #fff; }
.btn-outline {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .6rem 1.25rem; border-radius: 10px;
    background: #fff; color: #374151; font-weight: 600; font-size: .85rem;
    border: 1.5px solid #e5e7eb; cursor: pointer; text-decoration: none; transition: border-color .2s;
}
.btn-outline:hover { border-color: #e8590c; color: #e8590c; }
</style>

<div class="adm-dash">

    {{-- ── En-tête ── --}}
    <div class="dash-header">
        <div>
            <div class="dash-title">Tableau de bord</div>
            <div class="dash-sub">Vue d'ensemble de la plateforme Laravel CI</div>
        </div>
        <div style="display:flex;gap:.5rem;">
            <a href="{{ route('home') }}" target="_blank" class="btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                Voir le site
            </a>
            <button class="btn-export" onclick="window.print()">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                Exporter
            </button>
        </div>
    </div>

    {{-- ── KPI Cards ── --}}
    <div class="kpi-grid">

        {{-- Membres --}}
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#fff7ed">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#e8590c" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="kpi-label">Membres actifs</div>
            <div class="kpi-value">{{ number_format($totalMembers) }}</div>
            <span class="kpi-badge {{ $memberTrend >= 0 ? 'badge-up' : 'badge-down' }}">
                {{ $memberTrend >= 0 ? '↑' : '↓' }} {{ abs($memberTrend) }}% ce mois
            </span>
            <div class="kpi-sub">+{{ $membersThisMonth }} inscriptions ce mois</div>
        </div>

        {{-- Articles --}}
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#f0fdf4">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
            </div>
            <div class="kpi-label">Articles publiés</div>
            <div class="kpi-value">{{ number_format($publishedArticles) }}</div>
            <span class="kpi-badge badge-warn">{{ $pendingArticles }} en attente</span>
            <div class="kpi-sub">+{{ $articlesThisMonth }} publiés ce mois</div>
        </div>

        {{-- Questions --}}
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#eff6ff">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <div class="kpi-label">Questions forum</div>
            <div class="kpi-value">{{ number_format($totalQuestions) }}</div>
            <span class="kpi-badge badge-info">{{ $answeredPct }}% résolues</span>
            <div class="kpi-sub">{{ $hiddenQuestions }} question(s) masquée(s)</div>
        </div>

        {{-- Offres --}}
        <div class="kpi-card">
            <div class="kpi-icon" style="background:#fdf4ff">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#9333ea" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            </div>
            <div class="kpi-label">Offres actives</div>
            <div class="kpi-value">{{ number_format($activeOffers) }}</div>
            <span class="kpi-badge badge-warn">{{ $pendingOffers }} en validation</span>
            <div class="kpi-sub">{{ number_format($totalApplications) }} candidatures reçues</div>
        </div>

    </div>

    {{-- ── Ligne principale : Chart + Actions ── --}}
    <div class="main-grid">

        {{-- Bar chart Activité --}}
        <div class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Activité du contenu</div>
                    <div class="panel-sub">Articles · Questions · Offres — 6 derniers mois</div>
                </div>
                <div style="display:flex;gap:.4rem;">
                    <span style="display:flex;align-items:center;gap:.3rem;font-size:.72rem;color:#6b7280"><span style="width:8px;height:8px;border-radius:50%;background:#e8590c;display:inline-block"></span>Articles</span>
                    <span style="display:flex;align-items:center;gap:.3rem;font-size:.72rem;color:#6b7280"><span style="width:8px;height:8px;border-radius:50%;background:#2563eb;display:inline-block"></span>Questions</span>
                    <span style="display:flex;align-items:center;gap:.3rem;font-size:.72rem;color:#6b7280"><span style="width:8px;height:8px;border-radius:50%;background:#16a34a;display:inline-block"></span>Offres</span>
                </div>
            </div>
            <div class="panel-body">
                <div class="chart-wrap" style="height:220px">
                    <canvas id="contentChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Actions en attente --}}
        <div class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Actions requises</div>
                    <div class="panel-sub">Contenu en attente de traitement</div>
                </div>
                @php $totalPending = $pendingArticles + $pendingOffers + $pendingCompanies; @endphp
                @if ($totalPending > 0)
                    <span style="background:#fef9c3;color:#ca8a04;font-size:.7rem;font-weight:700;padding:.2rem .6rem;border-radius:2rem">{{ $totalPending }} en attente</span>
                @else
                    <span style="background:#dcfce7;color:#16a34a;font-size:.7rem;font-weight:700;padding:.2rem .6rem;border-radius:2rem">✓ Tout traité</span>
                @endif
            </div>

            <div class="action-row">
                <div class="action-icon" style="background:#fff7ed">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#e8590c" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                </div>
                <div style="flex:1;margin-left:.75rem">
                    <div class="action-label">Articles à valider</div>
                    <div class="action-desc">{{ $rejectedArticles }} refusés récemment</div>
                </div>
                <div class="action-count" style="color:{{ $pendingArticles > 0 ? '#e8590c' : '#9ca3af' }}">{{ $pendingArticles }}</div>
            </div>

            <div class="action-row">
                <div class="action-icon" style="background:#eff6ff">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                </div>
                <div style="flex:1;margin-left:.75rem">
                    <div class="action-label">Offres à publier</div>
                    <div class="action-desc">En attente de validation admin</div>
                </div>
                <div class="action-count" style="color:{{ $pendingOffers > 0 ? '#2563eb' : '#9ca3af' }}">{{ $pendingOffers }}</div>
            </div>

            <div class="action-row">
                <div class="action-icon" style="background:#f0fdf4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <div style="flex:1;margin-left:.75rem">
                    <div class="action-label">Demandes entreprise</div>
                    <div class="action-desc">Accès portail recruteur en attente</div>
                </div>
                <div class="action-count" style="color:{{ $pendingCompanies > 0 ? '#16a34a' : '#9ca3af' }}">{{ $pendingCompanies }}</div>
            </div>

            <div class="action-row">
                <div class="action-icon" style="background:#fdf4ff">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#9333ea" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div style="flex:1;margin-left:.75rem">
                    <div class="action-label">Membres inscrits</div>
                    <div class="action-desc">+{{ $membersThisMonth }} ce mois</div>
                </div>
                <div class="action-count" style="color:#9333ea">{{ number_format($totalMembers) }}</div>
            </div>

            {{-- Stats rapides --}}
            <div style="padding:1rem 1.5rem;border-top:1px solid #f3f4f6">
                <div class="stat-row">
                    <div class="stat-box">
                        <div class="val" style="color:#e8590c">{{ $answeredPct }}%</div>
                        <div class="lbl">Résolues</div>
                    </div>
                    <div class="stat-box">
                        <div class="val" style="color:#2563eb">{{ $activeOffers }}</div>
                        <div class="lbl">Offres actives</div>
                    </div>
                    <div class="stat-box">
                        <div class="val" style="color:#16a34a">{{ $publishedArticles }}</div>
                        <div class="lbl">Articles</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Ligne basse : Membres + Growth chart ── --}}
    <div class="bottom-grid">

        {{-- Membres récents --}}
        <div class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Membres récents</div>
                    <div class="panel-sub">7 derniers inscrits</div>
                </div>
                <a href="/admin/users" style="font-size:.78rem;color:#e8590c;font-weight:600;text-decoration:none">
                    Voir tout →
                </a>
            </div>

            @foreach ($recentMembers as $member)
                @php
                    $roleName = $member->roles->first()?->name ?? 'member';
                    $roleColors = [
                        'super-admin' => ['#fdeaec', '#e74c3c'],
                        'admin'       => ['#fff5e0', '#f39c12'],
                        'moderator'   => ['#e3f2fd', '#2196F3'],
                        'member'      => ['#e8f5e9', '#4caf50'],
                    ];
                    [$rBg, $rColor] = $roleColors[$roleName] ?? ['#f3f4f6', '#6b7280'];
                @endphp
                <div class="member-row">
                    @if ($member->avatar)
                        <img src="{{ $member->avatar }}" class="member-avatar" alt="" />
                    @else
                        <div class="member-avatar-init">{{ strtoupper(substr($member->name, 0, 2)) }}</div>
                    @endif
                    <div style="flex:1;min-width:0">
                        <div class="member-name">{{ $member->name }}</div>
                        <div class="member-handle">{{ $member->github_username ? '@'.$member->github_username : $member->email }}</div>
                    </div>
                    <span class="role-pill" style="background:{{ $rBg }};color:{{ $rColor }}">
                        {{ ucfirst(str_replace('-', ' ', $roleName)) }}
                    </span>
                </div>
            @endforeach
        </div>

        {{-- Growth chart + Donut offres --}}
        <div style="display:grid;grid-template-rows:1fr 1fr;gap:1rem;">

            {{-- Line chart croissance --}}
            <div class="panel">
                <div class="panel-header">
                    <div>
                        <div class="panel-title">Croissance des membres</div>
                        <div class="panel-sub">Inscriptions mensuelles — 12 mois</div>
                    </div>
                    <span style="background:#fff7ed;color:#e8590c;font-size:.72rem;font-weight:700;padding:.2rem .6rem;border-radius:2rem">
                        +{{ $membersThisMonth }} ce mois
                    </span>
                </div>
                <div class="panel-body" style="padding-top:.75rem">
                    <div class="chart-wrap" style="height:130px">
                        <canvas id="growthChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Donut offres --}}
            <div class="panel">
                <div class="panel-header">
                    <div>
                        <div class="panel-title">Répartition des offres</div>
                        <div class="panel-sub">Par statut</div>
                    </div>
                </div>
                <div class="panel-body">
                    <div style="display:grid;grid-template-columns:auto 1fr;gap:1.25rem;align-items:center">
                        <div style="position:relative;width:100px;height:100px">
                            <canvas id="donutChart"></canvas>
                        </div>
                        <div class="donut-legend">
                            <div class="legend-item">
                                <div class="legend-dot" style="background:#16a34a"></div>
                                <span>Actives</span>
                                <span class="legend-val">{{ $offersActive }}</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-dot" style="background:#ca8a04"></div>
                                <span>En attente</span>
                                <span class="legend-val">{{ $offersPending }}</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-dot" style="background:#2563eb"></div>
                                <span>Pourvues</span>
                                <span class="legend-val">{{ $offersFilled }}</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-dot" style="background:#dc2626"></div>
                                <span>Expirées</span>
                                <span class="legend-val">{{ $offersExpired }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

{{-- ── Charts JS ── --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    Chart.defaults.font.family = 'inherit';
    Chart.defaults.color = '#9ca3af';

    /* ── Bar chart contenu ── */
    new Chart(document.getElementById('contentChart'), {
        type: 'bar',
        data: {
            labels: @json($chartLabels),
            datasets: [
                {
                    label: 'Articles',
                    data: @json($chartArticles),
                    backgroundColor: 'rgba(232,89,12,0.8)',
                    borderRadius: 6,
                    borderSkipped: false,
                },
                {
                    label: 'Questions',
                    data: @json($chartQuestions),
                    backgroundColor: 'rgba(37,99,235,0.8)',
                    borderRadius: 6,
                    borderSkipped: false,
                },
                {
                    label: 'Offres',
                    data: @json($chartOffers),
                    backgroundColor: 'rgba(22,163,74,0.8)',
                    borderRadius: 6,
                    borderSkipped: false,
                },
            ]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, border: { display: false } },
                y: { beginAtZero: true, grid: { color: '#f3f4f6' }, border: { display: false },
                     ticks: { stepSize: 1 } },
            }
        }
    });

    /* ── Line chart croissance ── */
    new Chart(document.getElementById('growthChart'), {
        type: 'line',
        data: {
            labels: @json($growthLabels),
            datasets: [{
                label: 'Nouveaux membres',
                data: @json($growthData),
                borderColor: '#e8590c',
                backgroundColor: 'rgba(232,89,12,0.08)',
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointBackgroundColor: '#e8590c',
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, border: { display: false } },
                y: { beginAtZero: true, grid: { color: '#f3f4f6' }, border: { display: false },
                     ticks: { stepSize: 1 } },
            }
        }
    });

    /* ── Donut offres ── */
    new Chart(document.getElementById('donutChart'), {
        type: 'doughnut',
        data: {
            labels: ['Actives', 'En attente', 'Pourvues', 'Expirées'],
            datasets: [{
                data: [{{ $offersActive }}, {{ $offersPending }}, {{ $offersFilled }}, {{ $offersExpired }}],
                backgroundColor: ['#16a34a', '#ca8a04', '#2563eb', '#dc2626'],
                borderWidth: 3,
                borderColor: '#fff',
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            cutout: '70%',
            plugins: { legend: { display: false } },
        }
    });
});
</script>

</x-filament-panels::page>
