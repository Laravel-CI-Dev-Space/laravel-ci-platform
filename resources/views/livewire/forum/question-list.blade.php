<div>
    <section class="section-sm">
        <div class="container">

            <button class="btn btn-ghost d-lg-none mb-3 w-100" type="button"
                    data-bs-toggle="collapse" data-bs-target="#forumSidebar">
                <i class="fa-solid fa-sliders me-1"></i> Filtres &amp; tags
            </button>

            <div class="row g-4">

                {{-- ═══ SIDEBAR ═══ --}}
                <div class="col-lg-3" style="align-self:flex-start;">
                    <div class="collapse d-lg-block" id="forumSidebar"
                         style="position:sticky; top:1.5rem;">

                        {{-- Recherche --}}
                        <div class="sidebar-card">
                            <div class="search-field">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="search"
                                       wire:model.live.debounce.400ms="search"
                                       class="form-control"
                                       placeholder="Rechercher…" />
                            </div>
                        </div>

                        {{-- Tri --}}
                        <div class="sidebar-card">
                            <div class="sidebar-title">Trier par</div>
                            <div class="filter-pills">
                                @foreach ([
                                    'recent'     => ['Récentes',      'fa-clock-rotate-left'],
                                    'votes'      => ['Populaires',    'fa-fire'],
                                    'unanswered' => ['Sans réponse',  'fa-circle-question'],
                                ] as $value => [$label, $icon])
                                    <button wire:click="$set('sort','{{ $value }}')"
                                            class="filter-pill {{ $sort === $value ? 'active' : '' }}">
                                        <i class="fa-solid {{ $icon }} me-1"></i>{{ $label }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Tags --}}
                        <div class="sidebar-card" x-data="{ shown: 5 }">
                            <div class="sidebar-title">
                                Tags populaires
                                @if ($tagId !== null)
                                    <button wire:click="$set('tagId', null)"
                                            class="btn btn-ghost btn-sm py-0 float-end"
                                            style="font-size:.72rem">
                                        <i class="fa-solid fa-xmark"></i> Effacer
                                    </button>
                                @endif
                            </div>
                            <div class="tag-list">
                                @foreach ($tags as $tag)
                                    <div wire:click="$set('tagId', {{ $tagId === $tag->id ? 'null' : $tag->id }})"
                                         class="tag-list-item {{ $tagId === $tag->id ? 'active' : '' }}"
                                         style="cursor:pointer"
                                         x-show="{{ $loop->index }} < shown">
                                        <span class="mono">{{ $tag->name }}</span>
                                        <span class="count">{{ number_format($tag->usage_count) }}</span>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Voir plus / Voir moins --}}
                            @php $totalTags = $tags->count(); @endphp
                            @if ($totalTags > 5)
                                <button
                                    x-show="shown < {{ $totalTags }}"
                                    x-on:click="shown = Math.min(shown + 5, {{ $totalTags }})"
                                    class="btn btn-ghost btn-sm w-100 mt-1"
                                    style="font-size:.78rem; color:var(--orange)"
                                >
                                    <i class="fa-solid fa-chevron-down me-1"></i>
                                    Voir plus
                                    <span x-text="'(' + Math.min(5, {{ $totalTags }} - shown) + ' de plus)'"></span>
                                </button>
                                <button
                                    x-show="shown >= {{ $totalTags }}"
                                    x-on:click="shown = 5"
                                    class="btn btn-ghost btn-sm w-100 mt-1"
                                    style="font-size:.78rem; color:var(--muted)"
                                >
                                    <i class="fa-solid fa-chevron-up me-1"></i>Voir moins
                                </button>
                            @endif
                        </div>

                        {{-- CTA --}}
                        @auth
                            <div class="sidebar-card text-center">
                                <p class="text-muted-2 mb-2" style="font-size:.82rem">Vous avez une question ?</p>
                                <a href="{{ route('forum.ask') }}" class="btn btn-brand btn-sm w-100">
                                    <i class="fa-solid fa-pen-to-square me-1"></i>Poser une question
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>

                {{-- ═══ LISTE QUESTIONS ═══ --}}
                <div class="col-lg-9">

                    {{-- Barre résultats --}}
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <span class="text-muted-2">
                            <strong class="text-navy">{{ number_format($questions->total()) }}</strong>
                            question{{ $questions->total() !== 1 ? 's' : '' }}
                            @if ($search !== '')
                                pour « {{ $search }} »
                            @endif
                        </span>
                        @if ($tagId !== null)
                            <button wire:click="$set('tagId', null)" class="btn btn-ghost btn-sm">
                                <i class="fa-solid fa-xmark me-1"></i>Effacer le filtre
                            </button>
                        @endif
                    </div>

                    {{-- Questions --}}
                    @forelse ($questions as $question)
                        @php
                            $isAnswered = $question->hasAcceptedAnswer();
                        @endphp
                        <div class="q-card {{ $question->is_pinned ? 'pinned' : '' }}">

                            {{-- Stats --}}
                            <div class="q-stats">
                                <div class="q-vote">
                                    <span style="{{ $question->votes_score > 0 ? 'color:var(--orange,#e8590c);font-weight:700' : '' }}">
                                        {{ $question->votes_score }}
                                    </span>
                                    <small>votes</small>
                                </div>
                                <div class="q-answers {{ $isAnswered ? 'accepted' : '' }}">
                                    <strong>{{ $question->answers_count }}</strong>
                                    réponses
                                </div>
                                @if (($question->views_count ?? 0) > 0)
                                    <div style="text-align:center; margin-top:.3rem">
                                        <span style="font-size:.8rem; color:var(--muted); font-weight:600">
                                            {{ number_format($question->views_count) }}
                                        </span>
                                        <small style="display:block; font-size:.65rem; color:var(--muted)">vues</small>
                                    </div>
                                @endif
                            </div>

                            {{-- Corps --}}
                            <div class="q-body">
                                @if ($question->is_pinned)
                                    <div class="pin-flag">
                                        <i class="fa-solid fa-thumbtack me-1"></i>Épinglé par les modérateurs
                                    </div>
                                @endif

                                {{-- Badge résolu --}}
                                @if ($isAnswered)
                                    <span class="badge mb-1 d-inline-flex align-items-center gap-1"
                                          style="background:#edfaf3;color:#2ecc71;font-size:.72rem;border-radius:2rem;padding:.2rem .6rem">
                                        <i class="fa-solid fa-circle-check"></i> Résolue
                                    </span>
                                @endif

                                <h3 class="q-title">
                                    <a href="{{ route('forum.show', $question->slug) }}">
                                        {{ $question->title }}
                                    </a>
                                </h3>

                                <div class="q-tags">
                                    @foreach ($question->tags as $tag)
                                        <span class="tag" wire:click="$set('tagId', {{ $tag->id }})"
                                              style="cursor:pointer">
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>

                                <div class="q-foot">
                                    <div class="author-row">
                                        @if ($question->user->avatar)
                                            <img src="{{ $question->user->avatar }}"
                                                 class="avatar avatar-sm"
                                                 alt="{{ $question->user->name }}" />
                                        @else
                                            <span class="avatar avatar-sm av-1">
                                                {{ strtoupper(substr($question->user->name, 0, 2)) }}
                                            </span>
                                        @endif
                                        <div class="meta">
                                            <div class="name">{{ $question->user->name }}</div>
                                        </div>
                                    </div>
                                    <span class="read-time">
                                        <i class="fa-regular fa-clock"></i>
                                        {{ $question->last_activity_at?->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="card-soft h-auto p-5 text-center">
                            <i class="fa-regular fa-comment-dots fa-2x mb-3 text-muted-2"></i>
                            <p class="mb-3">Aucune question trouvée.</p>
                            @auth
                                <a href="{{ route('forum.ask') }}" class="btn btn-brand">
                                    <i class="fa-solid fa-circle-plus"></i> Poser la première question
                                </a>
                            @endauth
                        </div>
                    @endforelse

                    <div class="mt-4 d-flex justify-content-center">
                        {{ $questions->links() }}
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>
