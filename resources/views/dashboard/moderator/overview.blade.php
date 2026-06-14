@extends('layouts.dashboard')

@section('title', 'Dashboard Modérateur — Laravel CI')

@section('content')

<x-dashboard.breadcrumb :items="[
    ['label' => 'Dashboard', 'href' => route('dashboard.moderator.overview')],
    ['label' => 'Vue d\'ensemble'],
]" />

{{-- En-tête --}}
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
    <div>
        <h1 class="fs-3 fw-bold mb-1">Dashboard Modérateur</h1>
        <p class="text-muted mb-0">Vue d'ensemble de la santé de la communauté Laravel CI.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('dashboard.moderator.articles') }}" class="btn btn-warning btn-sm">
            <i class="ti ti-file-text me-1"></i>Articles ({{ $stats['pending_articles'] }})
        </a>
        <a href="{{ route('dashboard.moderator.questions') }}" class="btn btn-outline-secondary btn-sm">
            <i class="ti ti-help-circle me-1"></i>Questions
        </a>
    </div>
</div>

{{-- ─── KPIs principaux ─────────────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- Articles en attente --}}
    <div class="col-6 col-lg-3">
        <a href="{{ route('dashboard.moderator.articles') }}" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 {{ $stats['pending_articles'] > 0 ? 'border-start border-4 border-warning' : '' }}"
                 style="{{ $stats['pending_articles'] > 0 ? 'border-left:4px solid #f39c12!important' : '' }}">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="rounded-3 d-flex align-items-center justify-content-center"
                             style="width:44px;height:44px;background:#fff5e0">
                            <i class="ti ti-file-check" style="font-size:1.3rem;color:#f39c12"></i>
                        </div>
                        @if ($stats['pending_articles'] > 0)
                            <span class="badge bg-warning text-dark" style="font-size:.7rem">Action requise</span>
                        @endif
                    </div>
                    <div class="fw-bold" style="font-size:2rem;line-height:1;color:#f39c12">{{ $stats['pending_articles'] }}</div>
                    <div class="text-muted small mt-1">Articles à valider</div>
                    <div style="font-size:.75rem;color:#adb5bd">{{ $stats['published_articles'] }} publiés · {{ $stats['rejected_articles'] }} refusés</div>
                </div>
            </div>
        </a>
    </div>

    {{-- Questions cachées --}}
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="rounded-3 d-flex align-items-center justify-content-center mb-2"
                     style="width:44px;height:44px;background:#fff0ef">
                    <i class="ti ti-eye-off" style="font-size:1.3rem;color:#e74c3c"></i>
                </div>
                <div class="fw-bold" style="font-size:2rem;line-height:1;color:#e74c3c">{{ $stats['hidden_questions'] }}</div>
                <div class="text-muted small mt-1">Questions masquées</div>
                <div style="font-size:.75rem;color:#adb5bd">{{ $stats['total_questions'] }} publiées au total</div>
            </div>
        </div>
    </div>

    {{-- Membres --}}
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="rounded-3 d-flex align-items-center justify-content-center mb-2"
                     style="width:44px;height:44px;background:#edfaf3">
                    <i class="ti ti-users" style="font-size:1.3rem;color:#2ecc71"></i>
                </div>
                <div class="fw-bold" style="font-size:2rem;line-height:1;color:#2ecc71">{{ number_format($stats['total_members']) }}</div>
                <div class="text-muted small mt-1">Membres actifs</div>
                <div style="font-size:.75rem;color:#adb5bd">+{{ $stats['new_members_week'] }} cette semaine</div>
            </div>
        </div>
    </div>

    {{-- Taux de résolution --}}
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="rounded-3 d-flex align-items-center justify-content-center mb-2"
                     style="width:44px;height:44px;background:#e3f2fd">
                    <i class="ti ti-chart-bar" style="font-size:1.3rem;color:#3498db"></i>
                </div>
                <div class="fw-bold" style="font-size:2rem;line-height:1;color:#3498db">{{ $stats['answered_pct'] }}%</div>
                <div class="text-muted small mt-1">Questions résolues</div>
                <div style="font-size:.75rem;color:#adb5bd">Taux de résolution forum</div>
            </div>
        </div>
    </div>
</div>

{{-- ─── Contenu principal ────────────────────────────────── --}}
<div class="row g-3">

    {{-- Articles en attente de validation --}}
    <div class="col-12 col-xl-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom px-4 py-3 d-flex align-items-center justify-content-between">
                <h5 class="mb-0 fw-bold">
                    <i class="ti ti-file-text me-1 text-warning"></i>
                    Articles à valider
                    @if ($stats['pending_articles'] > 0)
                        <span class="badge bg-warning text-dark ms-1" style="font-size:.68rem">{{ $stats['pending_articles'] }}</span>
                    @endif
                </h5>
                <a href="{{ route('dashboard.moderator.articles') }}" class="btn btn-warning btn-sm px-3">
                    Gérer <i class="ti ti-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                @forelse ($pendingArticles as $article)
                    <div class="d-flex align-items-start gap-3 px-4 py-3 border-bottom">
                        @if ($article->author->avatar)
                            <img src="{{ $article->author->avatar }}" class="rounded-circle flex-shrink-0"
                                 style="width:36px;height:36px;object-fit:cover" alt="" />
                        @else
                            <div class="rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center"
                                 style="width:36px;height:36px;background:#fff5e0;color:#f39c12;font-weight:700;font-size:.8rem">
                                {{ strtoupper(substr($article->author->name, 0, 2)) }}
                            </div>
                        @endif
                        <div class="flex-grow-1 min-width-0">
                            <div class="fw-semibold" style="font-size:.88rem">
                                {{ Str::limit($article->title, 55) }}
                            </div>
                            <div style="font-size:.75rem;color:#adb5bd">
                                par {{ $article->author->name }}
                                · {{ $article->created_at->diffForHumans() }}
                                · <span class="badge px-2" style="background:{{ $article->level->badgeBackground() }};color:{{ $article->level->accentColor() }};font-size:.65rem;">{{ $article->level->label() }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-circle-check d-block mb-2 text-success" style="font-size:2.5rem;opacity:.5"></i>
                        Aucun article en attente. File vide !
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Questions masquées + Nouveaux membres --}}
    <div class="col-12 col-xl-6">
        <div class="row g-3 h-100">

            {{-- Questions masquées --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom px-4 py-3 d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-bold">
                            <i class="ti ti-eye-off me-1 text-danger"></i>
                            Questions masquées
                        </h5>
                        <a href="{{ route('dashboard.moderator.questions') }}" class="text-primary" style="font-size:.82rem">
                            Voir tout <i class="ti ti-arrow-right"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        @forelse ($pendingQuestions->take(4) as $question)
                            <div class="d-flex align-items-center gap-3 px-4 py-2 border-bottom">
                                <div class="flex-grow-1">
                                    <div style="font-size:.85rem;font-weight:600">
                                        {{ Str::limit($question->title, 60) }}
                                    </div>
                                    <div style="font-size:.75rem;color:#adb5bd">
                                        {{ $question->user->name }} · {{ $question->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <span class="badge bg-danger-subtle text-danger" style="font-size:.65rem">Caché</span>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted" style="font-size:.85rem">
                                <i class="ti ti-circle-check text-success me-1"></i>Aucune question masquée.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Nouveaux membres --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom px-4 py-3">
                        <h5 class="mb-0 fw-bold">
                            <i class="ti ti-user-plus me-1 text-success"></i>
                            Nouveaux membres
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        @foreach ($newMembers as $member)
                            <div class="d-flex align-items-center gap-3 px-4 py-2 border-bottom">
                                @if ($member->avatar)
                                    <img src="{{ $member->avatar }}" class="rounded-circle flex-shrink-0"
                                         style="width:32px;height:32px;object-fit:cover" alt="" />
                                @else
                                    <div class="rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center"
                                         style="width:32px;height:32px;background:#edfaf3;color:#2ecc71;font-weight:700;font-size:.75rem">
                                        {{ strtoupper(substr($member->name, 0, 2)) }}
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <div style="font-size:.85rem;font-weight:600">{{ $member->name }}</div>
                                    <div style="font-size:.73rem;color:#adb5bd">
                                        {{ $member->github_username ? '@' . $member->github_username . ' · ' : '' }}{{ $member->created_at->diffForHumans() }}
                                    </div>
                                </div>
                                <span class="badge bg-success-subtle text-success" style="font-size:.65rem">
                                    {{ $member->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

{{-- ─── Actions rapides ──────────────────────────────────── --}}
<div class="row g-3 mt-1">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="fw-semibold text-muted" style="font-size:.82rem">Actions rapides :</span>
                    <a href="{{ route('dashboard.moderator.articles') }}" class="btn btn-warning btn-sm">
                        <i class="ti ti-file-check me-1"></i>Valider les articles
                    </a>
                    <a href="{{ route('dashboard.moderator.questions') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="ti ti-help-circle me-1"></i>Modérer le forum
                    </a>
                    <a href="{{ route('forum.index') }}" class="btn btn-outline-primary btn-sm" target="_blank">
                        <i class="ti ti-external-link me-1"></i>Voir le forum
                    </a>
                    <a href="{{ route('blog.index') }}" class="btn btn-outline-primary btn-sm" target="_blank">
                        <i class="ti ti-external-link me-1"></i>Voir le blog
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
