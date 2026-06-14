@extends('layouts.web')

@section('title', "Recherche : {$query} — Laravel CI")

@section('content')

    {{-- ===== EN-TÊTE ===== --}}
    <section class="page-hero">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="breadcrumb-bar">
                        <a href="{{ route('home') }}">Accueil</a>
                        <i class="fa-solid fa-chevron-right"></i>
                        <span>Recherche</span>
                    </div>
                    <h1 class="mb-2">Résultats de recherche</h1>

                    {{-- Barre de recherche --}}
                    <form method="GET" action="{{ route('search.index') }}" class="mb-3" style="max-width:500px">
                        <div class="search-input-wrapper">
                            <i class="fa-solid fa-magnifying-glass search-icon"></i>
                            <input type="text" name="q" value="{{ $query }}"
                                   placeholder="Rechercher questions, articles, offres..."
                                   class="search-input" autocomplete="off" />
                            @if ($type !== 'all')
                                <input type="hidden" name="type" value="{{ $type }}" />
                            @endif
                        </div>
                    </form>

                    {{-- Filtres par type --}}
                    <div class="filter-pills">
                        @foreach (['all' => 'Tout', 'questions' => 'Questions', 'articles' => 'Articles', 'jobs' => "Offres d'emploi"] as $value => $label)
                            <a href="{{ route('search.index', ['q' => $query, 'type' => $value]) }}"
                               class="filter-pill {{ $type === $value ? 'active' : '' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== RÉSULTATS ===== --}}
    <section class="section-sm">
        <div class="container">

            @if (strlen(trim($query)) < 2)
                <div class="card-soft h-auto p-5 text-center">
                    <i class="fa-solid fa-magnifying-glass fa-2x mb-3 text-muted-2"></i>
                    <p class="mb-0">Saisissez au moins 2 caractères pour lancer une recherche.</p>
                </div>
            @else
                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                    <span class="text-muted-2" style="font-size:.88rem">
                        <strong class="text-navy">{{ number_format($total) }}</strong>
                        résultat{{ $total !== 1 ? 's' : '' }} pour
                        <strong>&laquo;&nbsp;{{ $query }}&nbsp;&raquo;</strong>
                    </span>
                </div>

                @if ($total === 0)
                    <div class="card-soft h-auto p-5 text-center">
                        <i class="fa-solid fa-magnifying-glass fa-2x mb-3 text-muted-2"></i>
                        <p class="mb-0">Aucun résultat pour "{{ $query }}".</p>
                    </div>
                @else
                    <div class="d-flex flex-column gap-3">
                        @foreach ($results as $item)
                            @php
                                // Normalise l'item : ['type' => ..., 'model' => ...] pour "all", modèle direct sinon.
                                if (is_array($item)) {
                                    $resultType = $item['type'];
                                    $model      = $item['model'];
                                } else {
                                    $model = $item;
                                    $resultType = match ($type) {
                                        'questions' => 'question',
                                        'articles'  => 'article',
                                        'jobs'      => 'job',
                                        default     => 'question',
                                    };
                                }
                            @endphp

                            <div class="card-soft card-pad">
                                @switch($resultType)
                                    @case('question')
                                        <span class="badge-pill mb-2 d-inline-flex align-items-center gap-1"
                                              style="background:#fff5f0; color:var(--orange); font-size:.72rem; font-weight:700; border-radius:2rem; padding:.25rem .7rem; width:fit-content">
                                            <i class="fa-solid fa-comments"></i> Forum
                                        </span>
                                        <h3 class="art-title mb-1" style="font-size:1.05rem">
                                            <a href="{{ route('forum.show', $model->slug) }}">{{ $model->title }}</a>
                                        </h3>
                                        <p class="art-excerpt mb-2" style="font-size:.85rem; color:var(--muted)">
                                            {{ Str::limit(strip_tags($model->body), 160) }}
                                        </p>
                                        <div class="d-flex gap-3 align-items-center" style="font-size:.78rem; color:var(--muted)">
                                            <span><i class="fa-solid fa-arrow-up me-1"></i>{{ $model->votes_score }} votes</span>
                                            <span><i class="fa-regular fa-comment me-1"></i>{{ $model->answers_count }} réponses</span>
                                        </div>
                                        @if ($model->tags->isNotEmpty())
                                            <div class="q-tags mt-2">
                                                @foreach ($model->tags->take(3) as $tag)
                                                    <span class="tag" style="font-size:.72rem">{{ $tag->name }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                        @break

                                    @case('article')
                                        <span class="badge-pill mb-2 d-inline-flex align-items-center gap-1"
                                              style="background:#edfaf3; color:var(--green,#2ecc71); font-size:.72rem; font-weight:700; border-radius:2rem; padding:.25rem .7rem; width:fit-content">
                                            <i class="fa-solid fa-book-open"></i>
                                            {{ $model->level->label() }}
                                        </span>
                                        <h3 class="art-title mb-1" style="font-size:1.05rem">
                                            <a href="{{ route('blog.show', $model->slug) }}">{{ $model->title }}</a>
                                        </h3>
                                        @if ($model->excerpt)
                                            <p class="art-excerpt mb-2" style="font-size:.85rem; color:var(--muted)">
                                                {{ Str::limit($model->excerpt, 160) }}
                                            </p>
                                        @endif
                                        <div class="d-flex gap-3 align-items-center" style="font-size:.78rem; color:var(--muted)">
                                            <span><i class="fa-regular fa-user me-1"></i>{{ $model->author?->name }}</span>
                                            <span><i class="fa-regular fa-clock me-1"></i>
                                                {{ max(1, (int) round(str_word_count(strip_tags($model->body ?? '')) / 200)) }} min
                                            </span>
                                        </div>
                                        @if ($model->tags->isNotEmpty())
                                            <div class="q-tags mt-2">
                                                @foreach ($model->tags->take(3) as $tag)
                                                    <span class="tag" style="font-size:.72rem">{{ $tag->name }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                        @break

                                    @case('job')
                                        <span class="badge-pill mb-2 d-inline-flex align-items-center gap-1"
                                              style="background:#eef2ff; color:#4361ee; font-size:.72rem; font-weight:700; border-radius:2rem; padding:.25rem .7rem; width:fit-content">
                                            <i class="fa-solid fa-briefcase"></i> Offre d'emploi
                                        </span>
                                        <h3 class="art-title mb-1" style="font-size:1.05rem">
                                            <a href="{{ route('jobs.show', $model->slug) }}">{{ $model->title }}</a>
                                        </h3>
                                        <p class="art-excerpt mb-2" style="font-size:.85rem; color:var(--muted)">
                                            {{ Str::limit(strip_tags($model->description), 160) }}
                                        </p>
                                        <div class="d-flex gap-3 align-items-center flex-wrap" style="font-size:.78rem; color:var(--muted)">
                                            <span><i class="fa-regular fa-building me-1"></i>{{ $model->company?->name }}</span>
                                            <span><i class="fa-solid fa-file-contract me-1"></i>{{ $model->contract_type->label() }}</span>
                                            <span><i class="fa-solid fa-layer-group me-1"></i>{{ $model->level->label() }}</span>
                                            @if ($model->is_remote)
                                                <span class="badge-pill" style="background:#edfaf3; color:var(--green,#2ecc71); font-size:.7rem; padding:.15rem .5rem; border-radius:2rem">
                                                    <i class="fa-solid fa-house-laptop"></i> Télétravail
                                                </span>
                                            @elseif ($model->location)
                                                <span><i class="fa-solid fa-location-dot me-1"></i>{{ $model->location }}</span>
                                            @endif
                                        </div>
                                        @break
                                @endswitch
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if ($results instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $results->links() }}
                        </div>
                    @endif
                @endif
            @endif
        </div>
    </section>

@endsection
