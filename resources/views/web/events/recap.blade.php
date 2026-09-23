@extends('layouts.web')

@section('title', 'Récapitulatif — ' . $event->title . ' — Laravel CI')

@php
    use Illuminate\Support\Str;

    $heroPhoto = $photos->first();
    $heroBg    = $heroPhoto ? $heroPhoto->url() : null;

    // Extraire les titres h2 pour le TOC
    $rawContent = $event->recap_content ?? '';
    preg_match_all('/<h2[^>]*>(.*?)<\/h2>/s', $rawContent, $h2Matches);
    $tocItems = collect($h2Matches[1] ?? [])->map(fn ($t) => [
        'id'    => Str::slug(strip_tags($t)),
        'title' => strip_tags($t),
    ]);

    // Photos de la galerie à insérer entre les sections (hors la première utilisée en hero)
    $galleryPhotos = $photos->skip(1)->values();
@endphp

@push('styles')
<style>
/* ── Tokens ─────────────────────────────────────────────────────────── */
:root {
    --rc-orange:   #FF6600;
    --rc-orange-d: #e65c00;
    --rc-navy:     #1C1C2E;
    --rc-shadow:   4px 4px 0 #1C1C2E;
    --rc-shadow-lg:7px 7px 0 #1C1C2E;
    --rc-text:     #1e293b;
    --rc-muted:    #64748b;
    --rc-surface:  #ffffff;
    --rc-border:   #e2e8f0;
}
@media (prefers-color-scheme: dark) {
    :root:not([data-theme="light"]) {
        --rc-text:    #e2e8f0;
        --rc-muted:   #94a3b8;
        --rc-surface: #0f172a;
        --rc-border:  rgba(255,255,255,.08);
    }
}
:root[data-theme="dark"] {
    --rc-text:    #e2e8f0;
    --rc-muted:   #94a3b8;
    --rc-surface: #0f172a;
    --rc-border:  rgba(255,255,255,.08);
}

/* ── Hero ────────────────────────────────────────────────────────────── */
.recap-hero {
    position: relative;
    height: 480px;
    display: flex;
    align-items: flex-end;
    overflow: hidden;
    background-color: var(--rc-navy);
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
}
.recap-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to top,
        rgba(28,28,46,.97) 0%,
        rgba(28,28,46,.60) 45%,
        rgba(28,28,46,.15) 100%
    );
    pointer-events: none;
    z-index: 0;
}
.recap-hero .hero-inner {
    position: relative;
    z-index: 1;
    padding-bottom: 2.75rem;
    padding-top: 2rem;
    width: 100%;
}
.recap-hero .breadcrumb-bar {
    color: rgba(255,255,255,.5);
    font-size: .82rem;
    margin-bottom: .9rem;
}
.recap-hero .breadcrumb-bar a { color: rgba(255,255,255,.5); text-decoration: none; }
.recap-hero .breadcrumb-bar a:hover { color: var(--rc-orange); }
.recap-hero .breadcrumb-bar i { font-size: .58rem; margin: 0 .35rem; }

.recap-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    background: rgba(255,102,0,.18);
    color: #ff9f5e;
    border: 1px solid rgba(255,102,0,.38);
    border-radius: 2rem;
    padding: .25rem .9rem;
    font-size: .77rem;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    margin-bottom: .85rem;
}
.recap-hero h1 {
    color: #fff;
    font-size: clamp(1.75rem, 3.5vw, 2.6rem);
    font-weight: 800;
    letter-spacing: -.03em;
    margin-bottom: .7rem;
    text-wrap: balance;
}
.recap-meta {
    display: flex;
    flex-wrap: wrap;
    gap: .5rem 1.25rem;
    color: rgba(255,255,255,.68);
    font-size: .875rem;
}
.recap-meta i { color: var(--rc-orange); margin-right: .25rem; }

/* ── Layout ──────────────────────────────────────────────────────────── */
.recap-page-section {
    background: var(--rc-surface);
    padding: 3rem 0 4rem;
}

/* ── Sidebar ─────────────────────────────────────────────────────────── */
.recap-sidebar {
    position: sticky;
    top: calc(72px + 1.25rem);
}
.sidebar-widget {
    background: var(--rc-surface);
    border: 1px solid var(--rc-border);
    border-radius: .625rem;
    padding: 1.35rem 1.25rem;
    margin-bottom: 1.25rem;
}
.sidebar-widget-title {
    font-size: .78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--rc-orange);
    margin-bottom: 1rem;
    padding-bottom: .5rem;
    border-bottom: 2px solid var(--rc-border);
    display: flex;
    align-items: center;
    gap: .45rem;
}

/* Search widget */
.sidebar-search-wrap { position: relative; }
.sidebar-search-input {
    width: 100%;
    padding: .5rem .75rem;
    padding-right: 2.5rem;
    border: 1.5px solid var(--rc-border);
    border-radius: .375rem;
    font-size: .875rem;
    background: var(--rc-surface);
    color: var(--rc-text);
    outline: none;
    transition: border-color .15s;
}
.sidebar-search-input:focus { border-color: var(--rc-orange); }
.sidebar-search-btn {
    position: absolute;
    right: .6rem;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--rc-muted);
    font-size: .85rem;
    padding: 0;
    cursor: pointer;
    transition: color .15s;
}
.sidebar-search-btn:hover { color: var(--rc-orange); }

/* TOC */
.toc-nav { display: flex; flex-direction: column; gap: 0; }
.toc-link {
    display: flex;
    align-items: flex-start;
    gap: .55rem;
    padding: .45rem .5rem;
    color: var(--rc-muted);
    font-size: .82rem;
    line-height: 1.4;
    text-decoration: none;
    border-left: 2px solid transparent;
    border-radius: 0 .25rem .25rem 0;
    transition: color .15s, border-color .15s, background .15s;
}
.toc-link:hover {
    color: var(--rc-orange);
    border-left-color: var(--rc-orange);
    background: rgba(255,102,0,.04);
    text-decoration: none;
}
.toc-link.toc-active {
    color: var(--rc-orange);
    border-left-color: var(--rc-orange);
    font-weight: 600;
    background: rgba(255,102,0,.06);
}
.toc-link .toc-bullet {
    color: var(--rc-orange);
    font-weight: 700;
    flex-shrink: 0;
    margin-top: .05rem;
}
.toc-link.toc-active .toc-bullet { opacity: 1; }

/* Sidebar info row */
.sinfo-row {
    display: flex;
    align-items: flex-start;
    gap: .6rem;
    padding: .45rem 0;
    font-size: .85rem;
    color: var(--rc-text);
}
.sinfo-row + .sinfo-row { border-top: 1px solid var(--rc-border); }
.sinfo-row i { color: var(--rc-orange); width: 1rem; flex-shrink: 0; margin-top: .1rem; }

/* ── Lead ────────────────────────────────────────────────────────────── */
.recap-lead {
    font-size: 1.12rem;
    line-height: 1.85;
    color: var(--rc-text);
    border-left: 4px solid var(--rc-orange);
    padding: .4rem 0 .4rem 1.15rem;
    margin-bottom: 2.25rem;
}

/* ── Section label ───────────────────────────────────────────────────── */
.recap-section-label {
    font-size: .75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .09em;
    color: var(--rc-orange);
    display: flex;
    align-items: center;
    gap: .4rem;
    margin-bottom: .85rem;
}

/* ── Carrousel ────────────────────────────────────────────────────────── */
.recap-carousel-wrap {
    border: 2px solid var(--rc-navy);
    border-radius: .625rem;
    overflow: hidden;
    box-shadow: var(--rc-shadow-lg);
    background: var(--rc-navy);
    margin-bottom: .5rem;
}
#recapCarousel .carousel-inner { background: var(--rc-navy); }
#recapCarousel .carousel-item img {
    max-height: 460px;
    width: 100%;
    object-fit: contain;
    background: var(--rc-navy);
}
#recapCarousel .carousel-caption {
    background: rgba(0,0,0,.55);
    border-radius: .375rem;
    padding: .3rem .65rem;
    bottom: .75rem;
    font-size: .8rem;
}

/* ── Miniatures ──────────────────────────────────────────────────────── */
.recap-thumbs {
    display: flex;
    gap: .35rem;
    flex-wrap: wrap;
    margin-bottom: 2.25rem;
}
.recap-thumb-btn {
    padding: 0;
    border: 2px solid transparent;
    border-radius: .3rem;
    overflow: hidden;
    cursor: pointer;
    background: none;
    transition: border-color .12s, box-shadow .12s;
}
.recap-thumb-btn:hover { border-color: var(--rc-orange); }
.recap-thumb-btn.active-thumb {
    border-color: var(--rc-orange);
    box-shadow: 2px 2px 0 var(--rc-navy);
}
.recap-thumb-btn img { width: 62px; height: 46px; object-fit: cover; display: block; }

/* ── Article ─────────────────────────────────────────────────────────── */
.recap-article-body { color: var(--rc-text); }

.recap-article-body h2 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--rc-navy);
    margin-top: 2rem;
    margin-bottom: .65rem;
    padding-left: .9rem;
    border-left: 3px solid var(--rc-orange);
    line-height: 1.35;
    scroll-margin-top: 90px;
}
@media (prefers-color-scheme: dark) {
    :root:not([data-theme="light"]) .recap-article-body h2 { color: #e2e8f0; }
}
:root[data-theme="dark"] .recap-article-body h2 { color: #e2e8f0; }

.recap-article-body h3 {
    font-size: 1.05rem;
    font-weight: 600;
    color: var(--rc-navy);
    margin-top: 1.5rem;
    margin-bottom: .4rem;
}

.recap-article-body p {
    margin-bottom: .95rem;
    line-height: 1.8;
    color: var(--rc-text);
}
.recap-article-body ul,
.recap-article-body ol {
    padding-left: 1.5rem;
    margin-bottom: 1rem;
    color: var(--rc-text);
}
.recap-article-body li { margin-bottom: .4rem; line-height: 1.7; }
.recap-article-body strong { color: var(--rc-navy); font-weight: 700; }
@media (prefers-color-scheme: dark) {
    :root:not([data-theme="light"]) .recap-article-body strong { color: #f1f5f9; }
}
:root[data-theme="dark"] .recap-article-body strong { color: #f1f5f9; }

.recap-article-body a { color: var(--rc-orange); text-decoration: underline; }

.recap-article-body blockquote {
    border-left: 4px solid var(--rc-orange);
    background: rgba(255,102,0,.05);
    padding: .9rem 1.2rem;
    border-radius: 0 .5rem .5rem 0;
    margin: 1.5rem 0;
    font-style: italic;
    color: var(--rc-muted);
    font-size: 1.05rem;
    line-height: 1.7;
}
.recap-article-body blockquote cite {
    display: block;
    font-size: .82rem;
    font-style: normal;
    font-weight: 700;
    color: var(--rc-orange);
    margin-top: .45rem;
}

/* ── Photos intégrées ────────────────────────────────────────────────── */
.article-photo-break {
    margin: 2rem 0;
    position: relative;
    display: block;
}
.article-photo-break img {
    width: 100%;
    height: 280px;
    object-fit: cover;
    border-radius: .5rem;
    border: 2px solid var(--rc-navy);
    box-shadow: 8px 8px 0 var(--rc-orange);
    display: block;
}
.article-photo-break--tilt img {
    transform: rotate(-0.6deg);
    box-shadow: -7px 7px 0 var(--rc-orange);
}
.article-photo-break--tilt2 img {
    transform: rotate(0.5deg);
    box-shadow: 7px 7px 0 var(--rc-navy);
    border-color: var(--rc-orange);
}
.article-photo-break figcaption {
    margin-top: .55rem;
    font-size: .8rem;
    color: var(--rc-muted);
    font-style: italic;
    padding-left: .35rem;
}
.photo-duo {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .75rem;
    margin: 2rem 0;
}
.photo-duo img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    border-radius: .4rem;
    border: 2px solid var(--rc-navy);
    display: block;
}
.photo-duo .photo-duo--left { box-shadow: -6px 6px 0 var(--rc-orange); transform: rotate(-0.5deg); }
.photo-duo .photo-duo--right { box-shadow: 6px 6px 0 var(--rc-navy); border-color: var(--rc-orange); transform: rotate(0.4deg); }

/* ── Boutons d'action ────────────────────────────────────────────────── */
.recap-actions { display: flex; flex-wrap: wrap; gap: .65rem; margin-bottom: 2rem; }
.rc-btn {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .6rem 1.2rem;
    font-weight: 700;
    font-size: .875rem;
    text-decoration: none;
    border-radius: .375rem;
    transition: transform .1s, box-shadow .1s;
    cursor: pointer;
}
.rc-btn:hover { transform: translate(-2px, -2px); text-decoration: none; }
.rc-btn--orange {
    background: var(--rc-orange);
    color: #fff;
    border: 2px solid var(--rc-navy);
    box-shadow: var(--rc-shadow);
}
.rc-btn--orange:hover { color: #fff; box-shadow: 6px 6px 0 var(--rc-navy); }
.rc-btn--dark {
    background: var(--rc-navy);
    color: #fff;
    border: 2px solid var(--rc-navy);
    box-shadow: 3px 3px 0 rgba(0,0,0,.3);
}
.rc-btn--dark:hover { color: #fff; box-shadow: 5px 5px 0 rgba(0,0,0,.3); }

/* ── Divider ─────────────────────────────────────────────────────────── */
.recap-hr {
    border: none;
    height: 1px;
    background: var(--rc-border);
    margin: 2rem 0;
}

/* ── Responsive ──────────────────────────────────────────────────────── */
@media (max-width: 991.98px) {
    .recap-sidebar { position: static; }
    .recap-hero { height: 380px; }
    .photo-duo { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 575.98px) {
    .recap-hero { height: 320px; }
    .photo-duo { grid-template-columns: 1fr; }
    .article-photo-break img { height: 220px; }
}
</style>
@endpush

@section('content')
<div>

{{-- ===== HERO : 1ère photo en fond plein-écran ===== --}}
<section class="recap-hero"@if($heroBg) style="background-image:url('{{ $heroBg }}')"@endif>
    <div class="container hero-inner">

        <div class="breadcrumb-bar">
            <a href="{{ route('home') }}">Accueil</a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('events.index') }}">Événements</a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('events.show', $event->slug) }}">{{ $event->title }}</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span style="color:rgba(255,255,255,.8)">Récapitulatif</span>
        </div>

        <div class="recap-eyebrow">
            <i class="fa-solid fa-flag-checkered"></i>
            Compte rendu officiel
        </div>

        <h1>{{ $event->title }}</h1>

        <div class="recap-meta">
            <span><i class="fa-regular fa-calendar"></i>{{ $event->starts_at->translatedFormat('l d M Y') }}</span>
            @if ($event->location)
                <span><i class="fa-solid fa-location-dot"></i>{{ $event->location }}</span>
            @endif
            @if ($photos->isNotEmpty())
                <span><i class="fa-solid fa-images"></i>{{ $photos->count() }} photos</span>
            @endif
            @if ($event->recap_published_at)
                <span><i class="fa-regular fa-clock"></i>Publié le {{ $event->recap_published_at->translatedFormat('d M Y') }}</span>
            @endif
        </div>

    </div>
</section>

{{-- ===== CORPS : 2 colonnes ===== --}}
<div class="recap-page-section">
    <div class="container">
        <div class="row g-4 g-lg-5">

            {{-- ── CONTENU PRINCIPAL ── --}}
            <div class="col-lg-8">

                {{-- Lead --}}
                @if ($event->recap_summary)
                    <p class="recap-lead">{{ $event->recap_summary }}</p>
                @endif

                {{-- Galerie carousel --}}
                @if ($photos->isNotEmpty())
                    <div class="recap-section-label mb-2">
                        <i class="fa-solid fa-images"></i>
                        Photos de l'événement
                    </div>

                    <div class="recap-carousel-wrap">
                        <div id="recapCarousel" class="carousel slide" data-bs-ride="false">
                            <div class="carousel-indicators">
                                @foreach ($photos as $i => $photo)
                                    <button type="button"
                                            data-bs-target="#recapCarousel"
                                            data-bs-slide-to="{{ $i }}"
                                            @if($i===0) class="active" aria-current="true" @endif
                                            aria-label="Photo {{ $i+1 }}"></button>
                                @endforeach
                            </div>
                            <div class="carousel-inner">
                                @foreach ($photos as $i => $photo)
                                    <div class="carousel-item {{ $i===0 ? 'active' : '' }}">
                                        <img src="{{ $photo->url() }}"
                                             alt="{{ $photo->caption ?? ($event->title . ' — photo ' . ($i+1)) }}"
                                             loading="{{ $i===0 ? 'eager' : 'lazy' }}" />
                                        @if ($photo->caption)
                                            <div class="carousel-caption d-none d-md-block">
                                                <p class="mb-0">{{ $photo->caption }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#recapCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Précédent</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#recapCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Suivant</span>
                            </button>
                        </div>
                    </div>

                    <div class="recap-thumbs">
                        @foreach ($photos as $i => $photo)
                            <button type="button"
                                    class="recap-thumb-btn {{ $i===0 ? 'active-thumb' : '' }}"
                                    data-bs-target="#recapCarousel"
                                    data-bs-slide-to="{{ $i }}"
                                    title="Photo {{ $i+1 }}">
                                <img src="{{ $photo->thumbnailUrl() }}" alt="Miniature {{ $i+1 }}" loading="lazy" />
                            </button>
                        @endforeach
                    </div>
                @endif

                {{-- Article avec photos injectées --}}
                @if ($event->recap_content)
                    <div class="recap-section-label mb-3">
                        <i class="fa-solid fa-pen-nib"></i>
                        Le compte rendu complet
                    </div>

                    @php
                        // Split l'article sur les balises h2 pour injecter des photos entre les sections
                        $articleSections = collect(preg_split('/(?=<h2)/i', $rawContent, -1, PREG_SPLIT_NO_EMPTY))
                            ->filter(fn ($s) => trim($s) !== '')
                            ->values();

                        // Styles de photo en rotation
                        $photoStyles = ['', 'article-photo-break--tilt', 'article-photo-break--tilt2'];
                        $photoIdx = 0; // index dans $galleryPhotos
                    @endphp

                    <div class="recap-article-body">
                        @foreach ($articleSections as $si => $section)

                            {{-- Rendu de la section --}}
                            {!! clean($section) !!}

                            {{-- Injection d'une photo tous les 3 sections --}}
                            @if (($si + 1) % 3 === 0 && $galleryPhotos->has($photoIdx))
                                @php
                                    $pStyle = $photoStyles[$photoIdx % 3];

                                    // Duo de photos si on en a deux disponibles
                                    $hasDuo = $galleryPhotos->has($photoIdx + 1) && ($photoIdx % 5 === 2);
                                @endphp

                                @if ($hasDuo)
                                    <div class="photo-duo">
                                        <div>
                                            <img src="{{ $galleryPhotos->get($photoIdx)->url() }}"
                                                 alt="Photo {{ $photoIdx + 2 }}"
                                                 class="photo-duo--left"
                                                 loading="lazy" />
                                        </div>
                                        <div>
                                            <img src="{{ $galleryPhotos->get($photoIdx + 1)->url() }}"
                                                 alt="Photo {{ $photoIdx + 3 }}"
                                                 class="photo-duo--right"
                                                 loading="lazy" />
                                        </div>
                                    </div>
                                    @php $photoIdx += 2; @endphp
                                @else
                                    <figure class="article-photo-break {{ $pStyle }}">
                                        <img src="{{ $galleryPhotos->get($photoIdx)->url() }}"
                                             alt="{{ $galleryPhotos->get($photoIdx)->caption ?? 'Photo de l\'événement' }}"
                                             loading="lazy" />
                                        @if ($galleryPhotos->get($photoIdx)?->caption)
                                            <figcaption>{{ $galleryPhotos->get($photoIdx)->caption }}</figcaption>
                                        @endif
                                    </figure>
                                    @php $photoIdx++; @endphp
                                @endif
                            @endif

                        @endforeach
                    </div>
                @endif

                {{-- Vidéos --}}
                @if (! empty($event->recapVideoUrls()))
                    <hr class="recap-hr">
                    <div class="recap-section-label mb-3">
                        <i class="fa-solid fa-video"></i> Vidéos
                    </div>
                    <div class="row g-3 mb-4">
                        @foreach ($event->recapVideoUrls() as $videoUrl)
                            <div class="col-sm-6">
                                <div class="ratio ratio-16x9" style="border-radius:.5rem;overflow:hidden;border:2px solid var(--rc-navy);box-shadow:var(--rc-shadow)">
                                    <iframe src="{{ $event->toEmbedUrl($videoUrl) }}" title="Vidéo" allowfullscreen></iframe>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Téléchargements --}}
                @if ($photos->isNotEmpty() || $event->recapDocumentUrl())
                    <hr class="recap-hr">
                    <div class="recap-section-label mb-2">
                        <i class="fa-solid fa-download"></i> Téléchargements
                    </div>
                    <div class="recap-actions">
                        @if ($photos->isNotEmpty() && Route::has('events.media.zip.all'))
                            <a href="{{ route('events.media.zip.all', $event) }}" class="rc-btn rc-btn--orange">
                                <i class="fa-solid fa-file-zipper"></i>
                                Toutes les photos (ZIP)
                            </a>
                        @endif
                        @if ($event->recapDocumentUrl())
                            <a href="{{ $event->recapDocumentUrl() }}" target="_blank" rel="noopener" class="rc-btn rc-btn--dark">
                                <i class="fa-solid fa-file-arrow-down"></i>
                                {{ $event->recap_document_name ?? 'Document officiel' }}
                            </a>
                        @endif
                    </div>
                @endif

                {{-- Retour --}}
                <hr class="recap-hr">
                <a href="{{ route('events.show', $event->slug) }}"
                   class="d-inline-flex align-items-center gap-2"
                   style="color:var(--rc-muted);text-decoration:none;font-size:.875rem;">
                    <i class="fa-solid fa-arrow-left"></i>
                    Retour à la page de l'événement
                </a>

            </div>{{-- /col-lg-8 --}}

            {{-- ── SIDEBAR ── --}}
            <div class="col-lg-4 d-none d-lg-block">
                <div class="recap-sidebar">

                    {{-- Recherche --}}
                    <div class="sidebar-widget">
                        <div class="sidebar-widget-title">
                            <i class="fa-solid fa-magnifying-glass"></i> Rechercher
                        </div>
                        <form action="{{ route('search.index') }}" method="GET">
                            <div class="sidebar-search-wrap">
                                <input type="text" name="q" class="sidebar-search-input"
                                       placeholder="Articles, événements…" />
                                <button type="submit" class="sidebar-search-btn" aria-label="Rechercher">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Table des matières --}}
                    @if ($tocItems->isNotEmpty())
                        <div class="sidebar-widget">
                            <div class="sidebar-widget-title">
                                <i class="fa-solid fa-list-ul"></i> Dans cet article
                            </div>
                            <nav class="toc-nav" id="tocNav">
                                @foreach ($tocItems as $item)
                                    <a href="#{{ $item['id'] }}"
                                       class="toc-link"
                                       data-section="{{ $item['id'] }}">
                                        <span class="toc-bullet">—</span>
                                        <span>{{ Str::limit($item['title'], 52) }}</span>
                                    </a>
                                @endforeach
                            </nav>
                        </div>
                    @endif

                    {{-- Infos événement --}}
                    <div class="sidebar-widget">
                        <div class="sidebar-widget-title">
                            <i class="fa-solid fa-calendar-days"></i> L'événement
                        </div>

                        <div class="sinfo-row">
                            <i class="fa-regular fa-calendar"></i>
                            <div>
                                <div style="font-size:.78rem;color:var(--rc-muted);margin-bottom:.1rem">Date</div>
                                <div style="font-weight:600">{{ $event->starts_at->translatedFormat('d M Y') }}</div>
                            </div>
                        </div>
                        <div class="sinfo-row">
                            <i class="fa-regular fa-clock"></i>
                            <div>
                                <div style="font-size:.78rem;color:var(--rc-muted);margin-bottom:.1rem">Horaires</div>
                                <div style="font-weight:600">
                                    {{ $event->starts_at->format('H:i') }} – {{ $event->ends_at->format('H:i') }}
                                </div>
                            </div>
                        </div>
                        @if ($event->location)
                            <div class="sinfo-row">
                                <i class="fa-solid fa-location-dot"></i>
                                <div>
                                    <div style="font-size:.78rem;color:var(--rc-muted);margin-bottom:.1rem">Lieu</div>
                                    <div style="font-weight:600">{{ $event->location }}</div>
                                </div>
                            </div>
                        @endif
                        @if ($photos->isNotEmpty())
                            <div class="sinfo-row">
                                <i class="fa-solid fa-images"></i>
                                <div>
                                    <div style="font-size:.78rem;color:var(--rc-muted);margin-bottom:.1rem">Galerie</div>
                                    <div style="font-weight:600">{{ $photos->count() }} photos</div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Téléchargements rapides --}}
                    @if ($event->recapDocumentUrl() || ($photos->isNotEmpty() && Route::has('events.media.zip.all')))
                        <div class="sidebar-widget">
                            <div class="sidebar-widget-title">
                                <i class="fa-solid fa-download"></i> Ressources
                            </div>
                            <div class="d-flex flex-column gap-2">
                                @if ($event->recapDocumentUrl())
                                    <a href="{{ $event->recapDocumentUrl() }}" target="_blank" rel="noopener"
                                       class="rc-btn rc-btn--orange" style="justify-content:center">
                                        <i class="fa-solid fa-file-pdf"></i>
                                        Programme officiel
                                    </a>
                                @endif
                                @if ($photos->isNotEmpty() && Route::has('events.media.zip.all'))
                                    <a href="{{ route('events.media.zip.all', $event) }}"
                                       class="rc-btn rc-btn--dark" style="justify-content:center">
                                        <i class="fa-solid fa-file-zipper"></i>
                                        Photos (ZIP)
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                </div>{{-- /recap-sidebar --}}
            </div>{{-- /col-lg-4 --}}

        </div>{{-- /row --}}
    </div>{{-- /container --}}
</div>

</div>{{-- /root div --}}
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Carousel thumbnails sync ──────────────────────────────────────
    const carousel = document.getElementById('recapCarousel');
    if (carousel) {
        const thumbs = document.querySelectorAll('.recap-thumb-btn');
        carousel.addEventListener('slide.bs.carousel', function (e) {
            thumbs.forEach(b => b.classList.remove('active-thumb'));
            if (thumbs[e.to]) thumbs[e.to].classList.add('active-thumb');
        });
    }

    // ── Ajouter des IDs aux h2 de l'article pour le TOC ──────────────
    function slugify(text) {
        return text.normalize('NFD')
            .replace(/[̀-ͯ]/g, '')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-|-$/g, '');
    }
    document.querySelectorAll('.recap-article-body h2').forEach(function (h2) {
        if (!h2.id) {
            h2.id = slugify(h2.textContent.trim());
        }
    });

    // ── Smooth scroll TOC ─────────────────────────────────────────────
    document.querySelectorAll('.toc-link').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(link.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });

    // ── TOC active highlight via IntersectionObserver ────────────────
    const tocLinks = document.querySelectorAll('.toc-link');
    if (tocLinks.length && 'IntersectionObserver' in window) {
        const observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                const link = document.querySelector('.toc-link[href="#' + entry.target.id + '"]');
                if (link) link.classList.toggle('toc-active', entry.isIntersecting);
            });
        }, { rootMargin: '-15% 0px -75% 0px' });

        document.querySelectorAll('.recap-article-body h2[id]').forEach(function (h2) {
            observer.observe(h2);
        });
    }

});
</script>
@endpush
