<div>
    {{-- ===== EN-TÊTE ===== --}}
    <section class="page-hero">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="breadcrumb-bar">
                        <a href="{{ route('home') }}">Accueil</a>
                        <i class="fa-solid fa-chevron-right"></i>
                        <span>Événements</span>
                    </div>
                    <h1 class="mb-2">Événements</h1>
                    <p class="lead mb-3">Meetups, webinaires et hackathons — à Abidjan et en ligne. Venez apprendre et construire avec la communauté.</p>

                    {{-- Filtres type --}}
                    <div class="filter-pills mb-2">
                        @foreach (['all' => 'Tous', 'meetup' => 'Meetup', 'webinar' => 'Webinaire', 'hackathon' => 'Hackathon', 'conference' => 'Conférence', 'workshop' => 'Workshop'] as $value => $label)
                            <button wire:click="$set('type', '{{ $value }}')"
                                    class="filter-pill {{ $type === $value ? 'active' : '' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Filtres période --}}
                    <div class="filter-pills">
                        @foreach (['upcoming' => 'À venir', 'past' => 'Passés', 'all' => 'Tous'] as $value => $label)
                            <button wire:click="$set('period', '{{ $value }}')"
                                    class="filter-pill {{ $period === $value ? 'active' : '' }}">
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

    {{-- ===== LISTE DES ÉVÉNEMENTS ===== --}}
    <section class="section">
        <div class="container">

            {{-- Tri --}}
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <span class="text-muted-2" style="font-size:.9rem">{{ $events->total() }} événement(s)</span>
                <div class="d-flex flex-wrap gap-2">
                    @foreach (['soonest' => 'Date la plus proche', 'recent' => 'Récemment ajoutés'] as $value => $label)
                        <button wire:click="$set('sort', '{{ $value }}')"
                                class="filter-pill {{ $sort === $value ? 'active' : '' }}"
                                style="font-size:.82rem">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            @if ($events->isEmpty())
                <x-web.empty-state
                    icon="fa-solid fa-calendar-xmark"
                    title="Aucun événement trouvé"
                    message="Il n'y a pas d'événement correspondant à vos filtres pour le moment."
                />
            @else
                <div class="row g-4">
                    @foreach ($events as $event)
                        @php
                            $typeConfig = match($event->type) {
                                'meetup'     => ['label' => 'Meetup',      'icon' => 'fa-solid fa-people-roof',   'bg' => '#fff4e5', 'color' => '#ea580c', 'bannerClass' => 'ev-meetup'],
                                'webinar'    => ['label' => 'Webinaire',   'icon' => 'fa-solid fa-video',         'bg' => '#e7ebff', 'color' => '#4361ee', 'bannerClass' => 'ev-webinar'],
                                'hackathon'  => ['label' => 'Hackathon',   'icon' => 'fa-solid fa-laptop-code',   'bg' => '#f1e7ff', 'color' => '#7209b7', 'bannerClass' => 'ev-hackathon'],
                                'conference' => ['label' => 'Conférence',  'icon' => 'fa-solid fa-microphone',    'bg' => '#e7fff1', 'color' => '#2d9b4e', 'bannerClass' => 'ev-meetup'],
                                'workshop'   => ['label' => 'Workshop',    'icon' => 'fa-solid fa-screwdriver-wrench', 'bg' => '#e7f7ff', 'color' => '#0891b2', 'bannerClass' => 'ev-webinar'],
                                default      => ['label' => ucfirst($event->type), 'icon' => 'fa-solid fa-calendar', 'bg' => '#f1f5f9', 'color' => '#64748b', 'bannerClass' => 'ev-meetup'],
                            };
                            $spotsLeft = $event->spotsLeft();
                            $pct       = ($event->capacity && $event->capacity > 0)
                                ? min(100, round(($event->registrations_count / $event->capacity) * 100))
                                : 0;
                            $isPast    = $event->isPast();
                        @endphp

                        <div class="col-md-6 col-lg-4 reveal">
                            <article class="card-soft event-card {{ $isPast ? 'past' : '' }}">
                                @if ($isPast)
                                    <span class="past-badge badge-pill badge-soft">
                                        <i class="fa-solid fa-clock-rotate-left"></i> Événement passé
                                    </span>
                                @endif
                                <div class="event-banner {{ $typeConfig['bannerClass'] }}"></div>
                                <div class="card-pad">
                                    <div class="d-flex gap-3 mb-3">
                                        <div class="event-date-chip">
                                            <div class="m">{{ $event->starts_at->translatedFormat('M') }}</div>
                                            <div class="d">{{ $event->starts_at->format('d') }}</div>
                                        </div>
                                        <div>
                                            <span class="badge-pill"
                                                  style="background:{{ $typeConfig['bg'] }};color:{{ $typeConfig['color'] }}">
                                                <i class="{{ $typeConfig['icon'] }}"></i>
                                                {{ $typeConfig['label'] }}
                                            </span>
                                            <h3 class="art-title mt-2 mb-0" style="font-size:1.05rem">
                                                <a href="{{ route('events.show', $event->slug) }}" class="text-navy">
                                                    {{ $event->title }}
                                                </a>
                                            </h3>
                                        </div>
                                    </div>

                                    <div class="d-flex flex-column gap-1 mb-3" style="font-size:.86rem;color:var(--muted)">
                                        <span>
                                            <i class="fa-regular fa-clock text-orange me-2"></i>
                                            {{ $event->starts_at->translatedFormat('D H:i') }}
                                        </span>
                                        @if ($event->location)
                                            <span>
                                                <i class="fa-solid fa-location-dot text-orange me-2"></i>
                                                {{ $event->location }}
                                            </span>
                                        @elseif ($event->online_url)
                                            <span>
                                                <i class="fa-solid fa-globe text-orange me-2"></i>
                                                En ligne
                                            </span>
                                        @endif
                                    </div>

                                    @if ($event->capacity && ! $isPast)
                                        <div class="spots-label">
                                            <span>{{ $event->registrations_count }} / {{ $event->capacity }}</span>
                                            <span>{{ $spotsLeft }} restante(s)</span>
                                        </div>
                                        <div class="progress-spots mb-3">
                                            <div class="bar" style="width:{{ $pct }}%"></div>
                                        </div>
                                    @endif

                                    <div class="d-flex gap-2">
                                        @if ($isPast)
                                            <a href="{{ route('events.show', $event->slug) }}"
                                               class="btn btn-ghost w-100">
                                                <i class="fa-solid fa-circle-info"></i> Voir les détails
                                            </a>
                                        @else
                                            <a href="{{ route('events.show', $event->slug) }}"
                                               class="btn btn-brand flex-grow-1">
                                                <i class="fa-solid fa-ticket"></i> S'inscrire
                                            </a>
                                            <a href="{{ route('events.show', $event->slug) }}"
                                               class="btn btn-ghost" aria-label="Détails">
                                                <i class="fa-solid fa-arrow-right"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </section>
</div>
