@extends('layouts.dashboard')

@section('title', 'Mes questions — Laravel CI')

@section('content')

<x-dashboard.breadcrumb :items="[
    ['label' => 'Dashboard', 'href' => route('dashboard.member.overview')],
    ['label' => 'Mes questions'],
]" />

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- ── En-tête ─────────────────────────────────── --}}
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
    <div>
        <h1 class="fs-3 fw-bold mb-1">Mes questions</h1>
        <p class="text-muted mb-0">Toutes les questions que vous avez posées sur le forum.</p>
    </div>
    <a href="{{ route('forum.ask') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Poser une question
    </a>
</div>

{{-- ── Statistiques ────────────────────────────── --}}
@php
    $total    = $counts['total']    ?? 0;
    $open     = $counts['open']     ?? 0;
    $resolved = $counts['resolved'] ?? 0;
    $hidden   = $counts['hidden']   ?? 0;
@endphp

<div class="row g-3 mb-4">
    @foreach ([
        ['Total',     $total,    '#f0f4ff', '#4a6cf7', 'ti-message-circle-question'],
        ['Résolues',  $resolved, '#edfaf3', '#2ecc71', 'ti-circle-check'],
        ['Ouvertes',  $open,     '#fff5e0', '#f39c12', 'ti-help-circle'],
        ['Cachées',   $hidden,   '#f5f6fa', '#7f8c8d', 'ti-eye-off'],
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
     Filtres + Recherche + Cartes
     ═══════════════════════════════════════════════ --}}
<div x-data="{ search: '' }">

    {{-- ── Filtres + Recherche ──────────────────── --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">

            {{-- Onglets --}}
            <div class="d-flex gap-1 flex-wrap">
                @php
                $tabs = [
                    null       => ['Toutes',   $total,    ''],
                    'open'     => ['Ouvertes', $open,     'warning'],
                    'resolved' => ['Résolues', $resolved, 'success'],
                    'hidden'   => ['Cachées',  $hidden,   'secondary'],
                ];
                $activeFilter = request('filter');
                @endphp

                @foreach ($tabs as $key => [$lbl, $cnt, $col])
                    @php $on = ($key === $activeFilter || ($key === null && $activeFilter === null)); @endphp
                    <a href="{{ route('dashboard.member.questions', $key ? ['filter' => $key] : []) }}"
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

    {{-- ── Grille de cartes ────────────────────── --}}
    <div class="row g-3">
        @forelse ($questions as $question)
            @php
                $isResolved = $question->hasAcceptedAnswer();
                $isHidden   = $question->status === 'hidden';
                $borderColor = $isResolved ? '#2ecc71' : ($isHidden ? '#adb5bd' : '#f39c12');
                $titleSlug   = strtolower($question->title);
            @endphp

            <div
                class="col-md-6"
                x-show="search === '' || '{{ addslashes($titleSlug) }}'.includes(search.toLowerCase())"
            >
                <div class="card border-0 shadow-sm h-100 overflow-hidden"
                     style="border-left:4px solid {{ $borderColor }} !important; border-radius:.75rem;
                            transition:box-shadow .15s, transform .15s;"
                     onmouseover="this.style.boxShadow='0 6px 20px rgba(0,0,0,.1)';this.style.transform='translateY(-2px)'"
                     onmouseout="this.style.boxShadow='';this.style.transform=''">

                    <div class="card-body pb-2">

                        {{-- Statut + Métriques --}}
                        <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                            {{-- Badge statut --}}
                            @if ($isResolved)
                                <span class="badge d-inline-flex align-items-center gap-1"
                                      style="background:#edfaf3;color:#2ecc71;font-size:.72rem">
                                    <i class="ti ti-circle-check"></i>Résolue
                                </span>
                            @elseif ($isHidden)
                                <span class="badge bg-secondary-subtle text-secondary" style="font-size:.72rem">
                                    <i class="ti ti-eye-off me-1"></i>Cachée
                                </span>
                            @else
                                <span class="badge d-inline-flex align-items-center gap-1"
                                      style="background:#fff5e0;color:#f39c12;font-size:.72rem">
                                    <i class="ti ti-help-circle"></i>Ouverte
                                </span>
                            @endif

                            {{-- Votes + Réponses --}}
                            <div class="d-flex gap-2" style="font-size:.78rem">
                                <span class="d-inline-flex align-items-center gap-1"
                                      style="color:{{ $question->votes_score > 0 ? 'var(--orange,#e8590c)' : '#adb5bd' }}; font-weight:600">
                                    <i class="ti ti-arrow-up"></i>{{ $question->votes_score }}
                                </span>
                                <span class="d-inline-flex align-items-center gap-1 text-muted">
                                    <i class="ti ti-messages"></i>{{ $question->answers_count }}
                                </span>
                                @if (($question->views_count ?? 0) > 0)
                                    <span class="d-inline-flex align-items-center gap-1 text-muted">
                                        <i class="ti ti-eye"></i>{{ number_format($question->views_count) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Épinglé --}}
                        @if ($question->is_pinned)
                            <div class="mb-1" style="font-size:.72rem; color:#b7791f">
                                <i class="ti ti-pin me-1"></i>Épinglée par les modérateurs
                            </div>
                        @endif

                        {{-- Titre + badge Modifié --}}
                        <h6 class="fw-bold mb-1 lh-sm" style="font-size:.95rem">
                            <a href="{{ route('forum.show', $question->slug) }}"
                               class="text-dark text-decoration-none">
                                {{ Str::limit($question->title, 80) }}
                            </a>
                        </h6>
                        @if ($question->wasEdited())
                            <span class="badge bg-light border text-muted d-inline-flex align-items-center gap-1 mb-1"
                                  style="font-size:.68rem"
                                  title="Modifié le {{ $question->edited_at->format('d M Y à H:i') }}">
                                <i class="ti ti-pencil"></i>Modifié
                            </span>
                        @endif

                        {{-- Tags --}}
                        @if ($question->tags->isNotEmpty())
                            <div class="d-flex flex-wrap gap-1 mt-1">
                                @foreach ($question->tags as $tag)
                                    <span class="badge bg-light border text-secondary"
                                          style="font-size:.68rem; border-radius:.5rem">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="card-footer bg-white border-top py-2 px-3 d-flex align-items-center justify-content-between">
                        <span class="text-muted" style="font-size:.75rem">
                            <i class="ti ti-calendar me-1"></i>{{ $question->created_at->format('d M Y') }}
                        </span>

                        <div class="d-flex gap-1">
                            <a href="{{ route('forum.show', $question->slug) }}"
                               class="btn btn-light btn-sm px-2" title="Voir la question">
                                <i class="ti ti-external-link" style="font-size:1rem"></i>
                            </a>

                            {{-- Modifier (48h ou admin) --}}
                            @if ($question->canEditBy(auth()->user()))
                                <a href="{{ route('forum.edit', $question) }}"
                                   class="btn btn-light btn-sm px-2 text-primary" title="Modifier la question">
                                    <i class="ti ti-edit" style="font-size:1rem"></i>
                                </a>
                            @elseif ($question->isOwnedBy(auth()->user()))
                                <span class="btn btn-light btn-sm px-2 disabled text-muted"
                                      title="Modification verrouillée après 48h">
                                    <i class="ti ti-lock" style="font-size:1rem"></i>
                                </span>
                            @endif

                            <form method="POST" action="{{ route('forum.destroy', $question) }}" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="btn btn-light btn-sm px-2 text-danger"
                                        title="Supprimer"
                                        onclick="return confirm('Supprimer définitivement cette question ?')">
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
                        <i class="ti ti-message-circle-question d-block mb-3 text-muted"
                           style="font-size:3rem;opacity:.35"></i>
                        <p class="fw-semibold mb-2">
                            @if (request('filter'))
                                Aucune question avec ce filtre.
                            @else
                                Vous n'avez pas encore posé de question.
                            @endif
                        </p>
                        <a href="{{ route('forum.ask') }}" class="btn btn-primary btn-sm">
                            <i class="ti ti-plus me-1"></i>Poser votre première question
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($questions->hasPages())
        <div class="mt-4">{{ $questions->links() }}</div>
    @endif
</div>

<style>
[x-cloak] { display: none !important; }
</style>

@endsection
