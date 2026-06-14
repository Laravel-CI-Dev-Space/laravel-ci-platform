@extends('layouts.dashboard')

@section('title', 'Mes articles — Laravel CI')

@section('content')

<x-dashboard.breadcrumb :items="[
    ['label' => 'Dashboard', 'href' => route('dashboard.member.overview')],
    ['label' => 'Mes articles'],
]" />

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ── En-tête ────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h1 class="fs-3 fw-bold mb-1">Mes articles</h1>
        <p class="text-muted mb-0">Gérez vos contributions au blog de la communauté.</p>
    </div>
    <a href="{{ route('blog.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Rédiger un article
    </a>
</div>

{{-- ── Statistiques ────────────────────────────── --}}
@php
    $totalArticles  = array_sum($counts ?? []);
    $publishedCount = $counts['published']  ?? 0;
    $pendingCount   = $counts['pending']    ?? 0;
    $draftCount     = $counts['draft']      ?? 0;
    $rejectedCount  = $counts['rejected']   ?? 0;

    /* Tags uniques présents sur cette page */
    $pageTags = collect($articles->items())
        ->flatMap(fn ($a) => $a->tags ?? collect())
        ->unique('id')
        ->sortBy('name');
@endphp

<div class="row g-3 mb-4">
    @foreach ([
        ['Total',       $totalArticles,  '#f0f4ff', '#4a6cf7', 'ti-file-text'],
        ['Publiés',     $publishedCount, '#edfaf3', '#2ecc71', 'ti-circle-check'],
        ['En attente',  $pendingCount,   '#fff5e0', '#f39c12', 'ti-clock'],
        ['Brouillons',  $draftCount,     '#f5f6fa', '#7f8c8d', 'ti-pencil'],
    ] as [$label, $count, $bg, $color, $icon])
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center"
                         style="width:44px;height:44px;flex-shrink:0;background:{{ $bg }}">
                        <i class="ti {{ $icon }}" style="font-size:1.3rem;color:{{ $color }}"></i>
                    </div>
                    <div>
                        <div class="fw-bold fs-4 lh-1">{{ $count }}</div>
                        <div class="text-muted small">{{ $label }}</div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- ═══════════════════════════════════════════════
     Filtres + Recherche + Tag filter + Grille cartes
     ═══════════════════════════════════════════════ --}}
<div x-data="{ search: '', activeTag: '' }">

    {{-- ── Filtres statut + Recherche ──────────── --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">

            {{-- Onglets statut --}}
            <div class="d-flex gap-1 flex-wrap">
                @php
                $filters = [
                    null        => ['Tous',       $totalArticles,  ''],
                    'draft'     => ['Brouillons', $draftCount,     'secondary'],
                    'pending'   => ['En attente', $pendingCount,   'warning'],
                    'published' => ['Publiés',    $publishedCount, 'success'],
                    'rejected'  => ['Rejetés',    $rejectedCount,  'danger'],
                ];
                $activeFilter = request('status');
                @endphp

                @foreach ($filters as $key => [$lbl, $cnt, $col])
                    @php $on = ($key === $activeFilter || ($key === null && $activeFilter === null)); @endphp
                    <a href="{{ route('dashboard.member.articles', $key ? ['status' => $key] : []) }}"
                       class="btn btn-sm d-inline-flex align-items-center gap-1 {{ $on ? 'btn-primary' : 'btn-light' }}"
                       style="border-radius:2rem; font-size:.78rem;">
                        {{ $lbl }}
                        <span class="badge rounded-pill {{ $on ? 'bg-white text-primary' : 'bg-secondary-subtle text-secondary' }}"
                              style="font-size:.65rem">{{ $cnt }}</span>
                    </a>
                @endforeach
            </div>

            {{-- Recherche --}}
            <div class="input-group input-group-sm" style="max-width:220px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="ti ti-search text-muted"></i>
                </span>
                <input type="text" x-model="search"
                       class="form-control border-start-0 ps-0"
                       placeholder="Rechercher…" style="box-shadow:none;" />
            </div>
        </div>
    </div>

    {{-- ── Filtre par tag (pills) ───────────────── --}}
    @if ($pageTags->isNotEmpty())
        <div class="d-flex align-items-center gap-2 flex-wrap mb-3">
            <span class="text-muted" style="font-size:.8rem">
                <i class="ti ti-tag me-1"></i>Tags :
            </span>
            <button
                class="badge fw-normal border-0 px-2 py-1"
                :class="activeTag === '' ? 'bg-primary text-white' : 'bg-light text-secondary'"
                style="border-radius:2rem; font-size:.75rem; cursor:pointer;"
                x-on:click="activeTag = ''"
            >Tous</button>
            @foreach ($pageTags as $tag)
                <button
                    class="badge fw-normal border-0 px-2 py-1"
                    :class="activeTag === '{{ strtolower($tag->name) }}' ? 'bg-primary text-white' : 'bg-light text-secondary'"
                    style="border-radius:2rem; font-size:.75rem; cursor:pointer;"
                    x-on:click="activeTag = activeTag === '{{ strtolower($tag->name) }}' ? '' : '{{ strtolower($tag->name) }}'"
                >{{ $tag->name }}</button>
            @endforeach
        </div>
    @endif

    {{-- ── Grille de cartes ────────────────────── --}}
    <div class="row g-3">
        @forelse ($articles as $article)
            @php
                $words    = str_word_count(strip_tags($article->body ?? ''));
                $readTime = max(1, (int) round($words / 200));
                $excerpt  = $article->excerpt
                    ?: Str::limit(strip_tags($article->body ?? ''), 100);

                $statusMap = [
                    \App\Enums\ArticleStatus::Published->value => ['bg-success-subtle text-success',     'ti-circle-check', 'Publié'],
                    \App\Enums\ArticleStatus::Pending->value   => ['bg-warning-subtle text-warning',     'ti-clock',        'En attente'],
                    \App\Enums\ArticleStatus::Rejected->value  => ['bg-danger-subtle text-danger',       'ti-x',            'Rejeté'],
                    \App\Enums\ArticleStatus::Draft->value     => ['bg-secondary-subtle text-secondary', 'ti-pencil',       'Brouillon'],
                ];
                [$sBadge, $sIcon, $sLabel] = $statusMap[$article->status->value] ?? $statusMap['draft'];

                $levelIconMap = [
                    \App\Enums\ArticleLevel::Beginner->value     => 'ti-seedling',
                    \App\Enums\ArticleLevel::Intermediate->value => 'ti-chart-bar',
                    \App\Enums\ArticleLevel::Advanced->value     => 'ti-flame',
                ];
                $lColor = $article->level->accentColor();
                $lIcon  = $levelIconMap[$article->level->value];
                $lLabel = $article->level->label();

                $cardTags = $article->tags->pluck('name')->map('strtolower')->implode(',');
                $cardTitle = strtolower($article->title);
            @endphp

            <div
                class="col-md-6 col-xl-4"
                x-show="
                    (search === '' || '{{ addslashes($cardTitle) }}'.includes(search.toLowerCase())) &&
                    (activeTag === '' || '{{ $cardTags }}'.split(',').includes(activeTag))
                "
            >
                <div class="card border-0 shadow-sm h-100 overflow-hidden"
                     style="border-left: 4px solid {{ $lColor }} !important; border-radius:.75rem;">
                    <div class="card-body pb-2">

                        {{-- Statut + Niveau + Modifié --}}
                        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-1">
                            <div class="d-flex align-items-center gap-1">
                                <span class="badge {{ $sBadge }} d-inline-flex align-items-center gap-1" style="font-size:.72rem">
                                    <i class="ti {{ $sIcon }}"></i>{{ $sLabel }}
                                </span>
                                @if ($article->wasEdited())
                                    <span class="badge bg-light border text-muted d-inline-flex align-items-center gap-1"
                                          style="font-size:.68rem" title="Modifié le {{ $article->edited_at->format('d M Y à H:i') }}">
                                        <i class="ti ti-pencil"></i>Modifié
                                    </span>
                                @endif
                            </div>
                            <span class="d-inline-flex align-items-center gap-1" style="font-size:.75rem; color:{{ $lColor }}; font-weight:600">
                                <i class="ti {{ $lIcon }}"></i>{{ $lLabel }}
                            </span>
                        </div>

                        {{-- Titre --}}
                        <h6 class="fw-bold mb-1 lh-sm" style="font-size:.95rem">
                            @if ($article->status === \App\Enums\ArticleStatus::Published)
                                <a href="{{ route('blog.show', $article->slug) }}"
                                   class="text-dark text-decoration-none stretched-link-off">
                                    {{ Str::limit($article->title, 60) }}
                                </a>
                            @else
                                {{ Str::limit($article->title, 60) }}
                            @endif
                        </h6>

                        {{-- Extrait --}}
                        @if ($excerpt)
                            <p class="text-muted mb-2" style="font-size:.8rem; line-height:1.5">
                                {{ Str::limit($excerpt, 90) }}
                            </p>
                        @endif

                        {{-- Tags --}}
                        @if ($article->tags->isNotEmpty())
                            <div class="d-flex flex-wrap gap-1 mb-2">
                                @foreach ($article->tags as $tag)
                                    <span
                                        class="badge bg-light border text-secondary"
                                        style="font-size:.68rem; border-radius:.5rem; cursor:pointer;"
                                        x-on:click="activeTag = '{{ strtolower($tag->name) }}'"
                                    >{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Raison du rejet --}}
                        @if ($article->status === \App\Enums\ArticleStatus::Rejected && $article->rejection_reason)
                            <div class="alert alert-danger py-1 px-2 mb-2" style="font-size:.72rem">
                                <i class="ti ti-alert-triangle me-1"></i>
                                {{ Str::limit($article->rejection_reason, 80) }}
                            </div>
                        @endif
                    </div>

                    {{-- Footer carte --}}
                    <div class="card-footer bg-white border-top py-2 px-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex gap-3 text-muted" style="font-size:.75rem">
                            <span title="Date de création">
                                <i class="ti ti-calendar me-1"></i>{{ $article->created_at->format('d M Y') }}
                            </span>
                            <span title="Temps de lecture estimé">
                                <i class="ti ti-clock me-1"></i>{{ $readTime }} min
                            </span>
                            @if ($article->views_count > 0)
                                <span title="Vues">
                                    <i class="ti ti-eye me-1"></i>{{ number_format($article->views_count) }}
                                </span>
                            @endif
                        </div>

                        <div class="d-flex gap-1">
                            @if ($article->status === \App\Enums\ArticleStatus::Published)
                                <a href="{{ route('blog.show', $article->slug) }}" target="_blank"
                                   class="btn btn-light btn-sm px-2" title="Voir l'article publié">
                                    <i class="ti ti-external-link" style="font-size:1rem"></i>
                                </a>
                            @endif

                            {{-- Modifier (48h ou admin) --}}
                            @if ($article->canEditBy(auth()->user()))
                                <a href="{{ route('blog.articles.edit', $article) }}"
                                   class="btn btn-light btn-sm px-2 text-primary" title="Modifier l'article">
                                    <i class="ti ti-edit" style="font-size:1rem"></i>
                                </a>
                            @elseif ($article->isOwnedBy(auth()->user()))
                                <span class="btn btn-light btn-sm px-2 disabled text-muted"
                                      title="Modification verrouillée après 48h">
                                    <i class="ti ti-lock" style="font-size:1rem"></i>
                                </span>
                            @endif

                            @if (in_array($article->status, [\App\Enums\ArticleStatus::Draft, \App\Enums\ArticleStatus::Rejected]))
                                <form method="POST" action="{{ route('blog.articles.submit', $article) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-warning btn-sm px-2" title="Soumettre pour validation"
                                            onclick="return confirm('Soumettre cet article pour validation ?')">
                                        <i class="ti ti-send" style="font-size:1rem"></i>
                                    </button>
                                </form>
                            @endif
                            @if ($article->status === \App\Enums\ArticleStatus::Pending)
                                <span class="btn btn-light btn-sm px-2 disabled" title="En cours de validation">
                                    <i class="ti ti-hourglass" style="font-size:1rem; color:#f39c12"></i>
                                </span>
                            @endif

                            {{-- Supprimer — toujours disponible, sans limite de temps --}}
                            <form method="POST" action="{{ route('blog.articles.destroy', $article) }}" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-light btn-sm px-2 text-danger" title="Supprimer"
                                        onclick="return confirm('Supprimer définitivement cet article ?')">
                                    <i class="ti ti-trash" style="font-size:1rem"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm text-center py-5">
                    <div class="card-body">
                        <i class="ti ti-file-off d-block mb-3 text-muted" style="font-size:3rem"></i>
                        <p class="fw-semibold mb-2">
                            @if (request('status'))
                                Aucun article avec ce statut.
                            @else
                                Vous n'avez pas encore d'article.
                            @endif
                        </p>
                        <a href="{{ route('blog.create') }}" class="btn btn-primary btn-sm">
                            <i class="ti ti-plus me-1"></i>Rédiger votre premier article
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($articles->hasPages())
        <div class="mt-4">{{ $articles->links() }}</div>
    @endif
</div>

<style>
[x-cloak] { display: none !important; }
.card { transition: box-shadow .15s, transform .15s; }
.card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.1) !important; transform: translateY(-2px); }
</style>

@endsection
