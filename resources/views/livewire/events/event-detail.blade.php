<div>
    @php
        $typeConfig = match($event->type) {
            'meetup'     => ['label' => 'Meetup',      'icon' => 'fa-solid fa-people-roof',       'bg' => '#fff4e5', 'color' => '#ea580c'],
            'webinar'    => ['label' => 'Webinaire',   'icon' => 'fa-solid fa-video',             'bg' => '#e7ebff', 'color' => '#4361ee'],
            'hackathon'  => ['label' => 'Hackathon',   'icon' => 'fa-solid fa-laptop-code',       'bg' => '#f1e7ff', 'color' => '#7209b7'],
            'conference' => ['label' => 'Conférence',  'icon' => 'fa-solid fa-microphone',        'bg' => '#e7fff1', 'color' => '#2d9b4e'],
            'workshop'   => ['label' => 'Workshop',    'icon' => 'fa-solid fa-screwdriver-wrench','bg' => '#e7f7ff', 'color' => '#0891b2'],
            default      => ['label' => ucfirst($event->type), 'icon' => 'fa-solid fa-calendar',  'bg' => '#f1f5f9', 'color' => '#64748b'],
        };
        $spotsLeft   = $event->spotsLeft();
        $capacity    = $event->capacity;
        $pct         = ($capacity && $capacity > 0)
            ? min(100, round(($event->registrations_count / $capacity) * 100))
            : 0;
    @endphp

    {{-- ===== HERO ===== --}}
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
                          style="background:{{ $typeConfig['bg'] }};color:{{ $typeConfig['color'] }}">
                        <i class="{{ $typeConfig['icon'] }}"></i> {{ $typeConfig['label'] }}
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
                        <img src="{{ asset('assets/web/img/mascot.png') }}" alt="Mascotte Laravel CI" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== CORPS ===== --}}
    <section class="section">
        <div class="container">
            <div class="row g-4">

                {{-- CONTENU PRINCIPAL --}}
                <div class="col-lg-8">

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

                    @if ($event->cover_image)
                        <img src="{{ asset('assets/' . $event->cover_image) }}"
                             alt="{{ $event->title }}"
                             class="img-fluid rounded mb-4" />
                    @endif

                    <div class="prose mb-4">
                        {!! clean($event->description) !!}
                    </div>

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

                    {{-- Organisateur --}}
                    @if ($event->creator)
                        <div class="info-card">
                            <div class="sidebar-title mb-3">Organisé par</div>
                            <div class="d-flex align-items-center gap-3">
                                <span class="avatar avatar-md av-1">
                                    {{ substr($event->creator->name ?? 'LC', 0, 2) }}
                                </span>
                                <div>
                                    <div style="font-weight:600">{{ $event->creator->name }}</div>
                                    <div class="text-muted-2" style="font-size:.85rem">
                                        {{ $event->creator->github_username ?? '' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- SIDEBAR --}}
                <div class="col-lg-4">
                    <div class="apply-card">

                        {{-- Bouton d'inscription --}}
                        @livewire('events.registration-button', ['event' => $event], key('reg-btn-' . $event->id))

                        {{-- Détails --}}
                        <div class="info-card mt-3">
                            <div class="sidebar-title">Détails</div>

                            <div class="info-row">
                                <div class="ic"><i class="{{ $typeConfig['icon'] }}"></i></div>
                                <div>
                                    <div class="lbl">Type</div>
                                    <div class="val">{{ $typeConfig['label'] }}</div>
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
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>
