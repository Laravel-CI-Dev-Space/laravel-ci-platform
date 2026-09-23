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

                        {{-- Lien vers la page récapitulative dédiée --}}
                        @if ($event->hasRecap())
                            <a href="{{ route('events.recap', $event->slug) }}"
                               class="d-flex align-items-center justify-content-center gap-2 mb-3"
                               style="background:#e7222c;color:#fff;padding:.65rem 1.25rem;border-radius:.5rem;font-weight:600;text-decoration:none;border:none;">
                                <i class="fa-solid fa-flag-checkered"></i>
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

    {{-- ===== TEASER RÉCAPITULATIF ===== --}}
    @if ($event->hasRecap())
        <section class="section" style="background:var(--surface-2,#f8fafc);padding:2.5rem 0;">
            <div class="container">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <div class="section-eyebrow mb-2">Après l'événement</div>
                        <h2 class="mb-2" style="font-size:1.5rem">Le récapitulatif est disponible</h2>
                        @if ($event->recap_summary)
                            <p class="mb-0" style="color:var(--muted);line-height:1.7">
                                {{ Str::limit($event->recap_summary, 180) }}
                            </p>
                        @endif
                    </div>
                    <div class="col-lg-4 d-flex justify-content-lg-end">
                        <a href="{{ route('events.recap', $event->slug) }}"
                           class="d-inline-flex align-items-center gap-2"
                           style="background:#e7222c;color:#fff;padding:.75rem 1.5rem;border-radius:.5rem;font-weight:700;text-decoration:none;white-space:nowrap;">
                            <i class="fa-solid fa-flag-checkered"></i>
                            Lire le récapitulatif
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    @endif
</div>
