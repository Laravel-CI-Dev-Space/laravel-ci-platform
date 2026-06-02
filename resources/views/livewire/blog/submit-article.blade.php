@push('styles')
{{-- EasyMDE : éditeur Markdown avec barre d'outils --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde@2.18.0/dist/easymde.min.css" />
{{-- highlight.js : coloration dans la prévisualisation --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/highlight.js@11.9.0/styles/github-dark.min.css" />
<style>
/* ── Personnalisation EasyMDE ───────────────────── */
.EasyMDEContainer { border-radius: var(--radius, .75rem); overflow: hidden; }
.editor-toolbar  { border-radius: var(--radius, .75rem) var(--radius, .75rem) 0 0; background: #f8f9fa; border-color: #dee2e6; padding: .35rem .5rem; flex-wrap: wrap; gap: .15rem; }
.editor-toolbar button { border-radius: 6px; color: #374151; }
.editor-toolbar button:hover, .editor-toolbar button.active { background: #e9ecef; color: var(--orange, #e8590c); }
.editor-toolbar i.separator { border-color: #dee2e6; }
.CodeMirror { border: 1px solid #dee2e6; border-top: none; border-radius: 0 0 var(--radius, .75rem) var(--radius, .75rem); font-family: 'JetBrains Mono', 'Fira Code', monospace; font-size: .9rem; line-height: 1.7; min-height: 380px; color: #1a1a2e; }
.CodeMirror-focused { border-color: var(--orange, #e8590c) !important; box-shadow: 0 0 0 .2rem rgba(232, 89, 12, .12); }
.editor-preview, .editor-preview-side { background: #fff; padding: 1.5rem; font-family: inherit; }
/* Blocs de code dans la prévisualisation EasyMDE */
.editor-preview pre, .editor-preview-side pre {
    background: #0d1117; border-radius: 8px; padding: 0; overflow: hidden;
    border: 1px solid #30363d; margin: 1rem 0;
}
.editor-preview pre::before, .editor-preview-side pre::before {
    content: ''; display: block; height: 2.2rem; background: #161b22; border-bottom: 1px solid #30363d;
}
.editor-preview pre::after, .editor-preview-side pre::after {
    content: '⬤  ⬤  ⬤'; position: absolute; font-size: .55rem; letter-spacing: .35rem;
    color: transparent; text-shadow: 0 0 0 #ff5f57,1.1rem 0 0 #ffbd2e,2.2rem 0 0 #28ca41;
    top: .42rem; left: .9rem;
}
.editor-preview pre code, .editor-preview-side pre code { display: block; padding: 1rem 1.25rem; font-family: 'JetBrains Mono', monospace; font-size: .875rem; }
.editor-toolbar.fullscreen, .CodeMirror-fullscreen { z-index: 9999; }
</style>
@endpush

<div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <section class="section-sm">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0" style="font-size:var(--fs-h3)">Rédiger un article</h2>
                        <a href="{{ route('blog.index') }}" class="btn btn-ghost btn-sm">
                            <i class="fa-solid fa-arrow-left me-1"></i> Retour au blog
                        </a>
                    </div>

                    <div class="card-soft p-4 p-lg-5">

                        {{-- Titre --}}
                        <div class="mb-4">
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
                            <div class="text-muted-2 mt-1" style="font-size:.82rem">
                                {{ strlen($title) }} / 300 caractères
                            </div>
                        </div>

                        {{-- Extrait --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Extrait
                                <span class="text-muted-2 fw-normal">(optionnel — résumé affiché sur la liste)</span>
                            </label>
                            <textarea
                                wire:model.live="excerpt"
                                class="form-control @error('excerpt') is-invalid @enderror"
                                rows="2"
                                placeholder="Une accroche courte pour donner envie de lire…"
                                maxlength="500"
                            ></textarea>
                            @error('excerpt')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- ===== ÉDITEUR MARKDOWN ===== --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Contenu <span class="text-danger">*</span>
                                <span class="text-muted-2 fw-normal">(min. 100 car.) — Markdown + blocs de code supportés</span>
                            </label>

                            @error('body')
                                <div class="text-danger mb-2" style="font-size:.875rem">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i>{{ $message }}
                                </div>
                            @enderror

                            {{--
                                wire:ignore empêche Livewire de ré-injecter le DOM pendant que l'utilisateur tape.
                                Alpine bridge synchronise EasyMDE → propriété Livewire via $wire.set().
                            --}}
                            <div
                                x-data="{
                                    editor: null,
                                    init() {
                                        this.editor = new EasyMDE({
                                            element: this.$refs.mdeBody,
                                            spellChecker: false,
                                            autosave: { enabled: false },
                                            placeholder: 'Rédigez ici en Markdown…\n\nExemple bloc de code :\n```php\n<?php\necho \"Hello Laravel CI\";\n```',
                                            initialValue: @json($body ?? ''),
                                            toolbar: [
                                                { name: 'bold',           action: EasyMDE.toggleBold,           className: 'fa fa-bold',         title: 'Gras (Ctrl+B)' },
                                                { name: 'italic',         action: EasyMDE.toggleItalic,         className: 'fa fa-italic',       title: 'Italique (Ctrl+I)' },
                                                { name: 'strikethrough',  action: EasyMDE.toggleStrikethrough,  className: 'fa fa-strikethrough', title: 'Barré' },
                                                '|',
                                                { name: 'heading-1',      action: EasyMDE.toggleHeading1,       className: 'fa fa-header fa-header-x fa-header-1', title: 'Titre H1' },
                                                { name: 'heading-2',      action: EasyMDE.toggleHeading2,       className: 'fa fa-header fa-header-x fa-header-2', title: 'Titre H2' },
                                                { name: 'heading-3',      action: EasyMDE.toggleHeading3,       className: 'fa fa-header fa-header-x fa-header-3', title: 'Titre H3' },
                                                '|',
                                                { name: 'code',           action: EasyMDE.toggleCodeBlock,      className: 'fa fa-code',         title: 'Bloc de code' },
                                                { name: 'quote',          action: EasyMDE.toggleBlockquote,     className: 'fa fa-quote-left',   title: 'Citation' },
                                                '|',
                                                { name: 'unordered-list', action: EasyMDE.toggleUnorderedList,  className: 'fa fa-list-ul',      title: 'Liste à puces' },
                                                { name: 'ordered-list',   action: EasyMDE.toggleOrderedList,    className: 'fa fa-list-ol',      title: 'Liste numérotée' },
                                                '|',
                                                { name: 'link',           action: EasyMDE.drawLink,             className: 'fa fa-link',         title: 'Lien (Ctrl+K)' },
                                                { name: 'table',          action: EasyMDE.drawTable,            className: 'fa fa-table',        title: 'Tableau' },
                                                '|',
                                                { name: 'preview',        action: EasyMDE.togglePreview,        className: 'fa fa-eye no-disable',                       title: 'Prévisualiser' },
                                                { name: 'side-by-side',   action: EasyMDE.toggleSideBySide,     className: 'fa fa-columns no-disable no-mobile',         title: 'Côte à côte' },
                                                { name: 'fullscreen',     action: EasyMDE.toggleFullScreen,     className: 'fa fa-arrows-alt no-disable no-mobile',      title: 'Plein écran' },
                                                '|',
                                                { name: 'guide', action: 'https://commonmark.org/help/', className: 'fa fa-question-circle', title: 'Guide Markdown' },
                                            ],
                                            renderingConfig: {
                                                codeSyntaxHighlighting: true,
                                                hljs: window.hljs,
                                            },
                                            previewClass: ['editor-preview', 'article-body'],
                                            status: [
                                                'lines',
                                                'words',
                                                {
                                                    className: 'char-count',
                                                    defaultValue: (el) => { el.innerHTML = '0 car.'; },
                                                    onUpdate: (el, cm) => { el.innerHTML = cm.getValue().length + ' car.'; },
                                                },
                                            ],
                                        });

                                        // Synchronise EasyMDE → propriété Livewire à chaque frappe
                                        this.editor.codemirror.on('change', () => {
                                            $wire.set('body', this.editor.value());
                                        });
                                    }
                                }"
                                x-init="init()"
                                wire:ignore
                            >
                                <textarea x-ref="mdeBody" class="form-control"></textarea>
                            </div>
                        </div>

                        {{-- Niveau --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Niveau <span class="text-danger">*</span>
                            </label>
                            <div class="filter-pills">
                                @foreach (['beginner' => 'Débutant', 'intermediate' => 'Intermédiaire', 'advanced' => 'Avancé'] as $value => $label)
                                    <button
                                        type="button"
                                        wire:click="$set('level', '{{ $value }}')"
                                        class="filter-pill {{ $level === $value ? 'active' : '' }}"
                                    >
                                        {{ $label }}
                                    </button>
                                @endforeach
                            </div>
                            @error('level')
                                <div class="text-danger mt-1" style="font-size:.875rem">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tags --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Tags <span class="text-danger">*</span>
                                <span class="text-muted-2 fw-normal">(1–5 tags)</span>
                            </label>

                            @error('selectedTags')
                                <div class="text-danger mb-2" style="font-size:.875rem">{{ $message }}</div>
                            @enderror

                            @if (count($selectedTags) > 0)
                                <div class="q-tags mb-2">
                                    @foreach ($tags->whereIn('id', $selectedTags) as $tag)
                                        <span class="tag" style="cursor:pointer" wire:click="removeTag({{ $tag->id }})">
                                            {{ $tag->name }}&nbsp;<i class="fa-solid fa-xmark"></i>
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="tag-list">
                                @foreach ($tags->whereNotIn('id', $selectedTags) as $tag)
                                    <div class="tag-list-item" style="cursor:pointer" wire:click="addTag({{ $tag->id }})">
                                        <span class="mono">{{ $tag->name }}</span>
                                        <span class="count">{{ number_format($tag->usage_count) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Image de couverture --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Image de couverture
                                <span class="text-muted-2 fw-normal">(optionnel, max. 2 Mo)</span>
                            </label>
                            <input
                                type="file"
                                wire:model="coverImage"
                                class="form-control @error('coverImage') is-invalid @enderror"
                                accept="image/*"
                            />
                            @error('coverImage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if ($coverImage)
                                <div class="mt-2">
                                    <img src="{{ $coverImage->temporaryUrl() }}" alt="Aperçu" style="max-height:150px; border-radius:var(--radius);" />
                                </div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex justify-content-end gap-3 pt-2">
                            <a href="{{ route('blog.index') }}" class="btn btn-ghost">Annuler</a>
                            <button wire:click="save" wire:loading.attr="disabled" class="btn btn-brand">
                                <span wire:loading wire:target="save">
                                    <i class="fa-solid fa-circle-notch fa-spin me-1"></i>
                                </span>
                                <span wire:loading.remove wire:target="save">
                                    <i class="fa-solid fa-floppy-disk me-1"></i>
                                </span>
                                Sauvegarder en brouillon
                            </button>
                        </div>

                    </div>{{-- /card-soft --}}
                </div>
            </div>
        </div>
    </section>
</div>

@push('scripts')
{{-- highlight.js pour la coloration dans la prévisualisation EasyMDE --}}
<script src="https://cdn.jsdelivr.net/npm/highlight.js@11.9.0/highlight.min.js"></script>
{{-- EasyMDE --}}
<script src="https://cdn.jsdelivr.net/npm/easymde@2.18.0/dist/easymde.min.js"></script>
@endpush
