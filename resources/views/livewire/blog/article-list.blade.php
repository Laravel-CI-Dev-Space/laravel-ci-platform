<div>
    {{-- ===== EN-TÊTE DE PAGE ===== --}}
    <section class="page-hero">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="breadcrumb-bar">
                        <a href="{{ route('home') }}">Accueil</a>
                        <i class="fa-solid fa-chevron-right"></i>
                        <span>Blog</span>
                    </div>
                    <h1 class="mb-2">Blog &amp; Ressources</h1>
                    <p class="lead mb-4">Tutoriels, analyses et ressources téléchargeables rédigés par la communauté Laravel ivoirienne.</p>

                    {{-- Filtres par niveau --}}
                    <div class="filter-pills">
                        @foreach (['all' => 'Tous', 'beginner' => 'Débutant', 'intermediate' => 'Intermédiaire', 'advanced' => 'Avancé'] as $value => $label)
                            <button
                                wire:click="$set('level', '{{ $value }}')"
                                class="filter-pill {{ $level === $value ? 'active' : '' }}"
                            >
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-4 d-none d-lg-block">
                    <div class="mascot-art">
                        <span class="m-ring"></span><span class="m-blob"></span>
                        <img src="{{ asset('assets/web/img/mascot.png') }}" alt="Mascotte Laravel CI" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== CONTENU PRINCIPAL ===== --}}
    <section class="section">
        <div class="container">

            {{-- Barre de filtres secondaires --}}
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    {{-- Filtre par tag --}}
                    @foreach ($tags->take(8) as $tag)
                        <button
                            wire:click="$set('tagId', {{ $tagId === $tag->id ? 'null' : $tag->id }})"
                            class="badge-pill {{ $tagId === $tag->id ? 'badge-active' : '' }}"
                            style="cursor:pointer; background:{{ $tagId === $tag->id ? ($tag->color ?? 'var(--orange)') : 'var(--light)' }}; color:{{ $tagId === $tag->id ? '#fff' : 'var(--navy)' }}; border:none; padding:.3rem .8rem; border-radius:2rem; font-size:.82rem;"
                        >
                            {{ $tag->name }}
                        </button>
                    @endforeach

                    @if ($tagId !== null)
                        <button wire:click="$set('tagId', null)" class="btn btn-ghost btn-sm">
                            <i class="fa-solid fa-xmark me-1"></i>Effacer
                        </button>
                    @endif
                </div>

                {{-- Tri --}}
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted-2" style="font-size:.85rem">Trier :</span>
                    @foreach (['recent' => 'Récents', 'popular' => 'Populaires', 'most-viewed' => 'Les plus vus'] as $value => $label)
                        <button
                            wire:click="$set('sort', '{{ $value }}')"
                            class="filter-pill {{ $sort === $value ? 'active' : '' }}"
                            style="font-size:.82rem;"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Compteur --}}
            <p class="text-muted-2 mb-4">
                <strong class="text-navy">{{ number_format($articles->total()) }}</strong>
                article{{ $articles->total() !== 1 ? 's' : '' }}
                @if ($level !== 'all')
                    · niveau
                    <strong>{{ ['beginner' => 'débutant', 'intermediate' => 'intermédiaire', 'advanced' => 'avancé'][$level] ?? $level }}</strong>
                @endif
            </p>

            {{-- Grille d'articles --}}
            @forelse ($articles as $article)
                <div class="col-md-6 col-lg-4 reveal" style="display:inline-block; width:100%;">
                    <article class="card-soft article-card mb-4">
                        <div class="level-banner lv-{{ $article->level }}"></div>
                        @if ($article->cover_image)
                            <img
                                src="{{ asset('assets/covers/' . $article->cover_image) }}"
                                alt="{{ $article->title }}"
                                style="width:100%; height:160px; object-fit:cover;"
                            />
                        @endif
                        <div class="card-pad">
                            <span class="badge-pill badge-{{ $article->level === 'beginner' ? 'green' : ($article->level === 'intermediate' ? 'orange' : 'red') }}">
                                <span class="lv-dot" style="background:var(--level-{{ $article->level }})"></span>
                                {{ ['beginner' => 'Débutant', 'intermediate' => 'Intermédiaire', 'advanced' => 'Avancé'][$article->level] ?? $article->level }}
                            </span>
                            <h3 class="art-title">
                                <a href="{{ route('blog.show', $article->slug) }}">{{ $article->title }}</a>
                            </h3>
                            @if ($article->excerpt)
                                <p class="art-excerpt">{{ $article->excerpt }}</p>
                            @endif
                            <div class="q-tags">
                                @foreach ($article->tags->take(3) as $tag)
                                    <span
                                        class="tag"
                                        wire:click="$set('tagId', {{ $tag->id }})"
                                        style="cursor:pointer"
                                    >
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                            <div class="art-foot">
                                <div class="author-row">
                                    @if ($article->author->avatar)
                                        <img
                                            src="{{ $article->author->avatar }}"
                                            class="avatar avatar-sm"
                                            alt="{{ $article->author->name }}"
                                        />
                                    @else
                                        <span class="avatar avatar-sm av-1">
                                            {{ strtoupper(substr($article->author->name, 0, 2)) }}
                                        </span>
                                    @endif
                                    <div class="meta">
                                        <div class="name">{{ $article->author->name }}</div>
                                    </div>
                                </div>
                                <span class="read-time">
                                    <i class="fa-regular fa-clock"></i>
                                    {{ $article->published_at?->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </article>
                </div>
            @empty
                <div class="card-soft p-5 text-center">
                    <i class="fa-regular fa-newspaper fa-2x mb-3 text-muted-2"></i>
                    <p class="mb-3">Aucun article trouvé.</p>
                    @auth
                        <a href="{{ route('blog.create') }}" class="btn btn-brand">
                            <i class="fa-solid fa-circle-plus"></i> Rédiger le premier article
                        </a>
                    @endauth
                </div>
            @endforelse

            {{-- Pagination --}}
            <div class="mt-4 d-flex justify-content-center">
                {{ $articles->links() }}
            </div>

            {{-- Lien vers les ressources --}}
            <div class="mt-5 pt-4 text-center">
                <p class="text-muted-2">Vous cherchez des ressources téléchargeables ?</p>
                <a href="{{ route('resources.index') }}" class="btn btn-outline-brand">
                    <i class="fa-solid fa-download me-1"></i> Voir toutes les ressources
                </a>
            </div>
        </div>
    </section>
</div>
