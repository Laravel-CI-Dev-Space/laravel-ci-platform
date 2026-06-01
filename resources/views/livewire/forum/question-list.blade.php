<div>
    <section class="section-sm">
        <div class="container">
            <button class="btn btn-ghost d-lg-none mb-3 w-100" type="button" data-bs-toggle="collapse" data-bs-target="#forumSidebar">
                <i class="fa-solid fa-sliders"></i> Filtres &amp; tags
            </button>

            <div class="row g-4">
                {{-- SIDEBAR --}}
                <div class="col-lg-3">
                    <div class="collapse d-lg-block" id="forumSidebar">
                        <div class="sidebar-card">
                            <div class="search-field">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input
                                    type="search"
                                    wire:model.live.debounce.400ms="search"
                                    class="form-control"
                                    placeholder="Rechercher…"
                                />
                            </div>
                        </div>

                        <div class="sidebar-card">
                            <div class="sidebar-title">Trier par</div>
                            <div class="filter-pills">
                                <button
                                    wire:click="$set('sort','recent')"
                                    class="filter-pill {{ $sort === 'recent' ? 'active' : '' }}"
                                >
                                    Récentes
                                </button>
                                <button
                                    wire:click="$set('sort','votes')"
                                    class="filter-pill {{ $sort === 'votes' ? 'active' : '' }}"
                                >
                                    Populaires
                                </button>
                                <button
                                    wire:click="$set('sort','unanswered')"
                                    class="filter-pill {{ $sort === 'unanswered' ? 'active' : '' }}"
                                >
                                    Sans réponse
                                </button>
                            </div>
                        </div>

                        <div class="sidebar-card">
                            <div class="sidebar-title">Tags populaires</div>
                            <div class="tag-list">
                                @foreach ($tags->take(10) as $tag)
                                    <div
                                        wire:click="$set('tagId', {{ $tagId === $tag->id ? 'null' : $tag->id }})"
                                        class="tag-list-item {{ $tagId === $tag->id ? 'active' : '' }}"
                                        style="cursor:pointer"
                                    >
                                        <span class="mono">{{ $tag->name }}</span>
                                        <span class="count">{{ number_format($tag->usage_count) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- MAIN --}}
                <div class="col-lg-9">
                    <div class="d-flex justify-content-between align-items-center mb-3">
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

                    @forelse ($questions as $question)
                        <div class="q-card {{ $question->is_pinned ? 'pinned' : '' }}">
                            <div class="q-stats">
                                <div class="q-vote">
                                    <span>{{ $question->votes_score }}</span>
                                    <small>votes</small>
                                </div>
                                <div class="q-answers {{ $question->hasAcceptedAnswer() ? 'accepted' : '' }}">
                                    <strong>{{ $question->answers_count }}</strong>
                                    réponses
                                </div>
                            </div>
                            <div class="q-body">
                                @if ($question->is_pinned)
                                    <div class="pin-flag">
                                        <i class="fa-solid fa-thumbtack"></i> Épinglé par les modérateurs
                                    </div>
                                @endif
                                <h3 class="q-title">
                                    <a href="{{ route('forum.show', $question->slug) }}">
                                        {{ $question->title }}
                                    </a>
                                </h3>
                                <div class="q-tags">
                                    @foreach ($question->tags as $tag)
                                        <span
                                            class="tag"
                                            wire:click="$set('tagId', {{ $tag->id }})"
                                            style="cursor:pointer"
                                        >
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                                <div class="q-foot">
                                    <div class="author-row">
                                        @if ($question->user->avatar)
                                            <img
                                                src="{{ $question->user->avatar }}"
                                                class="avatar avatar-sm"
                                                alt="{{ $question->user->name }}"
                                            />
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
                        <div class="card-soft p-5 text-center">
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
