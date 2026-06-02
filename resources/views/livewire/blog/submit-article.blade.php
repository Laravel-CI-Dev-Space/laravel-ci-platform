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
                            Input hidden : wire:model.live.debounce.500ms sur l'input caché
                            synchronise $body → Livewire quand JS dispatche un event 'input'.
                            wire:ignore : Livewire ne touche jamais le DOM d'EasyMDE.
                        --}}
                        {{--
                            wire:model="body" sans .live = deferred :
                            aucun re-render pendant la frappe.
                            Livewire inclura la valeur dans la requête du bouton Save.
                        --}}
                        <input type="hidden" id="mde-body-sync" wire:model="body" />
                        <div wire:ignore id="mde-wrapper" data-initial="{{ e($body) }}">
                            <textarea id="article-mde"></textarea>
                        </div>

                        {{-- ── Aide formatage ─────────────────────── --}}
                        <div x-data="{ open: false }" class="mt-2">
                            <button
                                type="button"
                                x-on:click="open = !open"
                                class="btn btn-ghost btn-sm d-flex align-items-center gap-1"
                                style="font-size:.8rem; color:var(--muted)"
                            >
                                <i class="fa-solid fa-circle-question text-orange"></i>
                                <span x-text="open ? 'Masquer l\'aide' : 'Aide — syntaxe Markdown & blocs de code'"></span>
                                <i class="fa-solid fa-chevron-down ms-1" style="font-size:.65rem; transition:.2s"
                                   :style="open ? 'transform:rotate(180deg)' : ''"></i>
                            </button>

                            <div x-show="open" x-transition class="md-help-panel p-3 mt-1">
                                <div class="row g-3">

                                    {{-- Blocs de code --}}
                                    <div class="col-md-6">
                                        <div class="fw-semibold mb-2" style="font-size:.82rem">
                                            <i class="fa-solid fa-terminal me-1 text-orange"></i>
                                            Blocs de code (cliquez <strong>⌨</strong> dans la barre)
                                        </div>
                                        @foreach ([
                                            ['``` ```php',         'PHP'],
                                            ['``` ```javascript',  'JavaScript'],
                                            ['``` ```html',        'HTML'],
                                            ['``` ```css',         'CSS'],
                                            ['``` ```sql',         'SQL'],
                                            ['``` ```bash',        'Bash / Shell'],
                                            ['``` ```json',        'JSON'],
                                            ['``` ```python',      'Python'],
                                        ] as [$syntax, $lang])
                                            <div class="syntax-row">
                                                <code>{{ $syntax }}</code>
                                                <span class="result">→ {{ $lang }}</span>
                                            </div>
                                        @endforeach
                                        <div class="mt-2 p-2 rounded" style="background:#0d1117; font-family:'JetBrains Mono',monospace; font-size:.75rem; color:#e6edf3;">
                                            <span style="color:#6e7681">```php</span><br>
                                            <span style="color:#7ee787">&lt;?php</span><br>
                                            <span style="color:#e6edf3">echo </span><span style="color:#a5d6ff">"Laravel CI"</span><span style="color:#e6edf3">;</span><br>
                                            <span style="color:#6e7681">```</span>
                                        </div>
                                    </div>

                                    {{-- Formatage texte --}}
                                    <div class="col-md-6">
                                        <div class="fw-semibold mb-2" style="font-size:.82rem">
                                            <i class="fa-brands fa-markdown me-1 text-orange"></i>
                                            Formatage de texte
                                        </div>
                                        @foreach ([
                                            ['**texte**',        'Gras'],
                                            ['*texte*',          'Italique'],
                                            ['~~texte~~',        'Barré'],
                                            ['# Titre 1',        'Titre H1'],
                                            ['## Titre 2',       'Titre H2'],
                                            ['> Citation',       'Blockquote'],
                                            ['`code inline`',    'Code inline'],
                                            ['[texte](url)',      'Lien'],
                                            ['- élément',        'Liste à puces'],
                                            ['1. élément',       'Liste numérotée'],
                                            ['| Col | Col |',    'Tableau'],
                                        ] as [$syntax, $desc])
                                            <div class="syntax-row">
                                                <code>{{ $syntax }}</code>
                                                <span class="result">{{ $desc }}</span>
                                            </div>
                                        @endforeach
                                    </div>

                                </div>

                                <div class="mt-2 pt-2" style="border-top:1px solid #d1d9e0; font-size:.75rem; color:#57606a">
                                    <i class="fa-solid fa-keyboard me-1"></i>
                                    <strong>Ctrl+B</strong> Gras &nbsp;·&nbsp;
                                    <strong>Ctrl+I</strong> Italique &nbsp;·&nbsp;
                                    <strong>Ctrl+K</strong> Lien &nbsp;·&nbsp;
                                    Bouton <strong>⌨</strong> → insérer un bloc de code avec sélection du langage
                                </div>
                            </div>
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
