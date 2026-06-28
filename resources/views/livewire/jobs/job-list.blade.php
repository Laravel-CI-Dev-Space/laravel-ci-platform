<div>
    {{-- ===== EN-TÊTE ===== --}}
    <section class="page-hero">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="breadcrumb-bar">
                        <a href="{{ route('home') }}">Accueil</a>
                        <i class="fa-solid fa-chevron-right"></i>
                        <span>Job Board</span>
                    </div>
                    <h1 class="mb-2">Job Board</h1>
                    <p class="lead mb-0">Offres Laravel &amp; PHP pour les développeurs ivoiriens — local, remote et diaspora.</p>
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

    {{-- ===== LAYOUT FILTRES + LISTE ===== --}}
    <section class="section-sm">
        <div class="container">

            {{-- KPI + Espace entreprise --}}
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="text-muted-2">
                    <strong class="text-navy">{{ number_format($offers->total()) }}</strong>
                    offre{{ $offers->total() !== 1 ? 's' : '' }} disponible{{ $offers->total() !== 1 ? 's' : '' }}
                </span>
                @auth
                    <a href="{{ route('company.login') }}" class="btn btn-ghost btn-sm">
                        <i class="fa-solid fa-building me-1"></i>Espace entreprise
                    </a>
                @endauth
            </div>

            <button class="btn btn-ghost d-lg-none mb-3 w-100" type="button"
                    data-bs-toggle="offcanvas" data-bs-target="#jobFilters">
                <i class="fa-solid fa-sliders me-1"></i> Filtres
            </button>

            <div class="row g-4">

                {{-- ═══ SIDEBAR FILTRES ═══ --}}
                <div class="col-lg-3" style="align-self:flex-start">
                    <div class="offcanvas-lg offcanvas-start d-lg-block job-filters-canvas" id="jobFilters">
                        <div class="offcanvas-header d-lg-none">
                            <h5 class="offcanvas-title">Filtres</h5>
                            <button class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#jobFilters"></button>
                        </div>
                        <div class="offcanvas-body d-block p-lg-0">

                            {{-- Recherche --}}
                            <div class="sidebar-card">
                                <div class="search-field">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <input type="search"
                                           wire:model.live.debounce.400ms="search"
                                           class="form-control"
                                           placeholder="Titre, mot-clé…" />
                                </div>
                            </div>

                            {{-- Type de contrat --}}
                            <div class="sidebar-card">
                                <div class="sidebar-title">Type de contrat</div>
                                <div class="filter-pills flex-column align-items-start gap-1">
                                    @foreach (['all' => 'Tous', 'cdi' => 'CDI', 'cdd' => 'CDD', 'freelance' => 'Freelance', 'internship' => 'Stage', 'apprenticeship' => 'Alternance'] as $val => $lbl)
                                        <button wire:click="$set('contractType','{{ $val }}')"
                                                class="filter-pill w-100 text-start {{ $contractType === $val ? 'active' : '' }}"
                                                style="margin-bottom:.2rem">
                                            {{ $lbl }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Niveau --}}
                            <div class="sidebar-card">
                                <div class="sidebar-title">Niveau</div>
                                <div class="filter-pills flex-column align-items-start gap-1">
                                    @foreach (['all' => 'Tous', 'junior' => 'Junior', 'intermediate' => 'Intermédiaire', 'senior' => 'Senior', 'lead' => 'Lead', 'any' => 'Tous niveaux'] as $val => $lbl)
                                        <button wire:click="$set('level','{{ $val }}')"
                                                class="filter-pill w-100 text-start {{ $level === $val ? 'active' : '' }}"
                                                style="margin-bottom:.2rem">
                                            {{ $lbl }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Remote --}}
                            <div class="sidebar-card">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="remoteToggle"
                                           wire:model.live="isRemote" />
                                    <label class="form-check-label" for="remoteToggle">Télétravail uniquement</label>
                                </div>
                            </div>

                            {{-- Catégories --}}
                            <div class="sidebar-card" x-data="{ shown: 5 }">
                                <div class="sidebar-title">
                                    Catégories
                                    @if ($categoryId !== null)
                                        <button wire:click="$set('categoryId', null)" class="btn btn-ghost btn-sm py-0 float-end" style="font-size:.72rem">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    @endif
                                </div>
                                <div class="tag-list">
                                    @foreach ($categories as $cat)
                                        <div wire:click="$set('categoryId', {{ $categoryId === $cat->id ? 'null' : $cat->id }})"
                                             class="tag-list-item {{ $categoryId === $cat->id ? 'active' : '' }}"
                                             style="cursor:pointer"
                                             x-show="{{ $loop->index }} < shown">
                                            <span>{{ $cat->name }}</span>
                                            <span class="count">{{ $cat->offers_count }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                @if ($categories->count() > 5)
                                    <button x-show="shown < {{ $categories->count() }}"
                                            x-on:click="shown += 5"
                                            class="btn btn-ghost btn-sm w-100 mt-1"
                                            style="font-size:.78rem; color:var(--orange)">
                                        <i class="fa-solid fa-chevron-down me-1"></i>Voir plus
                                    </button>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ═══ LISTE DES OFFRES ═══ --}}
                <div class="col-lg-9">

                    {{-- Tri --}}
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <span class="text-muted-2">
                            <strong class="text-navy">{{ number_format($offers->total()) }}</strong>
                            offre{{ $offers->total() !== 1 ? 's' : '' }}
                        </span>
                        <div class="d-flex gap-1">
                            @foreach (['recent' => 'Récentes', 'urgent' => 'Urgentes', 'closing' => 'Fermeture proche'] as $val => $lbl)
                                <button wire:click="$set('sort','{{ $val }}')"
                                        class="filter-pill {{ $sort === $val ? 'active' : '' }}"
                                        style="font-size:.8rem">
                                    {{ $lbl }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    @forelse ($offers as $offer)
                        <div class="job-card">
                            <div class="d-flex gap-3">
                                {{-- Logo entreprise --}}
                                <div class="company-logo cl-{{ ($offer->id % 6) + 1 }} flex-shrink-0">
                                    @if ($offer->company?->logo)
                                        <img src="{{ $offer->company->logoUrl() }}" alt="{{ $offer->company->name }}"
                                             style="width:100%;height:100%;object-fit:cover;border-radius:inherit" />
                                    @else
                                        {{ strtoupper(substr($offer->resolvedCompanyName() ?? $offer->title, 0, 2)) }}
                                    @endif
                                </div>

                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex justify-content-between gap-2">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                                <h3 style="font-size:1.1rem;margin:0" class="text-navy">
                                                    <a href="{{ route('jobs.show', $offer->slug) }}" class="text-navy">
                                                        {{ $offer->title }}
                                                    </a>
                                                </h3>
                                                @if ($offer->is_urgent)
                                                    <span class="badge-pill" style="background:#fdeaec;color:var(--level-advanced)">URGENT</span>
                                                @endif
                                                @if ($offer->isNew())
                                                    <span class="badge-pill badge-green">NOUVEAU</span>
                                                @endif
                                                @if ($offer->is_remote)
                                                    <span class="badge-pill badge-navy">Remote</span>
                                                @endif
                                            </div>
                                            <div class="text-muted-2" style="font-size:.88rem">
                                                <strong>{{ $offer->resolvedCompanyName() ?? 'Entreprise' }}</strong>
                                                @if ($offer->location)
                                                    · <i class="fa-solid fa-location-dot me-1"></i>{{ $offer->location }}
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Bouton favori --}}
                                        @auth
                                            <button class="save-heart" aria-label="Sauvegarder"
                                                    wire:click="$dispatch('toggle-favorite', { offerId: {{ $offer->id }} })">
                                                <i class="fa-regular fa-heart"></i>
                                            </button>
                                        @endauth
                                    </div>

                                    {{-- Tags --}}
                                    <div class="d-flex flex-wrap gap-2 my-2">
                                        <span class="badge-pill badge-soft">{{ $offer->contract_type->label() }}</span>
                                        <span class="badge-pill badge-soft">{{ $offer->level->label() }}</span>
                                        @foreach ($offer->skills->take(4) as $skill)
                                            <span class="tag">{{ $skill->name }}</span>
                                        @endforeach
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        @if ($offer->salaryRange())
                                            <span class="salary">{{ $offer->salaryRange() }}</span>
                                        @else
                                            <span class="text-muted-2" style="font-size:.85rem">Salaire non précisé</span>
                                        @endif
                                        <a href="{{ route('jobs.show', $offer->slug) }}" class="btn btn-brand btn-sm">
                                            Voir l'offre <i class="fa-solid fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="card-soft h-auto p-5 text-center">
                            <i class="fa-solid fa-briefcase fa-2x mb-3 text-muted-2"></i>
                            <p class="mb-3">Aucune offre ne correspond à vos critères.</p>
                            <button wire:click="$set('search', '')" class="btn btn-ghost btn-sm">
                                <i class="fa-solid fa-xmark me-1"></i>Réinitialiser les filtres
                            </button>
                        </div>
                    @endforelse

                    <div class="mt-4 d-flex justify-content-center">
                        {{ $offers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
