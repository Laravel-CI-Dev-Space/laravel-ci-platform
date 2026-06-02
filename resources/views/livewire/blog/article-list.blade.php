<div>
    {{-- ===== EN-TÊTE ===== --}}
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
                    <p class="lead mb-3">Tutoriels, analyses et ressources rédigés par la communauté Laravel ivoirienne.</p>
                    {{-- Niveau --}}
                    <div class="filter-pills">
                        @foreach (['all' => 'Tous', 'beginner' => 'Débutant', 'intermediate' => 'Intermédiaire', 'advanced' => 'Avancé'] as $value => $label)
                            <button wire:click="$set('level','{{ $value }}')"
                                    class="filter-pill {{ $level === $value ? 'active' : '' }}">
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

    {{-- ===== LAYOUT PRINCIPAL ===== --}}
    <section class="section-sm">
        <div class="container">

            {{-- Toggle mobile sidebar --}}
            <button class="btn btn-ghost d-lg-none mb-3 w-100" type="button"
                    data-bs-toggle="collapse" data-bs-target="#blogSidebar">
                <i class="fa-solid fa-sliders me-1"></i> Filtres &amp; tags
            </button>

            <div class="row g-4">

                {{-- ═══ SIDEBAR FIXE ═══ --}}
                <div class="col-lg-3" style="align-self:flex-start;">
                    <div class="collapse d-lg-block" id="blogSidebar"
                         style="position:sticky; top:1.5rem;">

                        {{-- Tri --}}
                        <div class="sidebar-card">
                            <div class="sidebar-title">Trier par</div>
                            <div class="filter-pills flex-column align-items-start">
                                @foreach (['recent' => ['Récents', 'fa-clock-rotate-left'], 'popular' => ['Populaires', 'fa-fire'], 'most-viewed' => ['Les plus vus', 'fa-eye']] as $value => [$label, $icon])
                                    <button wire:click="$set('sort','{{ $value }}')"
                                            class="filter-pill w-100 text-start {{ $sort === $value ? 'active' : '' }}"
                                            style="margin-bottom:.3rem">
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

                        {{-- Écrire --}}
                        @auth
                            <div class="sidebar-card text-center">
                                <p class="text-muted-2 mb-2" style="font-size:.82rem">
                                    Partagez vos connaissances avec la communauté
                                </p>
                                <a href="{{ route('blog.create') }}" class="btn btn-brand btn-sm w-100">
                                    <i class="fa-solid fa-pen-to-square me-1"></i> Rédiger un article
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>

                {{-- ═══ GRILLE ARTICLES (3 par ligne) ═══ --}}
                <div class="col-lg-9">

                    {{-- Compteur --}}
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                        <span class="text-muted-2" style="font-size:.88rem">
                            <strong class="text-navy">{{ number_format($articles->total()) }}</strong>
                            article{{ $articles->total() !== 1 ? 's' : '' }}
                            @if ($level !== 'all')
                                · <strong>{{ ['beginner' => 'Débutant', 'intermediate' => 'Intermédiaire', 'advanced' => 'Avancé'][$level] }}</strong>
                            @endif
                            @if ($tagId !== null)
                                · tag actif
                            @endif
                        </span>
                    </div>

                    @forelse ($articles as $article)
                        @php
                            $levelLabels = ['beginner' => 'Débutant', 'intermediate' => 'Intermédiaire', 'advanced' => 'Avancé'];
                            $levelColors = ['beginner' => 'var(--green,#2ecc71)', 'intermediate' => 'var(--orange,#e8590c)', 'advanced' => 'var(--level-advanced,#e74c3c)'];
                            $levelBg     = ['beginner' => '#edfaf3', 'intermediate' => '#fff5f0', 'advanced' => '#fdeaec'];
                            $readTime    = max(1, (int) round(str_word_count(strip_tags($article->body ?? '')) / 200));
                        @endphp
                        <div class="col-12 col-sm-6 col-xl-4 d-inline-flex" style="vertical-align:top; margin-bottom:1.25rem; padding:0 .4rem; width:33.333%;">
                            <article class="card-soft article-card w-100 d-flex flex-column overflow-hidden"
                                     style="border-top:3px solid {{ $levelColors[$article->level] ?? 'var(--orange)' }}; border-radius:.85rem;">

                                {{-- Cover image --}}
                                @if ($article->cover_image)
                                    <a href="{{ route('blog.show', $article->slug) }}">
                                        <img src="{{ asset('assets/covers/' . $article->cover_image) }}"
                                             alt="{{ $article->title }}"
                                             style="width:100%; height:150px; object-fit:cover;" />
                                    </a>
                                @endif

                                <div class="card-pad flex-grow-1 d-flex flex-column">
                                    {{-- Badge niveau --}}
                                    <span class="badge-pill mb-2 d-inline-flex align-items-center gap-1"
                                          style="background:{{ $levelBg[$article->level] ?? '#f5f5f5' }}; color:{{ $levelColors[$article->level] ?? 'var(--orange)' }}; font-size:.72rem; font-weight:700; border-radius:2rem; padding:.25rem .7rem; width:fit-content">
                                        {{ $levelLabels[$article->level] ?? $article->level }}
                                    </span>

                                    {{-- Titre --}}
                                    <h3 class="art-title mb-1" style="font-size:1rem; line-height:1.35">
                                        <a href="{{ route('blog.show', $article->slug) }}">
                                            {{ Str::limit($article->title, 65) }}
                                        </a>
                                    </h3>

                                    {{-- Extrait --}}
                                    @if ($article->excerpt)
                                        <p class="art-excerpt mb-2" style="font-size:.83rem; line-height:1.55; color:var(--muted)">
                                            {{ Str::limit($article->excerpt, 90) }}
                                        </p>
                                    @endif

                                    {{-- Tags --}}
                                    @if ($article->tags->isNotEmpty())
                                        <div class="q-tags mb-auto">
                                            @foreach ($article->tags->take(3) as $tag)
                                                <span class="tag" style="cursor:pointer; font-size:.72rem"
                                                      wire:click="$set('tagId', {{ $tag->id }})">
                                                    {{ $tag->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Auteur + Meta --}}
                                    <div class="art-foot mt-2 pt-2" style="border-top:1px solid var(--border,#eef0f4)">
                                        <div class="author-row">
                                            @if ($article->author->avatar)
                                                <img src="{{ $article->author->avatar }}"
                                                     class="avatar avatar-sm"
                                                     alt="{{ $article->author->name }}" />
                                            @else
                                                <span class="avatar avatar-sm av-1">
                                                    {{ strtoupper(substr($article->author->name, 0, 2)) }}
                                                </span>
                                            @endif
                                            <div class="meta">
                                                <div class="name" style="font-size:.8rem">{{ $article->author->name }}</div>
                                            </div>
                                        </div>
                                        <div class="d-flex gap-2 align-items-center" style="font-size:.75rem; color:var(--muted)">
                                            <span><i class="fa-regular fa-clock me-1"></i>{{ $readTime }} min</span>
                                            @if ($article->views_count > 0)
                                                <span><i class="fa-regular fa-eye me-1"></i>{{ number_format($article->views_count) }}</span>
                                            @endif
                                        </div>
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
                    <div class="mt-4 d-flex justify-content-center" style="clear:both">
                        {{ $articles->links() }}
                    </div>

                    {{-- Ressources --}}
                    <div class="mt-5 pt-4 text-center">
                        <p class="text-muted-2">Vous cherchez des ressources téléchargeables ?</p>
                        <a href="{{ route('resources.index') }}" class="btn btn-outline-brand">
                            <i class="fa-solid fa-download me-1"></i> Voir toutes les ressources
                        </a>
                    </div>
                </div>{{-- /col-lg-9 --}}
            </div>{{-- /row --}}
        </div>
    </section>
</div>
