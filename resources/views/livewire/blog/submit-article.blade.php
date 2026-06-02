<div>
    @if (session('success'))
        <div class="alert alert-success m-3">{{ session('success') }}</div>
    @endif

    <div class="py-4">
        <div class="container-lg">

            {{-- En-tête de page --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <div class="breadcrumb-bar mb-1">
                        <a href="{{ route('home') }}">Accueil</a>
                        <i class="fa-solid fa-chevron-right"></i>
                        <a href="{{ route('blog.index') }}">Blog</a>
                        <i class="fa-solid fa-chevron-right"></i>
                        <span>Rédiger</span>
                    </div>
                    <h1 class="mb-0" style="font-size:var(--fs-h3)">Rédiger un article</h1>
                </div>
                <a href="{{ route('blog.index') }}" class="btn btn-ghost btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i> Retour au blog
                </a>
            </div>

            {{-- Titre + Extrait (pleine largeur) --}}
            <div class="card-soft p-4 mb-3">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">
                            Titre <span class="text-danger">*</span>
                            <span class="text-muted-2 fw-normal">(min. 10, max. 300 car.)</span>
                        </label>
                        <input
                            type="text"
                            wire:model.live="title"
                            class="form-control @error('title') is-invalid @enderror"
                            placeholder="Un titre clair et précis…"
                            maxlength="300"
                        />
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="text-muted-2 mt-1" style="font-size:.8rem">{{ strlen($title) }} / 300</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">
                            Extrait
                            <span class="text-muted-2 fw-normal">(optionnel)</span>
                        </label>
                        <textarea
                            wire:model.live="excerpt"
                            class="form-control @error('excerpt') is-invalid @enderror"
                            rows="3"
                            placeholder="Résumé affiché sur la liste des articles…"
                            maxlength="500"
                        ></textarea>
                        @error('excerpt')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Éditeur + Sidebar --}}
            <div class="row g-3 align-items-start">

                {{-- ─── COLONNE GAUCHE : éditeur Markdown ─── --}}
                <div class="col-lg-8">
                    <div class="card-soft p-4">
                        <label class="form-label fw-semibold mb-1">
                            Contenu <span class="text-danger">*</span>
                        </label>
                        <p class="text-muted-2 mb-2" style="font-size:.8rem">
                            <i class="fa-brands fa-markdown me-1"></i>Markdown supporté ·
                            Utilisez le bouton <strong><i class="fa-solid fa-terminal"></i></strong> de la barre pour insérer un bloc de code avec coloration syntaxique
                        </p>

                        @error('body')
                            <div class="text-danger mb-2" style="font-size:.875rem">
                                <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                            </div>
                        @enderror

                        {{--
                            wire:ignore : Livewire ne touche pas au DOM géré par EasyMDE.
                            data-initial : valeur initiale passée proprement sans risque d'injection.
                            x-data="easyMdeEditor" : composant Alpine défini dans submit.blade.php.
                        --}}
                        <div
                            x-data="easyMdeEditor"
                            data-initial="{{ e($body) }}"
                            wire:ignore
                        >
                            <textarea x-ref="mdeBody" id="article-mde"></textarea>
                        </div>
                    </div>
                </div>

                {{-- ─── COLONNE DROITE : métadonnées + actions ─── --}}
                <div class="col-lg-4">

                    {{-- Niveau --}}
                    <div class="card-soft p-4 mb-3">
                        <label class="form-label fw-semibold mb-2">
                            Niveau <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex flex-column gap-2">
                            @foreach ([
                                'beginner'     => ['Débutant',      'fa-seedling',   '#2ecc71', '#edfaf3'],
                                'intermediate' => ['Intermédiaire', 'fa-chart-line', '#e8590c', '#fff5f0'],
                                'advanced'     => ['Avancé',        'fa-fire',       '#e74c3c', '#fdeaec'],
                            ] as $val => [$lbl, $ico, $color, $bg])
                                <button
                                    type="button"
                                    wire:click="$set('level', '{{ $val }}')"
                                    class="d-flex align-items-center gap-2 px-3 py-2 rounded-3 border w-100 text-start"
                                    style="background:{{ $level === $val ? $bg : '#fff' }};
                                           border-color:{{ $level === $val ? $color : '#dee2e6' }} !important;
                                           color:{{ $level === $val ? $color : '#6c757d' }};
                                           font-weight:{{ $level === $val ? '600' : '400' }};
                                           transition:.15s;"
                                >
                                    <i class="fa-solid {{ $ico }}"></i>
                                    {{ $lbl }}
                                    @if ($level === $val)
                                        <i class="fa-solid fa-circle-check ms-auto"></i>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                        @error('level')
                            <div class="text-danger mt-2" style="font-size:.82rem">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Tags --}}
                    <div class="card-soft p-4 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-semibold mb-0">
                                Tags <span class="text-danger">*</span>
                            </label>
                            <span class="text-muted-2" style="font-size:.78rem">
                                {{ count($selectedTags) }}/5
                            </span>
                        </div>

                        @error('selectedTags')
                            <div class="text-danger mb-2" style="font-size:.82rem">{{ $message }}</div>
                        @enderror

                        {{-- Tags sélectionnés --}}
                        @if (count($selectedTags) > 0)
                            <div class="d-flex flex-wrap gap-1 mb-2">
                                @foreach ($tags->whereIn('id', $selectedTags) as $tag)
                                    <span
                                        class="badge d-inline-flex align-items-center gap-1"
                                        style="background:var(--orange,#e8590c);color:#fff;border-radius:2rem;font-size:.75rem;padding:.25rem .6rem;cursor:pointer;"
                                        wire:click="removeTag({{ $tag->id }})"
                                        title="Retirer {{ $tag->name }}"
                                    >
                                        {{ $tag->name }}
                                        <i class="fa-solid fa-xmark" style="font-size:.6rem"></i>
                                    </span>
                                @endforeach
                            </div>
                        @endif

                        {{-- Tags disponibles --}}
                        <div style="max-height:200px; overflow-y:auto; margin:0 -.5rem; padding:0 .5rem;">
                            @foreach ($tags->whereNotIn('id', $selectedTags) as $tag)
                                <div
                                    class="d-flex justify-content-between align-items-center px-2 py-1 rounded-2 mb-1"
                                    style="cursor:pointer; font-size:.85rem;"
                                    wire:click="addTag({{ $tag->id }})"
                                    onmouseover="this.style.background='var(--light,#f5f6fa)'"
                                    onmouseout="this.style.background='transparent'"
                                >
                                    <span class="mono">{{ $tag->name }}</span>
                                    <span class="badge bg-light text-secondary border" style="font-size:.7rem">
                                        {{ $tag->usage_count }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Image de couverture --}}
                    <div class="card-soft p-4 mb-3">
                        <label class="form-label fw-semibold mb-1" style="font-size:.9rem">
                            <i class="fa-regular fa-image me-1 text-muted-2"></i>
                            Image de couverture
                            <span class="text-muted-2 fw-normal d-block" style="font-size:.75rem; font-weight:400">
                                Optionnel · JPG, PNG · max. 2 Mo
                            </span>
                        </label>

                        @if ($coverImage)
                            <img src="{{ $coverImage->temporaryUrl() }}" alt="Aperçu"
                                 class="img-fluid rounded mb-2 w-100"
                                 style="max-height:110px; object-fit:cover;" />
                        @endif

                        <input
                            type="file"
                            wire:model="coverImage"
                            class="form-control form-control-sm @error('coverImage') is-invalid @enderror"
                            accept="image/*"
                        />
                        @error('coverImage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Actions --}}
                    <div class="d-grid gap-2">
                        <button wire:click="save" wire:loading.attr="disabled" class="btn btn-brand">
                            <span wire:loading wire:target="save">
                                <i class="fa-solid fa-circle-notch fa-spin me-1"></i>En cours…
                            </span>
                            <span wire:loading.remove wire:target="save">
                                <i class="fa-solid fa-floppy-disk me-1"></i>Sauvegarder en brouillon
                            </span>
                        </button>
                        <a href="{{ route('blog.index') }}" class="btn btn-ghost text-center">Annuler</a>
                    </div>

                </div>{{-- /col sidebar --}}
            </div>{{-- /row --}}
        </div>
    </div>
</div>
