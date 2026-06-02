<div>
    {{-- ===== EN-TÊTE ===== --}}
    <section class="page-hero">
        <div class="container">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="breadcrumb-bar">
                        <a href="{{ route('home') }}">Accueil</a>
                        <i class="fa-solid fa-chevron-right"></i>
                        <span>Ressources</span>
                    </div>
                    <h1 class="mb-2">Ressources téléchargeables</h1>
                    <p class="lead mb-4">Boilerplates, cheatsheets, guides et fichiers utiles partagés par la communauté.</p>

                    {{-- Filtres par type --}}
                    <div class="filter-pills">
                        @foreach ([
                            'all'         => 'Tous',
                            'boilerplate' => 'Boilerplate',
                            'cheatsheet'  => 'Cheatsheet',
                            'guide'       => 'Guide',
                            'pdf'         => 'PDF',
                            'other'       => 'Autre',
                        ] as $value => $label)
                            <button
                                wire:click="$set('type', '{{ $value }}')"
                                class="filter-pill {{ $type === $value ? 'active' : '' }}"
                            >
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

    {{-- ===== LISTE DES RESSOURCES ===== --}}
    <section class="section">
        <div class="container">

            <p class="text-muted-2 mb-4">
                <strong class="text-navy">{{ number_format($resources->total()) }}</strong>
                ressource{{ $resources->total() !== 1 ? 's' : '' }}
            </p>

            @forelse ($resources as $resource)
                @php
                    $icon = match ($resource->type) {
                        'pdf'         => 'fa-file-pdf',
                        'boilerplate' => 'fa-file-zipper',
                        'guide'       => 'fa-book-open',
                        'cheatsheet'  => 'fa-file-lines',
                        default       => 'fa-file',
                    };
                    $iconColor = match ($resource->type) {
                        'pdf'         => '#e74c3c',
                        'boilerplate' => '#f39c12',
                        'guide'       => '#3498db',
                        'cheatsheet'  => '#27ae60',
                        default       => 'var(--muted)',
                    };
                @endphp
                <div class="resource-card mb-3 reveal">
                    <div class="file-icon" style="background:{{ $iconColor }}22; color:{{ $iconColor }}; width:3rem; height:3rem; display:flex; align-items:center; justify-content:center; border-radius:.75rem; flex-shrink:0;">
                        <i class="fa-solid {{ $icon }}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h3 style="font-size:1.05rem;margin-bottom:.2rem" class="text-navy">
                            {{ $resource->title }}
                        </h3>
                        @if ($resource->description)
                            <p class="mb-1" style="font-size:.88rem;color:var(--muted)">
                                {{ $resource->description }}
                            </p>
                        @endif
                        <div class="d-flex align-items-center gap-3" style="font-size:.82rem;color:var(--muted)">
                            <span>
                                <i class="fa-solid fa-tag me-1"></i>
                                {{ ['boilerplate' => 'Boilerplate', 'cheatsheet' => 'Cheatsheet', 'guide' => 'Guide', 'pdf' => 'PDF', 'other' => 'Autre'][$resource->type] ?? $resource->type }}
                            </span>
                            <span>
                                <i class="fa-solid fa-weight-hanging me-1"></i>
                                {{ $resource->fileSizeHuman() }}
                            </span>
                            <span>
                                <i class="fa-solid fa-download me-1"></i>
                                {{ number_format($resource->downloads_count) }} téléchargement{{ $resource->downloads_count !== 1 ? 's' : '' }}
                            </span>
                            <span>
                                <i class="fa-regular fa-user me-1"></i>
                                {{ $resource->user->name }}
                            </span>
                        </div>
                    </div>
                    @auth
                        <a
                            href="{{ route('resources.download', $resource) }}"
                            class="btn btn-outline-brand flex-shrink-0"
                            title="Télécharger"
                        >
                            <i class="fa-solid fa-download"></i>
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="btn btn-ghost btn-sm flex-shrink-0"
                            title="Connectez-vous pour télécharger"
                        >
                            <i class="fa-solid fa-lock me-1"></i> Connexion
                        </a>
                    @endauth
                </div>
            @empty
                <div class="card-soft p-5 text-center">
                    <i class="fa-regular fa-folder-open fa-2x mb-3 text-muted-2"></i>
                    <p class="mb-0">Aucune ressource disponible pour le moment.</p>
                </div>
            @endforelse

            {{-- Pagination --}}
            <div class="mt-4 d-flex justify-content-center">
                {{ $resources->links() }}
            </div>
        </div>
    </section>
</div>
