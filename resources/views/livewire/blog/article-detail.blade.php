<div>
    {{-- ===== EN-TÊTE ARTICLE ===== --}}
    <header class="page-hero">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="breadcrumb-bar">
                        <a href="{{ route('home') }}">Accueil</a>
                        <i class="fa-solid fa-chevron-right"></i>
                        <a href="{{ route('blog.index') }}">Blog</a>
                        <i class="fa-solid fa-chevron-right"></i>
                        <span>{{ $article->tags->first()->name ?? 'Article' }}</span>
                    </div>

                    {{-- Badge niveau --}}
                    @php
                        $levelLabels = ['beginner' => 'Débutant', 'intermediate' => 'Intermédiaire', 'advanced' => 'Avancé'];
                        $levelClasses = ['beginner' => 'badge-green', 'intermediate' => 'badge-orange', 'advanced' => ''];
                    @endphp
                    <span class="badge-pill {{ $levelClasses[$article->level] ?? '' }}" style="{{ $article->level === 'advanced' ? 'background:#fdeaec;color:var(--level-advanced)' : '' }}">
                        <span class="lv-dot" style="background:var(--level-{{ $article->level }})"></span>
                        {{ $levelLabels[$article->level] ?? $article->level }}
                    </span>

                    <h1 class="mt-3">{{ $article->title }}</h1>

                    <div class="cover-meta">
                        <div class="author-row">
                            @if ($article->author->avatar)
                                <img src="{{ $article->author->avatar }}" class="avatar" alt="{{ $article->author->name }}" />
                            @else
                                <span class="avatar av-1">{{ strtoupper(substr($article->author->name, 0, 2)) }}</span>
                            @endif
                            <div class="meta">
                                <div class="name">{{ $article->author->name }}</div>
                            </div>
                        </div>
                        <span class="text-muted-2">
                            <i class="fa-regular fa-calendar me-1 text-orange"></i>
                            {{ $article->published_at?->format('d M Y') }}
                        </span>
                        <span class="text-muted-2">
                            <i class="fa-regular fa-eye me-1 text-orange"></i>
                            {{ number_format($article->views_count) }} vues
                        </span>
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
    </header>

    {{-- ===== CONTENU PRINCIPAL ===== --}}
    <section class="section">
        <div class="container">
            <div class="row g-4 gx-lg-5">

                {{-- ===== SIDEBAR ===== --}}
                <div class="col-lg-4 order-lg-2">
                    <div class="toc-card">
                        <div class="sidebar-title">Tags</div>
                        <div class="q-tags mb-3">
                            @foreach ($article->tags as $tag)
                                <a href="{{ route('blog.index', ['tagId' => $tag->id]) }}" class="tag">
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        </div>

                        {{-- Partage --}}
                        <div class="sidebar-title mt-3">Partager</div>
                        @php
                            $shareUrl  = urlencode(request()->url());
                            $shareText = urlencode($article->title . ' — Laravel CI');
                        @endphp
                        <div class="d-flex gap-2 mt-2">
                            <a
                                href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}"
                                target="_blank"
                                rel="noopener"
                                class="social-icon"
                                style="background:var(--light);color:#0a66c2"
                                aria-label="Partager sur LinkedIn"
                            >
                                <i class="fa-brands fa-linkedin-in"></i>
                            </a>
                            <a
                                href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareText }}"
                                target="_blank"
                                rel="noopener"
                                class="social-icon"
                                style="background:var(--light);color:var(--navy)"
                                aria-label="Partager sur X (Twitter)"
                            >
                                <i class="fa-brands fa-x-twitter"></i>
                            </a>
                            <a
                                href="https://wa.me/?text={{ $shareText }}%20{{ $shareUrl }}"
                                target="_blank"
                                rel="noopener"
                                class="social-icon"
                                style="background:var(--light);color:#25d366"
                                aria-label="Partager sur WhatsApp"
                            >
                                <i class="fa-brands fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ===== CORPS DE L'ARTICLE ===== --}}
                <div class="col-lg-8 order-lg-1">

                    @if ($article->cover_image)
                        <img
                            src="{{ asset('assets/covers/' . $article->cover_image) }}"
                            alt="{{ $article->title }}"
                            class="img-fluid rounded mb-4"
                            style="width:100%; max-height:400px; object-fit:cover;"
                        />
                    @endif

                    <article class="article-body">
                        {!! $article->body_html ?: nl2br(e($article->body)) !!}
                    </article>

                    {{-- Biographie de l'auteur --}}
                    <div class="author-bio mt-5">
                        @if ($article->author->avatar)
                            <img src="{{ $article->author->avatar }}" class="avatar avatar-lg" alt="{{ $article->author->name }}" />
                        @else
                            <span class="avatar avatar-lg av-1">{{ strtoupper(substr($article->author->name, 0, 2)) }}</span>
                        @endif
                        <div>
                            <h3 style="font-size:1.1rem;margin-bottom:.3rem" class="text-navy">{{ $article->author->name }}</h3>
                            @if ($article->author->profile?->bio ?? false)
                                <p class="mb-2" style="font-size:.92rem;color:var(--muted)">{{ $article->author->profile->bio }}</p>
                            @endif
                            <a href="{{ route('members.show', $article->author->github_username ?? $article->author->id) }}" class="btn btn-ghost btn-sm">
                                <i class="fa-brands fa-github"></i> Voir le profil
                            </a>
                        </div>
                    </div>

                    {{-- ===== COMMENTAIRES ===== --}}
                    <div class="mt-5">
                        <h2 class="section-heading mb-4" style="font-size:var(--fs-h3)">
                            Commentaires ({{ $article->comments_count }})
                        </h2>

                        @auth
                            <div class="card-soft mb-4" style="padding:1.3rem">
                                <p class="text-muted-2 mb-0">Les commentaires seront disponibles prochainement.</p>
                            </div>
                        @else
                            <div class="card-soft p-4 text-center mb-4">
                                <p class="mb-3">Connectez-vous pour laisser un commentaire.</p>
                                <a href="{{ route('login') }}" class="btn btn-brand">
                                    <i class="fa-brands fa-github"></i> Se connecter
                                </a>
                            </div>
                        @endauth

                        @forelse ($comments as $comment)
                            <div class="comment">
                                @if ($comment->user->avatar)
                                    <img src="{{ $comment->user->avatar }}" class="avatar avatar-sm" alt="{{ $comment->user->name }}" />
                                @else
                                    <span class="avatar av-3">{{ strtoupper(substr($comment->user->name, 0, 2)) }}</span>
                                @endif
                                <div class="c-body">
                                    <div class="c-head">
                                        <span class="name" style="font-weight:600">{{ $comment->user->name }}</span>
                                        <span class="sub" style="font-size:.8rem;color:var(--muted)">
                                            {{ $comment->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <p class="mb-1">{{ $comment->body }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted-2">Aucun commentaire pour le moment. Soyez le premier à réagir !</p>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ===== ARTICLES SIMILAIRES ===== --}}
            @if ($similarArticles->isNotEmpty())
                <div class="mt-5 pt-4">
                    <h2 class="section-heading mb-4">Articles similaires</h2>
                    <div class="row g-4">
                        @foreach ($similarArticles as $related)
                            <div class="col-md-4 reveal">
                                <article class="card-soft article-card">
                                    <div class="level-banner lv-{{ $related->level }}"></div>
                                    <div class="card-pad">
                                        <span class="badge-pill badge-{{ $related->level === 'beginner' ? 'green' : ($related->level === 'intermediate' ? 'orange' : '') }}">
                                            <span class="lv-dot" style="background:var(--level-{{ $related->level }})"></span>
                                            {{ ['beginner' => 'Débutant', 'intermediate' => 'Intermédiaire', 'advanced' => 'Avancé'][$related->level] ?? $related->level }}
                                        </span>
                                        <h3 class="art-title">
                                            <a href="{{ route('blog.show', $related->slug) }}">{{ $related->title }}</a>
                                        </h3>
                                        <div class="art-foot">
                                            <div class="author-row">
                                                <span class="avatar avatar-sm av-1">
                                                    {{ strtoupper(substr($related->author->name, 0, 2)) }}
                                                </span>
                                                <div class="meta">
                                                    <div class="name">{{ $related->author->name }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
