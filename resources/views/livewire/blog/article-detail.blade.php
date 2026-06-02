@push('styles')
{{-- highlight.js – thème GitHub Dark pour les blocs de code --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/highlight.js@11.9.0/styles/github-dark.min.css" />
<style>
/* ══════════════════════════════════════════════════════════
   Style terminal pour les blocs de code dans les articles
   ══════════════════════════════════════════════════════════ */

/* Wrapper <pre> */
.article-body pre {
    position: relative;
    background: #0d1117;
    border: 1px solid #30363d;
    border-radius: 10px;
    margin: 1.75rem 0;
    padding: 0;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,.25);
}

/* Barre de titre type terminal */
.article-body pre::before {
    content: '';
    display: block;
    height: 2.4rem;
    background: #161b22;
    border-bottom: 1px solid #30363d;
}

/* Boutons macOS décoratifs */
.article-body pre::after {
    content: '⬤  ⬤  ⬤';
    position: absolute;
    top: .55rem;
    left: 1rem;
    font-size: .55rem;
    letter-spacing: .4rem;
    color: transparent;
    text-shadow: 0 0 0 #ff5f57, 1.15rem 0 0 #ffbd2e, 2.3rem 0 0 #28ca41;
    line-height: 1;
    pointer-events: none;
}

/* Le bloc de code lui-même */
.article-body pre code.hljs {
    display: block;
    padding: 1.25rem 1.4rem;
    overflow-x: auto;
    background: transparent !important;
    font-family: 'JetBrains Mono', 'Fira Code', 'Consolas', monospace;
    font-size: .875rem;
    line-height: 1.75;
    tab-size: 4;
}

/* Code inline (dans les paragraphes) */
.article-body :not(pre) > code {
    background: rgba(175,184,193,.15);
    padding: .18em .45em;
    border-radius: 5px;
    font-family: 'JetBrains Mono', 'Fira Code', monospace;
    font-size: .875em;
    color: #e06c75;
    white-space: nowrap;
    border: 1px solid rgba(175,184,193,.2);
}

/* Typographie générale du corps d'article */
.article-body h1,
.article-body h2,
.article-body h3,
.article-body h4 { margin-top: 2rem; margin-bottom: .75rem; font-weight: 700; color: var(--navy, #0f1b35); }
.article-body h2 { font-size: 1.45rem; padding-bottom: .4rem; border-bottom: 2px solid var(--border, #eef0f4); }
.article-body h3 { font-size: 1.2rem; }
.article-body p  { line-height: 1.85; margin-bottom: 1.1rem; }
.article-body ul,
.article-body ol { padding-left: 1.5rem; margin-bottom: 1.1rem; }
.article-body li { line-height: 1.8; margin-bottom: .3rem; }
.article-body blockquote {
    border-left: 4px solid var(--orange, #e8590c);
    background: rgba(232,89,12,.05);
    margin: 1.5rem 0;
    padding: .9rem 1.25rem;
    border-radius: 0 8px 8px 0;
    color: var(--muted, #6c757d);
    font-style: italic;
}
.article-body table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; }
.article-body th,
.article-body td { padding: .6rem .9rem; border: 1px solid var(--border, #eef0f4); }
.article-body th { background: var(--light, #f5f6fa); font-weight: 600; }
.article-body a { color: var(--orange, #e8590c); text-decoration: underline; }
.article-body img { max-width: 100%; border-radius: 8px; margin: 1rem 0; }
</style>
@endpush

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

                    @php
                        $levelLabels  = ['beginner' => 'Débutant', 'intermediate' => 'Intermédiaire', 'advanced' => 'Avancé'];
                        $levelBadge   = ['beginner' => 'badge-green', 'intermediate' => 'badge-orange', 'advanced' => ''];
                        $levelStyle   = $article->level === 'advanced' ? 'background:#fdeaec;color:var(--level-advanced)' : '';
                    @endphp
                    <span class="badge-pill {{ $levelBadge[$article->level] ?? '' }}" style="{{ $levelStyle }}">
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
                            {{ $article->published_at?->translatedFormat('d M Y') ?? $article->created_at->translatedFormat('d M Y') }}
                        </span>
                        <span class="text-muted-2">
                            <i class="fa-regular fa-eye me-1 text-orange"></i>
                            {{ number_format($article->views_count) }} vues
                        </span>
                        <span class="text-muted-2">
                            <i class="fa-regular fa-comment me-1 text-orange"></i>
                            {{ $article->comments_count }} commentaire{{ $article->comments_count !== 1 ? 's' : '' }}
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
                    <div class="toc-card" style="position:sticky; top:2rem;">

                        {{-- Tags --}}
                        <div class="sidebar-title">Tags</div>
                        <div class="q-tags mb-3">
                            @foreach ($article->tags as $tag)
                                <a href="{{ route('blog.index', ['tagId' => $tag->id]) }}" class="tag">
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        </div>

                        {{-- Partage --}}
                        <div class="sidebar-title mt-3">Partager cet article</div>
                        @php
                            $shareUrl  = urlencode(request()->url());
                            $shareText = urlencode($article->title . ' — Laravel CI');
                        @endphp
                        <div class="d-flex gap-2 mt-2 flex-wrap">
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $shareUrl }}"
                               target="_blank" rel="noopener"
                               class="btn btn-sm"
                               style="background:#0a66c2;color:#fff;border-radius:8px"
                               title="Partager sur LinkedIn">
                                <i class="fa-brands fa-linkedin-in me-1"></i>LinkedIn
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareText }}"
                               target="_blank" rel="noopener"
                               class="btn btn-sm"
                               style="background:#000;color:#fff;border-radius:8px"
                               title="Partager sur X">
                                <i class="fa-brands fa-x-twitter me-1"></i>Twitter
                            </a>
                            <a href="https://wa.me/?text={{ $shareText }}%20{{ $shareUrl }}"
                               target="_blank" rel="noopener"
                               class="btn btn-sm"
                               style="background:#25d366;color:#fff;border-radius:8px"
                               title="Partager sur WhatsApp">
                                <i class="fa-brands fa-whatsapp me-1"></i>WhatsApp
                            </a>
                        </div>

                        {{-- Lien retour --}}
                        <div class="mt-4 pt-3" style="border-top:1px solid var(--border)">
                            <a href="{{ route('blog.index') }}" class="text-muted-2" style="font-size:.85rem">
                                <i class="fa-solid fa-arrow-left me-1"></i> Tous les articles
                            </a>
                        </div>
                    </div>
                </div>

                {{-- ===== CORPS DE L'ARTICLE ===== --}}
                <div class="col-lg-8 order-lg-1">

                    @if ($article->cover_image)
                        <img src="{{ asset('assets/covers/' . $article->cover_image) }}"
                             alt="{{ $article->title }}"
                             class="img-fluid rounded mb-4"
                             style="width:100%; max-height:420px; object-fit:cover;" />
                    @endif

                    {{-- Corps rendu en HTML depuis CommonMark --}}
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
                            <h3 style="font-size:1.1rem;margin-bottom:.3rem" class="text-navy">
                                {{ $article->author->name }}
                            </h3>
                            @if ($article->author->profile?->bio ?? false)
                                <p class="mb-2" style="font-size:.92rem;color:var(--muted)">
                                    {{ $article->author->profile->bio }}
                                </p>
                            @endif
                            <a href="{{ route('members.show', $article->author->github_username ?? $article->author->id) }}"
                               class="btn btn-ghost btn-sm">
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
                                <p class="text-muted-2 mb-0">
                                    <i class="fa-solid fa-clock me-1"></i>
                                    Les commentaires interactifs arrivent prochainement.
                                </p>
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
                            <p class="text-muted-2 mt-3">Aucun commentaire pour le moment.</p>
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
                                        @php $rLabel = ['beginner'=>'Débutant','intermediate'=>'Intermédiaire','advanced'=>'Avancé']; @endphp
                                        <span class="badge-pill badge-{{ $related->level === 'beginner' ? 'green' : ($related->level === 'intermediate' ? 'orange' : '') }}">
                                            <span class="lv-dot" style="background:var(--level-{{ $related->level }})"></span>
                                            {{ $rLabel[$related->level] ?? $related->level }}
                                        </span>
                                        <h3 class="art-title">
                                            <a href="{{ route('blog.show', $related->slug) }}">{{ $related->title }}</a>
                                        </h3>
                                        <div class="art-foot">
                                            <div class="author-row">
                                                <span class="avatar avatar-sm av-1">
                                                    {{ strtoupper(substr($related->author->name, 0, 2)) }}
                                                </span>
                                                <div class="meta"><div class="name">{{ $related->author->name }}</div></div>
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/highlight.js@11.9.0/highlight.min.js"></script>
<script>
    // Coloration initiale
    document.querySelectorAll('.article-body pre code').forEach(el => hljs.highlightElement(el));
    // Re-coloration après navigation Livewire SPA
    document.addEventListener('livewire:navigated', () => {
        document.querySelectorAll('.article-body pre code').forEach(el => hljs.highlightElement(el));
    });
</script>
@endpush
