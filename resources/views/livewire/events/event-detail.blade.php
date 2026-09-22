<div>
    @php
        $spotsLeft  = $event->spotsLeft();
        $capacity   = $event->capacity;
        $pct        = ($capacity && $capacity > 0)
            ? min(100, round(($event->registrations_count / $capacity) * 100))
            : 0;
        $coverUrl   = $event->cover_image
            ? \Illuminate\Support\Facades\Storage::disk('assets')->url($event->cover_image)
            : null;
    @endphp

    {{-- ===== HERO ===== --}}
    @if ($coverUrl)
        {{-- Hero avec photo de couverture --}}
        <section class="event-cover-hero" style="
            position:relative;
            min-height:340px;
            display:flex;
            align-items:flex-end;
            overflow:hidden;
            background:#1e293b;
        ">
            <img src="{{ $coverUrl }}"
                 alt="{{ $event->title }}"
                 onerror="this.style.display='none'"
                 style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:.55;" />
            <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(15,23,42,.85) 30%,rgba(15,23,42,.25) 100%);"></div>
            <div class="container" style="position:relative;z-index:1;padding-bottom:2.5rem;padding-top:2rem">
                <div class="breadcrumb-bar" style="color:rgba(255,255,255,.65);margin-bottom:1rem">
                    <a href="{{ route('home') }}" style="color:rgba(255,255,255,.65)">Accueil</a>
                    <i class="fa-solid fa-chevron-right" style="font-size:.65rem"></i>
                    <a href="{{ route('events.index') }}" style="color:rgba(255,255,255,.65)">Événements</a>
                    <i class="fa-solid fa-chevron-right" style="font-size:.65rem"></i>
                    <span style="color:#fff">{{ $event->title }}</span>
                </div>
                <span class="badge-pill mb-2"
                      style="background:{{ $event->type->background() }};color:{{ $event->type->color() }}">
                    <i class="{{ $event->type->icon() }}"></i> {{ $event->type->label() }}
                </span>
                <h1 class="mt-2 mb-3" style="font-size:var(--fs-h1);color:#fff">{{ $event->title }}</h1>
                <div class="d-flex flex-wrap gap-3" style="color:rgba(255,255,255,.8);font-size:.95rem">
                    <span>
                        <i class="fa-regular fa-calendar me-1 text-orange"></i>
                        {{ $event->starts_at->translatedFormat('D d M Y') }}
                        @if (! $event->starts_at->isSameDay($event->ends_at))
                            – {{ $event->ends_at->translatedFormat('D d M Y') }}
                        @endif
                    </span>
                    <span>
                        <i class="fa-regular fa-clock me-1 text-orange"></i>
                        {{ $event->starts_at->format('H:i') }} – {{ $event->ends_at->format('H:i') }}
                    </span>
                    @if ($event->location)
                        <span>
                            <i class="fa-solid fa-location-dot me-1 text-orange"></i>
                            {{ $event->location }}
                        </span>
                    @elseif ($event->online_url)
                        <span>
                            <i class="fa-solid fa-globe me-1 text-orange"></i>
                            En ligne
                        </span>
                    @endif
                </div>
            </div>
        </section>
    @else
        {{-- Hero sans couverture (mascotte) --}}
        <section class="page-hero">
            <div class="container">
                <div class="row align-items-center g-4">
                    <div class="col-lg-9">
                        <div class="breadcrumb-bar">
                            <a href="{{ route('home') }}">Accueil</a>
                            <i class="fa-solid fa-chevron-right"></i>
                            <a href="{{ route('events.index') }}">Événements</a>
                            <i class="fa-solid fa-chevron-right"></i>
                            <span>{{ $event->title }}</span>
                        </div>
                        <span class="badge-pill mb-2"
                              style="background:{{ $event->type->background() }};color:{{ $event->type->color() }}">
                            <i class="{{ $event->type->icon() }}"></i> {{ $event->type->label() }}
                        </span>
                        <h1 class="my-3" style="font-size:var(--fs-h1)">{{ $event->title }}</h1>
                        <div class="d-flex flex-wrap gap-3" style="color:var(--muted);font-size:.95rem">
                            <span>
                                <i class="fa-regular fa-calendar me-1 text-orange"></i>
                                {{ $event->starts_at->translatedFormat('D d M Y') }}
                                @if (! $event->starts_at->isSameDay($event->ends_at))
                                    – {{ $event->ends_at->translatedFormat('D d M Y') }}
                                @endif
                            </span>
                            <span>
                                <i class="fa-regular fa-clock me-1 text-orange"></i>
                                {{ $event->starts_at->format('H:i') }} – {{ $event->ends_at->format('H:i') }}
                            </span>
                            @if ($event->location)
                                <span>
                                    <i class="fa-solid fa-location-dot me-1 text-orange"></i>
                                    {{ $event->location }}
                                </span>
                            @elseif ($event->online_url)
                                <span>
                                    <i class="fa-solid fa-globe me-1 text-orange"></i>
                                    En ligne
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-3 d-none d-lg-block">
                        <div class="mascot-art" style="width:clamp(130px,12vw,170px)">
                            <span class="m-ring"></span><span class="m-blob"></span>
                            <img src="{{ asset('assets/web/img/mascot.png') }}" alt="Mascotte Laravel CI"
                                 style="width:100%;height:auto;display:block;object-fit:contain;" />
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- ===== CORPS ===== --}}
    <section class="section">
        <div class="container">

            @if (session('success'))
                <div class="alert alert-success mb-4">
                    <i class="fa-solid fa-circle-check me-2"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger mb-4">
                    <i class="fa-solid fa-circle-exclamation me-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            <div class="row g-4">

                {{-- ── CONTENU PRINCIPAL ── --}}
                <div class="col-lg-8">

                    {{-- Description --}}
                    @if ($event->description)
                        <div class="prose mb-4">
                            {!! clean($event->description) !!}
                        </div>
                    @endif

                    {{-- Programme --}}
                    @if ($event->program)
                        <div class="info-card mb-4">
                            <div class="sidebar-title mb-3">
                                <i class="fa-solid fa-list-check me-2 text-orange"></i>Programme
                            </div>
                            <div class="prose">
                                {!! clean($event->program) !!}
                            </div>
                        </div>
                    @endif

                    {{-- Lieu & Accès détaillé (si location présente) --}}
                    @if ($event->location)
                        <div class="info-card mb-4">
                            <div class="sidebar-title mb-2">
                                <i class="fa-solid fa-location-dot me-2 text-orange"></i>Lieu & Accès
                            </div>
                            <p class="mb-0" style="font-size:.93rem;color:var(--muted)">
                                {{ $event->location }}
                            </p>
                        </div>
                    @endif

                </div>

                {{-- ── SIDEBAR ── --}}
                <div class="col-lg-4">
                    <div class="apply-card">

                        {{-- Lien vers le récapitulatif si disponible --}}
                        @if ($event->hasRecap())
                            <a href="#recap"
                               class="btn-primary d-flex align-items-center justify-content-center gap-2 mb-3"
                               style="background:var(--orange,#e7222c);border-color:var(--orange,#e7222c);color:#fff;padding:.65rem 1.25rem;border-radius:.5rem;font-weight:600;text-decoration:none;">
                                <i class="fa-solid fa-circle-play"></i>
                                Voir le récapitulatif
                            </a>
                        @endif

                        {{-- Bouton d'inscription --}}
                        @livewire('events.registration-button', ['event' => $event], key('reg-btn-' . $event->id))

                        {{-- Détails --}}
                        <div class="info-card mt-3">
                            <div class="sidebar-title mb-1">Détails</div>

                            <div class="info-row">
                                <div class="ic"><i class="{{ $event->type->icon() }}"></i></div>
                                <div>
                                    <div class="lbl">Type</div>
                                    <div class="val">{{ $event->type->label() }}</div>
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="ic"><i class="fa-regular fa-calendar"></i></div>
                                <div>
                                    <div class="lbl">Date</div>
                                    <div class="val">
                                        {{ $event->starts_at->translatedFormat('d M Y') }}
                                        @if (! $event->starts_at->isSameDay($event->ends_at))
                                            – {{ $event->ends_at->translatedFormat('d M Y') }}
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="ic"><i class="fa-regular fa-clock"></i></div>
                                <div>
                                    <div class="lbl">Horaire</div>
                                    <div class="val">
                                        {{ $event->starts_at->format('H:i') }} – {{ $event->ends_at->format('H:i') }}
                                    </div>
                                </div>
                            </div>

                            @if ($event->location)
                                <div class="info-row">
                                    <div class="ic"><i class="fa-solid fa-location-dot"></i></div>
                                    <div>
                                        <div class="lbl">Lieu</div>
                                        <div class="val">{{ $event->location }}</div>
                                    </div>
                                </div>
                            @endif

                            @if ($event->online_url)
                                <div class="info-row">
                                    <div class="ic"><i class="fa-solid fa-globe"></i></div>
                                    <div>
                                        <div class="lbl">Lien</div>
                                        <div class="val">
                                            <a href="{{ $event->online_url }}" target="_blank" rel="noopener">
                                                Rejoindre en ligne
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($capacity)
                                <div class="info-row">
                                    <div class="ic"><i class="fa-solid fa-user-group"></i></div>
                                    <div>
                                        <div class="lbl">Capacité</div>
                                        <div class="val">{{ $event->registrations_count }} / {{ $capacity }} inscrits</div>
                                    </div>
                                </div>
                                <div class="spots-label mt-2">
                                    <span></span>
                                    <span>{{ $spotsLeft }} place(s) restante(s)</span>
                                </div>
                                <div class="progress-spots">
                                    <div class="bar" style="width:{{ $pct }}%"></div>
                                </div>
                            @endif
                        </div>

                        {{-- Organisé par --}}
                        <div class="info-card mt-3">
                            <div class="sidebar-title mb-3">Organisé par</div>
                            <div class="d-flex align-items-center gap-3">
                                <img
                                    src="{{ asset('assets/web/img/logo.png') }}"
                                    alt="Laravel CI"
                                    style="width:40px;height:40px;border-radius:50%;object-fit:cover;border:1px solid #eee;"
                                    onerror="this.style.display='none';this.nextElementSibling.style.display='flex';"
                                >
                                <span
                                    class="avatar avatar-md av-1"
                                    style="display:none;background:#e7222c;color:#fff;font-weight:700"
                                >LC</span>
                                <div>
                                    <div style="font-weight:600">Laravel Côte d'Ivoire</div>
                                    <div class="text-muted-2" style="font-size:.85rem">@laravelci</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ===== RÉCAPITULATIF ===== --}}
    @if ($event->hasRecap())
        @php $recapPhotos = $event->mediaPhotos; @endphp

        <section id="recap" class="section" style="background:var(--surface-2,#f8fafc)">
            <div class="container">
                <div class="section-eyebrow">Après l'événement</div>
                <h2 class="mb-4">Récapitulatif</h2>

                @if ($event->recap_summary)
                    <p class="lead mb-4">{{ $event->recap_summary }}</p>
                @endif

                @if ($event->recap_content)
                    <div class="recap-content mb-5">
                        {!! clean($event->recap_content) !!}
                    </div>
                @endif

                {{-- ── Carrousel photos ── --}}
                @if ($recapPhotos->isNotEmpty())
                    <h3 class="mb-3" style="font-size:1.15rem;font-weight:700">
                        <i class="fa-solid fa-images me-2 text-orange"></i>Photos de l'événement
                        <span style="font-size:.85rem;font-weight:400;color:var(--muted)">
                            ({{ $recapPhotos->count() }})
                        </span>
                    </h3>

                    <div id="recapCarousel" class="carousel slide mb-3" data-bs-ride="false">

                        {{-- Indicateurs --}}
                        <div class="carousel-indicators">
                            @foreach ($recapPhotos as $i => $photo)
                                <button type="button"
                                        data-bs-target="#recapCarousel"
                                        data-bs-slide-to="{{ $i }}"
                                        @class(['active' => $i === 0])
                                        @if ($i === 0) aria-current="true" @endif
                                        aria-label="Photo {{ $i + 1 }}"></button>
                            @endforeach
                        </div>

                        {{-- Slides --}}
                        <div class="carousel-inner" style="border-radius:.75rem;overflow:hidden;background:#0f172a;">
                            @foreach ($recapPhotos as $i => $photo)
                                <div @class(['carousel-item', 'active' => $i === 0])>
                                    <img src="{{ $photo->url() }}"
                                         class="d-block w-100"
                                         alt="{{ $photo->caption ?? ($event->title . ' — photo ' . ($i + 1)) }}"
                                         loading="lazy"
                                         style="max-height:520px;object-fit:contain;background:#0f172a;" />
                                    @if ($photo->caption)
                                        <div class="carousel-caption d-none d-md-block"
                                             style="background:rgba(0,0,0,.45);border-radius:.5rem;padding:.4rem .75rem;bottom:1rem;">
                                            <p class="mb-0" style="font-size:.85rem">{{ $photo->caption }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        {{-- Contrôles --}}
                        <button class="carousel-control-prev" type="button" data-bs-target="#recapCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Précédent</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#recapCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Suivant</span>
                        </button>
                    </div>

                    {{-- Miniatures cliquables --}}
                    <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:2rem;">
                        @foreach ($recapPhotos as $i => $photo)
                            <button type="button"
                                    data-bs-target="#recapCarousel"
                                    data-bs-slide-to="{{ $i }}"
                                    style="padding:0;border:2px solid transparent;border-radius:.375rem;overflow:hidden;cursor:pointer;background:none;transition:border-color .15s;"
                                    onmouseover="this.style.borderColor='#e7222c'"
                                    onmouseout="this.style.borderColor='transparent'">
                                <img src="{{ $photo->thumbnailUrl() }}"
                                     alt="{{ $photo->caption ?? 'photo ' . ($i + 1) }}"
                                     loading="lazy"
                                     style="width:72px;height:54px;object-fit:cover;display:block;" />
                            </button>
                        @endforeach
                    </div>
                @endif

                {{-- ── Vidéos YouTube/Vimeo ── --}}
                @if (! empty($event->recapVideoUrls()))
                    <div class="row g-3 mb-5">
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
                @endif

                {{-- ── Galerie complète avec téléchargement ── --}}
                @if ($recapPhotos->isNotEmpty() || $event->mediaVideos->isNotEmpty())
                    <div class="mb-4">
                        <h3 class="mb-3" style="font-size:1.15rem;font-weight:700">
                            <i class="fa-solid fa-download me-2 text-orange"></i>Télécharger les médias
                        </h3>
                        @livewire('events.event-media-gallery', ['event' => $event], key('gallery-' . $event->id))
                    </div>
                @endif

                {{-- ── Document PDF ── --}}
                @if ($event->recapDocumentUrl())
                    <a href="{{ $event->recapDocumentUrl() }}" target="_blank" rel="noopener"
                       class="btn-outline-navy d-inline-flex align-items-center gap-2">
                        <i class="fa-solid fa-file-arrow-down"></i>
                        Télécharger {{ $event->recap_document_name ?? 'le document' }}
                    </a>
                @endif
            </div>
        </section>
    @endif
</div>
