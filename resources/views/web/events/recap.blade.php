@extends('layouts.web')

@section('title', 'Récapitulatif — ' . $event->title . ' — Laravel CI')

@push('styles')
<style>
/* ── Hero ────────────────────────────────────────────────────────────── */
.recap-hero {
    background: var(--navy, #0f172a);
    padding: 3.5rem 0 2.5rem;
    position: relative;
    overflow: hidden;
}
.recap-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at 70% 50%, rgba(231,34,44,.12) 0%, transparent 60%);
    pointer-events: none;
}
.recap-hero .breadcrumb-bar { color: rgba(255,255,255,.55); margin-bottom: 1rem; }
.recap-hero .breadcrumb-bar a { color: rgba(255,255,255,.55); text-decoration: none; }
.recap-hero .breadcrumb-bar a:hover { color: #fff; }
.recap-hero h1 { color: #fff; font-size: clamp(1.75rem, 4vw, 2.75rem); font-weight: 800; margin-bottom: 1rem; }
.recap-meta { display: flex; flex-wrap: wrap; gap: .75rem 1.5rem; color: rgba(255,255,255,.7); font-size: .9rem; margin-top: .75rem; }
.recap-meta i { color: #e7222c; }

/* ── Corps ───────────────────────────────────────────────────────────── */
.recap-body { max-width: 820px; margin: 0 auto; padding: 3rem 0; }

.recap-lead {
    font-size: 1.2rem;
    line-height: 1.75;
    color: var(--text, #1e293b);
    font-weight: 400;
    border-left: 4px solid #e7222c;
    padding-left: 1.25rem;
    margin-bottom: 2.5rem;
}

/* ── Carrousel ────────────────────────────────────────────────────────── */
.recap-carousel-wrap {
    margin: 2.5rem 0 1rem;
    border-radius: .875rem;
    overflow: hidden;
    box-shadow: 0 4px 24px rgba(0,0,0,.12);
    background: #0f172a;
}
#recapCarousel .carousel-inner { background: #0f172a; }
#recapCarousel .carousel-item img {
    max-height: 540px;
    width: 100%;
    object-fit: contain;
    background: #0f172a;
}
#recapCarousel .carousel-caption {
    background: rgba(0,0,0,.5);
    border-radius: .5rem;
    padding: .4rem .75rem;
    bottom: 1rem;
    font-size: .85rem;
}
#recapCarousel .carousel-control-prev,
#recapCarousel .carousel-control-next {
    width: 3rem;
    opacity: .8;
}

/* ── Miniatures ──────────────────────────────────────────────────────── */
.recap-thumbs {
    display: flex;
    gap: .4rem;
    flex-wrap: wrap;
    margin-bottom: 2.5rem;
    margin-top: .6rem;
}
.recap-thumb-btn {
    padding: 0;
    border: 2px solid transparent;
    border-radius: .375rem;
    overflow: hidden;
    cursor: pointer;
    background: none;
    transition: border-color .15s;
}
.recap-thumb-btn:hover,
.recap-thumb-btn.active-thumb { border-color: #e7222c; }
.recap-thumb-btn img {
    width: 68px;
    height: 51px;
    object-fit: cover;
    display: block;
}

/* ── Contenu riche ───────────────────────────────────────────────────── */
.recap-rich-content { margin-bottom: 2.5rem; }
.recap-rich-content h2 {
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--navy, #0f172a);
    margin-top: 2rem;
    margin-bottom: .75rem;
}
.recap-rich-content h3 {
    font-size: 1.1rem;
    font-weight: 600;
    margin-top: 1.5rem;
    margin-bottom: .5rem;
}
.recap-rich-content ul, .recap-rich-content ol {
    padding-left: 1.5rem;
    margin-bottom: 1rem;
}
.recap-rich-content li { margin-bottom: .35rem; line-height: 1.65; }
.recap-rich-content p { line-height: 1.75; margin-bottom: 1rem; }
.recap-rich-content blockquote {
    border-left: 4px solid #e7222c;
    padding: .75rem 1.25rem;
    background: rgba(231,34,44,.05);
    border-radius: 0 .5rem .5rem 0;
    margin: 1.5rem 0;
    font-style: italic;
    color: var(--muted, #64748b);
}

/* ── Section téléchargement ──────────────────────────────────────────── */
.recap-section-title {
    font-size: 1rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #e7222c;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: .5rem;
}
.recap-actions { display: flex; flex-wrap: wrap; gap: .75rem; margin-bottom: 2.5rem; }
.recap-btn-dl {
    display: inline-flex;
    align-items: center;
    gap: .5rem;
    padding: .6rem 1.25rem;
    border-radius: .5rem;
    font-weight: 600;
    font-size: .9rem;
    text-decoration: none;
    transition: opacity .15s;
}
.recap-btn-dl:hover { opacity: .85; }
.recap-btn-dl--primary { background: #e7222c; color: #fff; }
.recap-btn-dl--secondary { background: #1e293b; color: #fff; }
.recap-btn-dl--outline { border: 2px solid #1e293b; color: #1e293b; background: transparent; }

/* ── Vidéos ──────────────────────────────────────────────────────────── */
.recap-videos { margin-bottom: 2.5rem; }

/* ── Dark mode ───────────────────────────────────────────────────────── */
@media (prefers-color-scheme: dark) {
    :root:not([data-theme="light"]) .recap-rich-content h2 { color: #f1f5f9; }
    :root:not([data-theme="light"]) .recap-lead { color: #cbd5e1; }
    :root:not([data-theme="light"]) .recap-btn-dl--outline { border-color: #94a3b8; color: #e2e8f0; }
}
:root[data-theme="dark"] .recap-rich-content h2 { color: #f1f5f9; }
:root[data-theme="dark"] .recap-lead { color: #cbd5e1; }
</style>
@endpush

@section('content')
<div>
    {{-- ===== HERO ===== --}}
    <section class="recap-hero">
        <div class="container">
            <div class="breadcrumb-bar">
                <a href="{{ route('home') }}">Accueil</a>
                <i class="fa-solid fa-chevron-right" style="font-size:.65rem;margin:0 .4rem"></i>
                <a href="{{ route('events.index') }}">Événements</a>
                <i class="fa-solid fa-chevron-right" style="font-size:.65rem;margin:0 .4rem"></i>
                <a href="{{ route('events.show', $event->slug) }}">{{ $event->title }}</a>
                <i class="fa-solid fa-chevron-right" style="font-size:.65rem;margin:0 .4rem"></i>
                <span style="color:rgba(255,255,255,.85)">Récapitulatif</span>
            </div>

            <span class="badge-pill mb-3"
                  style="background:rgba(231,34,44,.2);color:#fca5a5;border:1px solid rgba(231,34,44,.35);">
                <i class="fa-solid fa-flag-checkered"></i> Après l'événement
            </span>

            <h1>{{ $event->title }}</h1>

            <div class="recap-meta">
                <span>
                    <i class="fa-regular fa-calendar me-1"></i>
                    {{ $event->starts_at->translatedFormat('d M Y') }}
                </span>
                <span>
                    <i class="fa-solid fa-location-dot me-1"></i>
                    {{ $event->location }}
                </span>
                <span>
                    <i class="fa-solid fa-images me-1"></i>
                    {{ $photos->count() }} photo(s)
                </span>
            </div>
        </div>
    </section>

    {{-- ===== CONTENU ===== --}}
    <section class="section">
        <div class="container">
            <div class="recap-body">

                {{-- ── Résumé ── --}}
                @if ($event->recap_summary)
                    <p class="recap-lead">{{ $event->recap_summary }}</p>
                @endif

                {{-- ── Carrousel photos ── --}}
                @if ($photos->isNotEmpty())
                    <div class="recap-section-title">
                        <i class="fa-solid fa-images"></i> Photos de l'événement
                    </div>

                    <div class="recap-carousel-wrap">
                        <div id="recapCarousel" class="carousel slide" data-bs-ride="false">

                            {{-- Indicateurs --}}
                            <div class="carousel-indicators">
                                @foreach ($photos as $i => $photo)
                                    <button type="button"
                                            data-bs-target="#recapCarousel"
                                            data-bs-slide-to="{{ $i }}"
                                            @if ($i === 0) class="active" aria-current="true" @endif
                                            aria-label="Photo {{ $i + 1 }}"></button>
                                @endforeach
                            </div>

                            {{-- Slides --}}
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

                    {{-- Miniatures --}}
                    <div class="recap-thumbs">
                        @foreach ($photos as $i => $photo)
                            <button type="button"
                                    class="recap-thumb-btn {{ $i === 0 ? 'active-thumb' : '' }}"
                                    data-bs-target="#recapCarousel"
                                    data-bs-slide-to="{{ $i }}">
                                <img src="{{ $photo->thumbnailUrl() }}"
                                     alt="Miniature {{ $i + 1 }}"
                                     loading="lazy" />
                            </button>
                        @endforeach
                    </div>
                @endif

                {{-- ── Contenu riche ── --}}
                @if ($event->recap_content)
                    <div class="recap-rich-content">
                        {!! clean($event->recap_content) !!}
                    </div>
                @endif

                {{-- ── Vidéos YouTube/Vimeo ── --}}
                @if (! empty($event->recapVideoUrls()))
                    <div class="recap-videos">
                        <div class="recap-section-title">
                            <i class="fa-solid fa-video"></i> Vidéos
                        </div>
                        <div class="row g-3">
                            @foreach ($event->recapVideoUrls() as $videoUrl)
                                <div class="col-md-6">
                                    <div class="ratio ratio-16x9" style="border-radius:.75rem;overflow:hidden;">
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
                    <div class="recap-section-title mt-4">
                        <i class="fa-solid fa-download"></i> Téléchargements
                    </div>
                    <div class="recap-actions">
                        @if ($photos->isNotEmpty() && Route::has('events.media.zip.all'))
                            <a href="{{ route('events.media.zip.all', $event) }}"
                               class="recap-btn-dl recap-btn-dl--primary">
                                <i class="fa-solid fa-file-zipper"></i>
                                Toutes les photos (ZIP)
                            </a>
                        @endif

                        @if ($event->recapDocumentUrl())
                            <a href="{{ $event->recapDocumentUrl() }}" target="_blank" rel="noopener"
                               class="recap-btn-dl recap-btn-dl--secondary">
                                <i class="fa-solid fa-file-arrow-down"></i>
                                {{ $event->recap_document_name ?? 'Document' }}
                            </a>
                        @endif
                    </div>
                @endif

                {{-- ── Retour ── --}}
                <div class="mt-4 pt-4" style="border-top: 1px solid var(--border, #e2e8f0);">
                    <a href="{{ route('events.show', $event->slug) }}"
                       class="d-inline-flex align-items-center gap-2"
                       style="color:var(--muted);text-decoration:none;font-size:.9rem;">
                        <i class="fa-solid fa-arrow-left"></i>
                        Retour à la page de l'événement
                    </a>
                </div>

            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
// Synchroniser la miniature active avec le slide courant
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
