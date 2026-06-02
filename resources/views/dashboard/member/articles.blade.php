@extends('layouts.dashboard')

@section('title', 'Mes articles — Laravel CI')

@section('content')

<x-dashboard.breadcrumb :items="[
    ['label' => 'Dashboard', 'href' => route('dashboard.member.overview')],
    ['label' => 'Mes articles'],
]" />

{{-- Flash --}}
@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ── En-tête ─────────────────────────────────── --}}
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
    $totalArticles   = array_sum($counts ?? []);
    $publishedCount  = $counts['published']  ?? 0;
    $pendingCount    = $counts['pending']    ?? 0;
    $draftCount      = $counts['draft']      ?? 0;
    $rejectedCount   = $counts['rejected']   ?? 0;
@endphp

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:44px;height:44px;background:#f0f4ff;">
                    <i class="ti ti-file-text" style="font-size:1.3rem;color:#4a6cf7"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1">{{ $totalArticles }}</div>
                    <div class="text-muted small">Total</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:44px;height:44px;background:#edfaf3;">
                    <i class="ti ti-circle-check" style="font-size:1.3rem;color:#2ecc71"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1">{{ $publishedCount }}</div>
                    <div class="text-muted small">Publiés</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:44px;height:44px;background:#fff5e0;">
                    <i class="ti ti-clock" style="font-size:1.3rem;color:#f39c12"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1">{{ $pendingCount }}</div>
                    <div class="text-muted small">En attente</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:44px;height:44px;background:#f5f6fa;">
                    <i class="ti ti-pencil" style="font-size:1.3rem;color:#7f8c8d"></i>
                </div>
                <div>
                    <div class="fw-bold fs-4 lh-1">{{ $draftCount }}</div>
                    <div class="text-muted small">Brouillons</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Filtres par statut + Recherche ──────────── --}}
<div class="card border-0 shadow-sm mb-0" x-data="{ search: '' }">

    <div class="card-header bg-white border-bottom px-4 py-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">

            {{-- Onglets statut --}}
            <div class="d-flex gap-1 flex-wrap">
                @php
                $filters = [
                    null         => ['Tous',        $totalArticles,  ''],
                    'draft'      => ['Brouillons',  $draftCount,     'secondary'],
                    'pending'    => ['En attente',  $pendingCount,   'warning'],
                    'published'  => ['Publiés',     $publishedCount, 'success'],
                    'rejected'   => ['Rejetés',     $rejectedCount,  'danger'],
                ];
                $activeFilter = request('status');
                @endphp

                @foreach ($filters as $filterKey => [$filterLabel, $filterCount, $filterColor])
                    @php $isActive = ($filterKey === $activeFilter || ($filterKey === null && $activeFilter === null)); @endphp
                    <a
                        href="{{ route('dashboard.member.articles', $filterKey ? ['status' => $filterKey] : []) }}"
                        class="btn btn-sm d-inline-flex align-items-center gap-1 {{ $isActive ? 'btn-primary' : 'btn-light' }}"
                        style="border-radius:2rem; font-size:.8rem;"
                    >
                        {{ $filterLabel }}
                        <span class="badge rounded-pill {{ $isActive ? 'bg-white text-primary' : 'bg-secondary-subtle text-secondary' }}"
                              style="font-size:.68rem">
                            {{ $filterCount }}
                        </span>
                    </a>
                @endforeach
            </div>

            {{-- Recherche live --}}
            <div class="input-group input-group-sm" style="max-width:240px;">
                <span class="input-group-text bg-white border-end-0">
                    <i class="ti ti-search text-muted"></i>
                </span>
                <input
                    type="text"
                    x-model="search"
                    class="form-control border-start-0 ps-0"
                    placeholder="Rechercher…"
                    style="box-shadow:none;"
                />
                <button x-show="search.length > 0" x-cloak
                        class="btn btn-outline-secondary btn-sm"
                        x-on:click="search = ''"
                        type="button">
                    <i class="ti ti-x"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- ── Liste des articles ────────────────────── --}}
    <div class="card-body p-0">
        @forelse ($articles as $article)
            @php
                $words       = str_word_count(strip_tags($article->body ?? ''));
                $readTime    = max(1, (int) round($words / 200));
                $statusMap   = [
                    'published' => ['bg-success-subtle text-success',     'ti-circle-check', 'Publié'],
                    'pending'   => ['bg-warning-subtle text-warning',     'ti-clock',        'En attente'],
                    'rejected'  => ['bg-danger-subtle text-danger',       'ti-x',            'Rejeté'],
                    'draft'     => ['bg-secondary-subtle text-secondary', 'ti-pencil',       'Brouillon'],
                ];
                [$sBadge, $sIcon, $sLabel] = $statusMap[$article->status] ?? $statusMap['draft'];
                $levelMap = [
                    'beginner'     => ['success', 'ti-seedling',  'Débutant'],
                    'intermediate' => ['warning', 'ti-chart-bar', 'Intermédiaire'],
                    'advanced'     => ['danger',  'ti-flame',     'Avancé'],
                ];
                [$lColor, $lIcon, $lLabel] = $levelMap[$article->level ?? 'beginner'] ?? $levelMap['beginner'];
            @endphp
            <div
                class="d-flex align-items-start gap-3 px-4 py-3 border-bottom article-row"
                style="transition:.1s;"
                x-show="search === '' || '{{ addslashes(strtolower($article->title)) }}'.includes(search.toLowerCase())"
            >
                {{-- Indicateur de niveau (barre colorée verticale) --}}
                <div class="flex-shrink-0 d-flex flex-column align-items-center" style="width:4px; min-height:60px;">
                    <div class="rounded-pill w-100 h-100" style="background:var(--bs-{{ $lColor }})"></div>
                </div>

                {{-- Miniature de couverture --}}
                @if ($article->cover_image)
                    <img src="{{ asset('assets/covers/' . $article->cover_image) }}"
                         alt="" class="rounded-2 flex-shrink-0 d-none d-sm-block"
                         style="width:56px;height:56px;object-fit:cover;" />
                @else
                    <div class="rounded-2 flex-shrink-0 d-none d-sm-flex align-items-center justify-content-center bg-light"
                         style="width:56px;height:56px;">
                        <i class="ti ti-file-text text-muted" style="font-size:1.4rem"></i>
                    </div>
                @endif

                {{-- Contenu principal --}}
                <div class="flex-grow-1 min-width-0">
                    <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap">
                        <div>
                            {{-- Titre --}}
                            @if ($article->status === 'published')
                                <a href="{{ route('blog.show', $article->slug) }}"
                                   class="fw-semibold text-dark text-decoration-none d-block mb-1"
                                   style="font-size:.95rem; line-height:1.35">
                                    {{ Str::limit($article->title, 70) }}
                                </a>
                            @else
                                <span class="fw-semibold d-block mb-1" style="font-size:.95rem; line-height:1.35">
                                    {{ Str::limit($article->title, 70) }}
                                </span>
                            @endif

                            {{-- Meta : niveau + tags + date + temps --}}
                            <div class="d-flex align-items-center gap-2 flex-wrap" style="font-size:.78rem;">
                                <span class="text-{{ $lColor }}">
                                    <i class="ti {{ $lIcon }} me-1"></i>{{ $lLabel }}
                                </span>
                                <span class="text-muted">·</span>
                                <span class="text-muted">
                                    <i class="ti ti-clock me-1"></i>{{ $readTime }} min
                                </span>
                                <span class="text-muted">·</span>
                                <span class="text-muted">
                                    <i class="ti ti-calendar me-1"></i>{{ $article->created_at->format('d M Y') }}
                                </span>
                                @if ($article->views_count > 0)
                                    <span class="text-muted">·</span>
                                    <span class="text-muted">
                                        <i class="ti ti-eye me-1"></i>{{ number_format($article->views_count) }}
                                    </span>
                                @endif
                            </div>

                            {{-- Tags --}}
                            @if ($article->tags->isNotEmpty())
                                <div class="d-flex gap-1 mt-1 flex-wrap">
                                    @foreach ($article->tags->take(4) as $tag)
                                        <span class="badge bg-light text-muted border" style="font-size:.68rem;border-radius:.5rem">
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Raison du rejet --}}
                            @if ($article->status === 'rejected' && $article->rejection_reason)
                                <div class="mt-1 d-flex align-items-start gap-1 text-danger" style="font-size:.75rem;">
                                    <i class="ti ti-alert-triangle flex-shrink-0 mt-1"></i>
                                    <span>{{ Str::limit($article->rejection_reason, 90) }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Statut + Actions --}}
                        <div class="d-flex flex-column align-items-end gap-2 flex-shrink-0">
                            <span class="badge {{ $sBadge }} d-inline-flex align-items-center gap-1" style="font-size:.72rem">
                                <i class="ti {{ $sIcon }}"></i>{{ $sLabel }}
                            </span>

                            <div class="d-flex gap-1">
                                {{-- Voir (publié) --}}
                                @if ($article->status === 'published')
                                    <a href="{{ route('blog.show', $article->slug) }}"
                                       target="_blank"
                                       class="btn btn-light btn-sm px-2" title="Voir l'article">
                                        <i class="ti ti-external-link" style="font-size:1rem"></i>
                                    </a>
                                @endif

                                {{-- Soumettre (brouillon / rejeté) --}}
                                @if (in_array($article->status, ['draft', 'rejected']))
                                    <form method="POST" action="{{ route('blog.articles.submit', $article) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm px-2" title="Soumettre pour validation"
                                                onclick="return confirm('Soumettre cet article pour validation ?')">
                                            <i class="ti ti-send" style="font-size:1rem"></i>
                                        </button>
                                    </form>
                                @endif

                                {{-- En attente --}}
                                @if ($article->status === 'pending')
                                    <span class="btn btn-light btn-sm px-2 disabled" title="En cours de validation">
                                        <i class="ti ti-hourglass" style="font-size:1rem;color:#f39c12"></i>
                                    </span>
                                @endif

                                {{-- Supprimer (non publié) --}}
                                @if ($article->status !== 'published')
                                    <form method="POST" action="{{ route('blog.articles.destroy', $article) }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-light btn-sm px-2 text-danger"
                                                title="Supprimer"
                                                onclick="return confirm('Supprimer définitivement cet article ?')">
                                            <i class="ti ti-trash" style="font-size:1rem"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="ti ti-file-text d-block mb-3" style="font-size:3rem;opacity:.35"></i>
                <p class="mb-3 fw-semibold">
                    @if (request('status'))
                        Aucun article avec ce statut.
                    @else
                        Vous n'avez pas encore d'article.
                    @endif
                </p>
                <a href="{{ route('blog.create') }}" class="btn btn-primary btn-sm">
                    <i class="ti ti-plus me-1"></i> Rédiger votre premier article
                </a>
            </div>
        @endforelse

        {{-- Message si aucun résultat côté recherche --}}
        <div class="text-center py-4 text-muted d-none"
             x-show="{{ $articles->isNotEmpty() ? 'true' : 'false' }} && search.length > 0 && document.querySelectorAll(\'.article-row[style*=\'display: none\']\').length === {{ $articles->count() }}"
             style="font-size:.88rem">
            <i class="ti ti-search me-1"></i>
            Aucun article ne correspond à votre recherche.
        </div>
    </div>

    {{-- ── Pagination ─────────────────────────────── --}}
    @if ($articles->hasPages())
        <div class="card-footer bg-white border-top-0 px-4 py-3">
            {{ $articles->links() }}
        </div>
    @endif
</div>

<style>
.article-row:hover { background: #fafbff; }
[x-cloak] { display: none !important; }
</style>

@endsection
