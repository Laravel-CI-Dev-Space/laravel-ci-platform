@extends('layouts.web')

@section('title', 'Récapitulatif — ' . $event->title . ' — Laravel CI')

@php
    $heroPhoto = $photos->first();
    $heroBg    = $heroPhoto ? $heroPhoto->url() : null;
@endphp

@push('styles')
<style>
/* ── Variables ────────────────────────────────────────────────────────── */
:root {
    --rc-orange:  #FF6600;
    --rc-orange2: #e65c00;
    --rc-navy:    #1C1C2E;
    --rc-shadow:  4px 4px 0 var(--rc-navy);
    --rc-shadow-lg: 7px 7px 0 var(--rc-navy);
}

/* ── Hero ────────────────────────────────────────────────────────────── */
.recap-hero {
    position: relative;
    min-height: 520px;
    display: flex;
    align-items: flex-end;
    overflow: hidden;
    background-color: var(--rc-navy);
    background-size: cover;
    background-position: center top;
}
.recap-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to top,
        rgba(28,28,46,.96) 0%,
        rgba(28,28,46,.65) 45%,
        rgba(28,28,46,.2)  100%
    );
    pointer-events: none;
}
.recap-hero .container { position: relative; z-index: 1; padding-bottom: 2.75rem; padding-top: 2rem; }

.recap-hero .breadcrumb-bar {
    color: rgba(255,255,255,.55);
    margin-bottom: 1rem;
    font-size: .85rem;
}
.recap-hero .breadcrumb-bar a { color: rgba(255,255,255,.55); text-decoration: none; }
.recap-hero .breadcrumb-bar a:hover { color: var(--rc-orange); }
.recap-hero .breadcrumb-bar i { font-size: .6rem; margin: 0 .35rem; }

.recap-hero h1 {
    color: #fff;
    font-size: clamp(1.8rem, 4vw, 2.8rem);
    font-weight: 800;
    letter-spacing: -.03em;
    margin-bottom: .75rem;
    text-wrap: balance;
}
.recap-hero .recap-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    background: rgba(255,102,0,.18);
    color: #ff8533;
    border: 1px solid rgba(255,102,0,.4);
    border-radius: 2rem;
    padding: .25rem .85rem;
    font-size: .8rem;
    font-weight: 600;
    letter-spacing: .04em;
    text-transform: uppercase;
    margin-bottom: .9rem;
}
.recap-meta {
    display: flex;
    flex-wrap: wrap;
    gap: .6rem 1.4rem;
    color: rgba(255,255,255,.72);
    font-size: .9rem;
    margin-top: .75rem;
}
.recap-meta i { color: var(--rc-orange); margin-right: .3rem; }

/* ── Corps ───────────────────────────────────────────────────────────── */
.recap-body {
    max-width: 860px;
    margin: 0 auto;
    padding: 3rem 0 4rem;
}

/* ── Lead ────────────────────────────────────────────────────────────── */
.recap-lead {
    font-size: 1.15rem;
    line-height: 1.8;
    color: var(--text, #1e293b);
    border-left: 4px solid var(--rc-orange);
    padding: .5rem 0 .5rem 1.25rem;
    margin-bottom: 2.5rem;
}

/* ── Section title ───────────────────────────────────────────────────── */
.recap-section-title {
    font-size: .82rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: var(--rc-orange);
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-bottom: 1rem;
}

/* ── Carrousel ────────────────────────────────────────────────────────── */
.recap-carousel-wrap {
    margin: 0 0 1rem;
    border: 2px solid var(--rc-navy);
    border-radius: .75rem;
    overflow: hidden;
    box-shadow: var(--rc-shadow-lg);
    background: var(--rc-navy);
}
#recapCarousel .carousel-inner { background: var(--rc-navy); }
#recapCarousel .carousel-item img {
    max-height: 520px;
    width: 100%;
    object-fit: contain;
    background: var(--rc-navy);
}
#recapCarousel .carousel-caption {
    background: rgba(0,0,0,.55);
    border-radius: .4rem;
    padding: .35rem .7rem;
    bottom: .85rem;
    font-size: .82rem;
}
#recapCarousel .carousel-control-prev,
#recapCarousel .carousel-control-next { width: 3rem; opacity: .75; }

/* ── Miniatures ──────────────────────────────────────────────────────── */
.recap-thumbs {
    display: flex;
    gap: .375rem;
    flex-wrap: wrap;
    margin-bottom: 2.75rem;
    margin-top: .5rem;
}
.recap-thumb-btn {
    padding: 0;
    border: 2px solid transparent;
    border-radius: .35rem;
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
.recap-thumb-btn img {
    width: 70px;
    height: 52px;
    object-fit: cover;
    display: block;
}

/* ── Contenu riche ───────────────────────────────────────────────────── */
.recap-article { margin-bottom: 2.5rem; line-height: 1.8; }
.recap-article h2 {
    font-size: 1.3rem;
    font-weight: 700;
    color: var(--rc-navy);
    margin-top: 2.25rem;
    margin-bottom: .7rem;
    padding-left: .75rem;
    border-left: 3px solid var(--rc-orange);
}
.recap-article h3 {
    font-size: 1.05rem;
    font-weight: 600;
    color: var(--rc-navy);
    margin-top: 1.5rem;
    margin-bottom: .45rem;
}
.recap-article p { margin-bottom: 1rem; color: var(--text, #334155); }
.recap-article ul, .recap-article ol {
    padding-left: 1.5rem;
    margin-bottom: 1rem;
}
.recap-article li { margin-bottom: .4rem; }
.recap-article strong { color: var(--rc-navy); }
.recap-article a { color: var(--rc-orange); text-decoration: underline; }
.recap-article blockquote {
    border-left: 4px solid var(--rc-orange);
    background: rgba(255,102,0,.04);
    padding: .9rem 1.25rem;
    border-radius: 0 .5rem .5rem 0;
    margin: 1.5rem 0;
    font-style: italic;
    color: var(--muted, #64748b);
    font-size: 1.05rem;
}
.recap-article blockquote cite {
    display: block;
    font-size: .85rem;
    font-style: normal;
    font-weight: 600;
    color: var(--rc-orange);
    margin-top: .4rem;
}

/* ── Speaker card inline ──────────────────────────────────────────────── */
.talk-card {
    background: var(--surface-2, #f8fafc);
    border: 1px solid var(--border, #e2e8f0);
    border-left: 4px solid var(--rc-orange);
    border-radius: .5rem;
    padding: 1.1rem 1.25rem;
    margin: 1.25rem 0 1.5rem;
    box-shadow: 3px 3px 0 var(--rc-navy);
}
.talk-card .talk-title {
    font-weight: 700;
    font-size: 1.05rem;
    color: var(--rc-navy);
}
.talk-card .talk-speaker {
    color: var(--rc-orange);
    font-size: .87rem;
    font-weight: 600;
    margin-bottom: .5rem;
}

/* ── Boutons ──────────────────────────────────────────────────────────── */
.recap-actions { display: flex; flex-wrap: wrap; gap: .75rem; margin-bottom: 2.5rem; }

.recap-btn {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .65rem 1.35rem;
    font-weight: 700;
    font-size: .9rem;
    text-decoration: none;
    border-radius: .375rem;
    transition: transform .1s, box-shadow .1s;
    cursor: pointer;
}
.recap-btn:hover {
    transform: translate(-2px, -2px);
    text-decoration: none;
}
.recap-btn--orange {
    background: var(--rc-orange);
    color: #fff;
    border: 2px solid var(--rc-navy);
    box-shadow: var(--rc-shadow);
}
.recap-btn--orange:hover {
    background: var(--rc-orange2);
    color: #fff;
    box-shadow: 6px 6px 0 var(--rc-navy);
}
.recap-btn--dark {
    background: var(--rc-navy);
    color: #fff;
    border: 2px solid var(--rc-navy);
    box-shadow: 3px 3px 0 rgba(0,0,0,.35);
}
.recap-btn--dark:hover {
    color: #fff;
    box-shadow: 5px 5px 0 rgba(0,0,0,.35);
}
.recap-btn--outline {
    background: transparent;
    color: var(--rc-navy);
    border: 2px solid var(--rc-navy);
    box-shadow: 3px 3px 0 rgba(0,0,0,.12);
}
.recap-btn--outline:hover {
    color: var(--rc-navy);
    box-shadow: 5px 5px 0 rgba(0,0,0,.12);
}

/* ── Vidéos ──────────────────────────────────────────────────────────── */
.recap-videos { margin-bottom: 2.5rem; }

/* ── Ligne de séparation ─────────────────────────────────────────────── */
.recap-divider {
    height: 2px;
    background: linear-gradient(to right, var(--rc-orange), transparent);
    border: none;
    margin: 2.5rem 0;
    opacity: .35;
}

/* ── Dark mode ───────────────────────────────────────────────────────── */
@media (prefers-color-scheme: dark) {
    :root:not([data-theme="light"]) .recap-article h2 { color: #e2e8f0; }
    :root:not([data-theme="light"]) .recap-article h3 { color: #cbd5e1; }
    :root:not([data-theme="light"]) .recap-article p { color: #94a3b8; }
    :root:not([data-theme="light"]) .recap-article strong { color: #e2e8f0; }
    :root:not([data-theme="light"]) .recap-lead { color: #94a3b8; }
    :root:not([data-theme="light"]) .talk-card { background: rgba(255,255,255,.04); border-color: rgba(255,255,255,.08); }
    :root:not([data-theme="light"]) .talk-card .talk-title { color: #f1f5f9; }
    :root:not([data-theme="light"]) .recap-btn--outline { color: #e2e8f0; border-color: rgba(255,255,255,.3); }
    :root:not([data-theme="light"]) .recap-btn--outline:hover { color: #fff; }
}
:root[data-theme="dark"] .recap-article h2 { color: #e2e8f0; }
:root[data-theme="dark"] .recap-article h3 { color: #cbd5e1; }
:root[data-theme="dark"] .recap-article p { color: #94a3b8; }
:root[data-theme="dark"] .recap-article strong { color: #e2e8f0; }
:root[data-theme="dark"] .recap-lead { color: #94a3b8; }
:root[data-theme="dark"] .talk-card { background: rgba(255,255,255,.04); border-color: rgba(255,255,255,.08); }
:root[data-theme="dark"] .talk-card .talk-title { color: #f1f5f9; }
</style>
@endpush

@section('content')
<div>
    {{-- ===== HERO — première photo de galerie en fond ===== --}}
    <section class="recap-hero"@if($heroBg) style="background-image:url('{{ $heroBg }}')"@endif>
        <div class="container">

            {{-- Fil d'Ariane --}}
            <div class="breadcrumb-bar">
                <a href="{{ route('home') }}">Accueil</a>
                <i class="fa-solid fa-chevron-right"></i>
                <a href="{{ route('events.index') }}">Événements</a>
                <i class="fa-solid fa-chevron-right"></i>
                <a href="{{ route('events.show', $event->slug) }}">{{ $event->title }}</a>
                <i class="fa-solid fa-chevron-right"></i>
                <span style="color:rgba(255,255,255,.85)">Récapitulatif</span>
            </div>

            <span class="recap-eyebrow">
                <i class="fa-solid fa-flag-checkered"></i>
                Compte rendu officiel
            </span>

            <h1>{{ $event->title }}</h1>

            <div class="recap-meta">
                <span>
                    <i class="fa-regular fa-calendar"></i>
                    {{ $event->starts_at->translatedFormat('l d M Y') }}
                </span>
                @if ($event->location)
                    <span>
                        <i class="fa-solid fa-location-dot"></i>
                        {{ $event->location }}
                    </span>
                @endif
                @if ($photos->isNotEmpty())
                    <span>
                        <i class="fa-solid fa-images"></i>
                        {{ $photos->count() }} photo(s)
                    </span>
                @endif
                @if ($event->recap_published_at)
                    <span>
                        <i class="fa-regular fa-clock"></i>
                        Publié le {{ $event->recap_published_at->translatedFormat('d M Y') }}
                    </span>
                @endif
            </div>
        </div>
    </section>

    {{-- ===== CONTENU ===== --}}
    <section class="section">
        <div class="container">
            <div class="recap-body">

                {{-- ── Résumé en lead ── --}}
                @if ($event->recap_summary)
                    <p class="recap-lead">{{ $event->recap_summary }}</p>
                @endif

                {{-- ── Galerie photos ── --}}
                @if ($photos->isNotEmpty())
                    <div class="recap-section-title">
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
                                            @if($i === 0) class="active" aria-current="true" @endif
                                            aria-label="Photo {{ $i + 1 }}"></button>
                                @endforeach
                            </div>

                            <div class="carousel-inner">
                                @foreach ($photos as $i => $photo)
                                    <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                                        <img src="{{ $photo->url() }}"
                                             alt="{{ $photo->caption ?? ($event->title . ' — photo ' . ($i + 1)) }}"
                                             loading="{{ $i === 0 ? 'eager' : 'lazy' }}" />
                                        @if ($photo->caption)
                                            <div class="carousel-caption d-none d-md-block">
                                                <p class="mb-0">{{ $photo->caption }}</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            <button class="carousel-control-prev" type="button"
                                    data-bs-target="#recapCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Précédent</span>
                            </button>
                            <button class="carousel-control-next" type="button"
                                    data-bs-target="#recapCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Suivant</span>
                            </button>
                        </div>
                    </div>

                    <div class="recap-thumbs">
                        @foreach ($photos as $i => $photo)
                            <button type="button"
                                    class="recap-thumb-btn {{ $i === 0 ? 'active-thumb' : '' }}"
                                    data-bs-target="#recapCarousel"
                                    data-bs-slide-to="{{ $i }}"
                                    title="Photo {{ $i + 1 }}{{ $photo->caption ? ' — ' . $photo->caption : '' }}">
                                <img src="{{ $photo->thumbnailUrl() }}"
                                     alt="Miniature {{ $i + 1 }}"
                                     loading="lazy" />
                            </button>
                        @endforeach
                    </div>
                @endif

                {{-- ── Article détaillé ── --}}
                @if ($event->recap_content)
                    <hr class="recap-divider">
                    <div class="recap-section-title">
                        <i class="fa-solid fa-pen-nib"></i>
                        Le compte rendu complet
                    </div>
                    <div class="recap-article">
                        {!! clean($event->recap_content) !!}
                    </div>
                @endif

                {{-- ── Vidéos ── --}}
                @if (! empty($event->recapVideoUrls()))
                    <hr class="recap-divider">
                    <div class="recap-section-title">
                        <i class="fa-solid fa-video"></i>
                        Vidéos
                    </div>
                    <div class="recap-videos">
                        <div class="row g-3">
                            @foreach ($event->recapVideoUrls() as $videoUrl)
                                <div class="col-md-6">
                                    <div class="ratio ratio-16x9" style="border-radius:.625rem;overflow:hidden;border:2px solid var(--rc-navy);box-shadow:var(--rc-shadow)">
                                        <iframe src="{{ $event->toEmbedUrl($videoUrl) }}"
                                                title="Vidéo récapitulative"
                                                allowfullscreen></iframe>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- ── Téléchargements ── --}}
                @if ($photos->isNotEmpty() || $videos->isNotEmpty() || $event->recapDocumentUrl())
                    <hr class="recap-divider">
                    <div class="recap-section-title">
                        <i class="fa-solid fa-download"></i>
                        Téléchargements
                    </div>
                    <div class="recap-actions">
                        @if ($photos->isNotEmpty() && Route::has('events.media.zip.all'))
                            <a href="{{ route('events.media.zip.all', $event) }}"
                               class="recap-btn recap-btn--orange">
                                <i class="fa-solid fa-file-zipper"></i>
                                Toutes les photos (ZIP)
                            </a>
                        @endif

                        @if ($event->recapDocumentUrl())
                            <a href="{{ $event->recapDocumentUrl() }}"
                               target="_blank" rel="noopener"
                               class="recap-btn recap-btn--dark">
                                <i class="fa-solid fa-file-arrow-down"></i>
                                {{ $event->recap_document_name ?? 'Document officiel' }}
                            </a>
                        @endif
                    </div>
                @endif

                {{-- ── Navigation ── --}}
                <div style="padding-top:2rem;border-top:1px solid var(--border,#e2e8f0);">
                    <a href="{{ route('events.show', $event->slug) }}"
                       class="d-inline-flex align-items-center gap-2"
                       style="color:var(--muted,#64748b);text-decoration:none;font-size:.9rem;">
                        <i class="fa-solid fa-arrow-left"></i>
                        Retour à la page de l'événement
                    </a>
                </div>

            </div>{{-- recap-body --}}
        </div>{{-- container --}}
    </section>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const carousel = document.getElementById('recapCarousel');
    if (!carousel) return;
    const thumbs = document.querySelectorAll('.recap-thumb-btn');
    carousel.addEventListener('slide.bs.carousel', function (e) {
        thumbs.forEach(function (btn) { btn.classList.remove('active-thumb'); });
        if (thumbs[e.to]) thumbs[e.to].classList.add('active-thumb');
    });
});
</script>
@endpush
