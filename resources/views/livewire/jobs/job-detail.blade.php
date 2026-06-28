<div>
    {{-- ===== EN-TÊTE ===== --}}
    <section class="page-hero">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-9">
                    <div class="breadcrumb-bar">
                        <a href="{{ route('home') }}">Accueil</a>
                        <i class="fa-solid fa-chevron-right"></i>
                        <a href="{{ route('jobs.index') }}">Jobs</a>
                        <i class="fa-solid fa-chevron-right"></i>
                        <span>{{ Str::limit($offer->title, 40) }}</span>
                    </div>

                    <div class="d-flex gap-3 align-items-start flex-wrap">
                        {{-- Logo --}}
                        <div class="company-logo cl-{{ ($offer->id % 6) + 1 }}" style="width:68px;height:68px;font-size:1.4rem; flex-shrink:0">
                            @if ($offer->company?->logo)
                                <img src="{{ $offer->company->logoUrl() }}" alt="{{ $offer->company->name }}"
                                     style="width:100%;height:100%;object-fit:cover;border-radius:inherit" />
                            @else
                                {{ strtoupper(substr($offer->resolvedCompanyName() ?? $offer->title, 0, 2)) }}
                            @endif
                        </div>

                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                                <h1 class="mb-0" style="font-size:var(--fs-h1)">{{ $offer->title }}</h1>
                                @if ($offer->is_urgent)
                                    <span class="badge-pill" style="background:#fdeaec;color:var(--level-advanced)">URGENT</span>
                                @endif
                                @if ($offer->isNew())
                                    <span class="badge-pill badge-green">NOUVEAU</span>
                                @endif
                                @if ($offer->isAdminPosted())
                                    <span class="badge-pill" style="background:#eef2ff;color:#4f46e5">
                                        <i class="fa-solid fa-shield-halved me-1"></i>Publiée par l'équipe
                                    </span>
                                @endif
                            </div>
                            <div class="d-flex flex-wrap gap-3" style="color:var(--muted);font-size:.95rem">
                                @if ($offer->resolvedCompanyName())
                                    <span><i class="fa-solid fa-building me-1 text-orange"></i>{{ $offer->resolvedCompanyName() }}</span>
                                @endif
                                @if ($offer->location)
                                    <span><i class="fa-solid fa-location-dot me-1 text-orange"></i>{{ $offer->location }}</span>
                                @endif
                                @if ($offer->is_remote)
                                    <span><i class="fa-solid fa-wifi me-1 text-orange"></i>Télétravail possible</span>
                                @endif
                                <span><i class="fa-regular fa-clock me-1 text-orange"></i>Publiée {{ $offer->published_at?->diffForHumans() ?? 'récemment' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 d-none d-lg-block">
                    <div class="mascot-art" style="width:clamp(120px,10vw,150px)">
                        <span class="m-ring"></span><span class="m-blob"></span>
                        <img src="{{ asset('assets/web/img/mascot.png') }}" alt="Mascotte Laravel CI" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="container">
            <div class="row g-4">

                {{-- ===== CORPS ===== --}}
                <div class="col-lg-8">

                    @if (session('success'))
                        <div class="alert alert-success mb-4">
                            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                        </div>
                    @endif

                    {{-- Description du poste --}}
                    <div class="prose article-body">
                        {!! $offer->description !!}
                    </div>

                    {{-- Profil du poste --}}
                    @if ($offer->profile_description)
                        <h2 class="section-heading mt-5 mb-3" style="font-size:1.25rem">Profil du poste</h2>
                        <div class="prose article-body">
                            {!! $offer->profile_description !!}
                        </div>
                    @endif

                    {{-- Environnement technique --}}
                    @if ($offer->tech_stack)
                        <h2 class="section-heading mt-5 mb-3" style="font-size:1.25rem">Environnement technique &amp; stack</h2>
                        <div class="prose article-body">
                            {!! $offer->tech_stack !!}
                        </div>
                    @endif

                    {{-- Ce que l'entreprise offre --}}
                    @if ($offer->benefits)
                        <h2 class="section-heading mt-5 mb-3" style="font-size:1.25rem">Ce que nous offrons</h2>
                        <div class="prose article-body">
                            {!! $offer->benefits !!}
                        </div>
                    @endif

                    {{-- Skills & tags --}}
                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <span class="badge-pill badge-soft">{{ $offer->contract_type->label() }}</span>
                        <span class="badge-pill badge-soft">{{ $offer->level->label() }}</span>
                        @foreach ($offer->skills as $skill)
                            <span class="tag">{{ $skill->name }}</span>
                        @endforeach
                        @foreach ($offer->domains ?? [] as $domain)
                            <span class="tag">{{ $domain }}</span>
                        @endforeach
                    </div>

                    {{-- Comment postuler (offre admin / externe) --}}
                    @if ($offer->isAdminPosted() && ($offer->apply_email || $offer->apply_url))
                        <div class="card-soft p-4 mt-5">
                            <h3 class="mb-3" style="font-size:1.05rem">
                                <i class="fa-solid fa-paper-plane text-orange me-2"></i>Comment postuler
                            </h3>
                            @if ($offer->apply_email)
                                <p class="mb-2" style="font-size:.95rem">
                                    Envoyez votre CV à :
                                    <a href="mailto:{{ $offer->apply_email }}" class="text-orange fw-semibold">
                                        {{ $offer->apply_email }}
                                    </a>
                                </p>
                            @endif
                            @if ($offer->apply_url)
                                <a href="{{ $offer->apply_url }}" target="_blank" rel="noopener" class="btn btn-brand mt-2">
                                    <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Postuler sur le site de l'entreprise
                                </a>
                            @endif
                        </div>

                    {{-- Formulaire de candidature en ligne (offre entreprise plateforme) --}}
                    @elseif (! $offer->isAdminPosted())
                        @auth
                            @if ($hasApplied)
                                <div class="card-soft p-4 mt-5 text-center">
                                    <i class="fa-solid fa-circle-check fa-2x text-success mb-2"></i>
                                    <p class="mb-0 fw-semibold">Vous avez déjà postulé à cette offre.</p>
                                </div>
                            @elseif ($offer->status === \App\Enums\JobOfferStatus::Active)
                                <div class="mt-5">
                                    @livewire('jobs.job-application-form', ['offer' => $offer], key('apply-'.$offer->id))
                                </div>
                            @endif
                        @else
                            <div class="card-soft p-4 mt-5 text-center">
                                <p class="mb-3">Connectez-vous pour postuler à cette offre.</p>
                                <a href="{{ route('login') }}" class="btn btn-brand">
                                    <i class="fa-brands fa-github me-1"></i>Se connecter
                                </a>
                            </div>
                        @endauth
                    @endif
                </div>

                {{-- ===== SIDEBAR ===== --}}
                <div class="col-lg-4">
                    <div style="position:sticky; top:2rem;">

                        {{-- Carte action principale --}}
                        <div class="apply-card">
                            <div class="info-card">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        @if ($offer->salaryRange())
                                            <div class="lbl text-muted-2" style="font-size:.8rem">Rémunération</div>
                                            <div class="salary" style="font-size:1.15rem">{{ $offer->salaryRange() }}</div>
                                        @else
                                            <div class="text-muted-2" style="font-size:.88rem">Salaire non communiqué</div>
                                        @endif
                                    </div>
                                    {{-- Favori --}}
                                    <button wire:click="toggleFavorite"
                                            class="save-heart {{ $favorited ? 'saved' : '' }}"
                                            aria-label="Sauvegarder">
                                        <i class="fa-{{ $favorited ? 'solid' : 'regular' }} fa-heart" style="{{ $favorited ? 'color:var(--orange)' : '' }}"></i>
                                    </button>
                                </div>

                                @if ($offer->isAdminPosted())
                                    @if ($offer->apply_url)
                                        <a href="{{ $offer->apply_url }}" target="_blank" rel="noopener" class="btn btn-brand w-100">
                                            <i class="fa-solid fa-arrow-up-right-from-square me-1"></i>Postuler
                                        </a>
                                    @elseif ($offer->apply_email)
                                        <a href="mailto:{{ $offer->apply_email }}" class="btn btn-brand w-100">
                                            <i class="fa-solid fa-envelope me-1"></i>Postuler par email
                                        </a>
                                    @endif
                                @else
                                    @auth
                                        @if ($hasApplied)
                                            <button class="btn btn-success w-100" disabled>
                                                <i class="fa-solid fa-circle-check me-1"></i>Déjà postulé
                                            </button>
                                        @elseif ($offer->status === \App\Enums\JobOfferStatus::Active)
                                            <a href="#apply-form" class="btn btn-brand w-100">
                                                <i class="fa-solid fa-paper-plane me-1"></i>Postuler maintenant
                                            </a>
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-brand w-100">
                                            <i class="fa-solid fa-paper-plane me-1"></i>Postuler
                                        </a>
                                    @endauth
                                @endif
                            </div>

                            <div class="info-card">
                                <div class="sidebar-title">Détails du poste</div>
                                <div class="info-row">
                                    <div class="ic"><i class="fa-solid fa-briefcase"></i></div>
                                    <div><div class="lbl">Contrat</div><div class="val">{{ $offer->contract_type->label() }}</div></div>
                                </div>
                                <div class="info-row">
                                    <div class="ic"><i class="fa-solid fa-layer-group"></i></div>
                                    <div><div class="lbl">Niveau</div><div class="val">{{ $offer->level->label() }}</div></div>
                                </div>
                                @if ($offer->experience_years)
                                    <div class="info-row">
                                        <div class="ic"><i class="fa-solid fa-hourglass-half"></i></div>
                                        <div><div class="lbl">Expérience</div><div class="val">{{ $offer->experience_years }} an{{ $offer->experience_years > 1 ? 's' : '' }} minimum</div></div>
                                    </div>
                                @endif
                                @if (! empty($offer->education_levels))
                                    <div class="info-row">
                                        <div class="ic"><i class="fa-solid fa-graduation-cap"></i></div>
                                        <div>
                                            <div class="lbl">Niveau académique</div>
                                            <div class="val">{{ implode(', ', $offer->education_levels) }}</div>
                                        </div>
                                    </div>
                                @endif
                                @if (! empty($offer->domains))
                                    <div class="info-row">
                                        <div class="ic"><i class="fa-solid fa-tag"></i></div>
                                        <div>
                                            <div class="lbl">Domaine(s)</div>
                                            <div class="val">{{ implode(', ', $offer->domains) }}</div>
                                        </div>
                                    </div>
                                @endif
                                @if ($offer->location)
                                    <div class="info-row">
                                        <div class="ic"><i class="fa-solid fa-location-dot"></i></div>
                                        <div><div class="lbl">Lieu</div><div class="val">{{ $offer->location }}</div></div>
                                    </div>
                                @endif
                                @if ($offer->expires_at)
                                    <div class="info-row">
                                        <div class="ic"><i class="fa-regular fa-clock"></i></div>
                                        <div><div class="lbl">Date limite</div><div class="val">{{ $offer->expires_at->format('d/m/Y') }}</div></div>
                                    </div>
                                @endif
                                @if (! $offer->isAdminPosted())
                                    <div class="info-row">
                                        <div class="ic"><i class="fa-solid fa-users"></i></div>
                                        <div>
                                            <div class="lbl">Candidatures</div>
                                            <div class="val">{{ $offer->applications_count }} reçue{{ $offer->applications_count !== 1 ? 's' : '' }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if ($offer->company)
                                <div class="info-card">
                                    <div class="sidebar-title">À propos de {{ $offer->company->name }}</div>
                                    @if ($offer->company->description)
                                        <p class="text-muted-2 mb-3" style="font-size:.88rem">
                                            {{ Str::limit($offer->company->description, 200) }}
                                        </p>
                                    @endif
                                    @if ($offer->company->website)
                                        <a href="{{ $offer->company->website }}" target="_blank" rel="noopener" class="view-all-link">
                                            Visiter le site <i class="fa-solid fa-arrow-up-right-from-square ms-1"></i>
                                        </a>
                                    @endif
                                </div>
                            @elseif ($offer->company_name)
                                <div class="info-card">
                                    <div class="sidebar-title">À propos de {{ $offer->company_name }}</div>
                                    <p class="text-muted-2" style="font-size:.88rem">
                                        Cette entreprise n'est pas encore sur la plateforme Laravel CI.
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Offres similaires --}}
            @if ($similarOffers->isNotEmpty())
                <div class="mt-5 pt-4">
                    <h2 class="section-heading mb-4">Offres similaires</h2>
                    @foreach ($similarOffers as $similar)
                        <div class="job-card">
                            <div class="d-flex gap-3">
                                <div class="company-logo cl-{{ ($similar->id % 6) + 1 }}">
                                    {{ strtoupper(substr($similar->resolvedCompanyName() ?? $similar->title, 0, 2)) }}
                                </div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex justify-content-between gap-2">
                                        <div>
                                            <h3 style="font-size:1.05rem;margin:0" class="text-navy">
                                                <a href="{{ route('jobs.show', $similar->slug) }}" class="text-navy">
                                                    {{ $similar->title }}
                                                </a>
                                            </h3>
                                            <div class="text-muted-2" style="font-size:.85rem">
                                                {{ $similar->resolvedCompanyName() ?? '' }}
                                            </div>
                                        </div>
                                        @if ($similar->salaryRange())
                                            <span class="salary text-nowrap" style="font-size:.9rem">{{ $similar->salaryRange() }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</div>
