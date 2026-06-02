@extends('layouts.web')

@section('title', 'Soumettre un article — Laravel CI')

{{-- ── Assets EasyMDE (uniquement sur cette page) ──────── --}}
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde@2.18.0/dist/easymde.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/highlight.js@11.9.0/styles/github-dark.min.css" />
<style>
/* ── EasyMDE : personnalisation ─────────────────── */
.EasyMDEContainer { border-radius: var(--radius,.75rem); overflow: hidden; }
.editor-toolbar  {
    border-radius: var(--radius,.75rem) var(--radius,.75rem) 0 0;
    background: #f8f9fa; border-color: #dee2e6;
    padding: .3rem .5rem; flex-wrap: wrap; gap: .1rem;
}
.editor-toolbar button { border-radius: 6px; color: #374151; }
.editor-toolbar button:hover,
.editor-toolbar button.active { background: #e9ecef; color: var(--orange,#e8590c); }
.editor-toolbar i.separator { border-color: #dee2e6; }
.CodeMirror {
    border: 1px solid #dee2e6; border-top: none;
    border-radius: 0 0 var(--radius,.75rem) var(--radius,.75rem);
    font-family: 'JetBrains Mono', 'Fira Code', monospace;
    font-size: .9rem; line-height: 1.7; min-height: 400px;
}
.CodeMirror-focused { border-color: var(--orange,#e8590c) !important; box-shadow: 0 0 0 .2rem rgba(232,89,12,.12); }
.editor-preview, .editor-preview-side { background:#fff; padding:1.5rem; }
.editor-preview pre, .editor-preview-side pre {
    background:#0d1117; border-radius:8px; overflow:hidden;
    border:1px solid #30363d; margin:1rem 0; position:relative;
}
.editor-preview pre code, .editor-preview-side pre code {
    display:block; padding:1rem 1.25rem;
    font-family:'JetBrains Mono',monospace; font-size:.875rem; background:transparent !important;
}
.editor-toolbar.fullscreen, .CodeMirror-fullscreen { z-index:9999; }

/* ── Modal de sélection de langage ─────────────── */
.lang-tile {
    display:flex; flex-direction:column; align-items:center; justify-content:center;
    gap:.35rem; padding:.75rem .5rem; border-radius:10px; cursor:pointer;
    border:2px solid transparent; transition:.15s; font-size:.78rem; font-weight:600;
    text-align:center; min-height:72px;
}
.lang-tile:hover { transform:translateY(-2px); box-shadow:0 4px 12px rgba(0,0,0,.15); }
.lang-tile .lang-logo { font-size:1.4rem; line-height:1; }
</style>
@endpush

@section('content')
    @livewire('blog.submit-article')

    {{-- ── Modal sélection de langage pour les blocs de code ── --}}
    <div class="modal fade" id="langPickerModal" tabindex="-1" aria-labelledby="langPickerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h6 class="modal-title fw-bold" id="langPickerModalLabel">
                        <i class="fa-solid fa-terminal me-2 text-orange"></i>
                        Insérer un bloc de code — Choisir le langage
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="row g-2">
                        @php
                        $langs = [
                            ['php',        'PHP',        '#8892BF', '#f0f0ff', '🐘'],
                            ['javascript', 'JavaScript', '#F7DF1E', '#fffde7', '𝐉𝐒'],
                            ['typescript', 'TypeScript', '#007ACC', '#e3f2fd', '𝐓𝐒'],
                            ['html',       'HTML',       '#E34F26', '#fff3e0', '</\>'],
                            ['css',        'CSS',        '#1572B6', '#e8f4fd', '🎨'],
                            ['blade',      'Blade',      '#FF2D20', '#fff0ef', '⚡'],
                            ['sql',        'SQL',        '#F29111', '#fff8e1', '🗄'],
                            ['bash',       'Bash/Shell', '#4EAA25', '#f0fff0', '$_'],
                            ['python',     'Python',     '#3776AB', '#e3f2fd', '🐍'],
                            ['json',       'JSON',       '#6C757D', '#f8f9fa', '{}'],
                            ['yaml',       'YAML',       '#CB171E', '#fff0ef', '📋'],
                            ['vue',        'Vue',        '#42B883', '#e8f8f0', '💚'],
                            ['text',       'Texte brut', '#6C757D', '#f8f9fa', '📄'],
                        ];
                        @endphp

                        @foreach ($langs as [$code, $label, $color, $bg, $icon])
                            <div class="col-4 col-sm-3 col-md-2">
                                <div
                                    class="lang-tile"
                                    style="background:{{ $bg }}; border-color:{{ $bg }};"
                                    onclick="pickLang('{{ $code }}')"
                                    onmouseover="this.style.borderColor='{{ $color }}'"
                                    onmouseout="this.style.borderColor='{{ $bg }}'"
                                >
                                    <span class="lang-logo" style="color:{{ $color }}">{{ $icon }}</span>
                                    <span style="color:{{ $color }}">{{ $label }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <p class="text-muted mb-0" style="font-size:.8rem">
                        <i class="fa-solid fa-lightbulb me-1 text-orange"></i>
                        Sélectionnez du texte avant de cliquer pour l'envelopper dans le bloc.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/highlight.js@11.9.0/highlight.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/easymde@2.18.0/dist/easymde.min.js"></script>
<script>
/*
 * Composant Alpine "easyMdeEditor" enregistré avant le démarrage d'Alpine.
 * L'événement 'alpine:init' est émis par Alpine juste avant le parcours du DOM,
 * tous les scripts ci-dessus sont déjà exécutés à ce moment.
 */
document.addEventListener('alpine:init', function () {
    Alpine.data('easyMdeEditor', function () {
        return {
            editor: null,

            init: function () {
                var self = this;

                self.editor = new EasyMDE({
                    element: self.$refs.mdeBody,
                    initialValue: self.$el.dataset.initial || '',
                    spellChecker: false,
                    autosave: { enabled: false },
                    placeholder: 'Rédigez votre article ici en Markdown…\n\nPour un bloc de code, cliquez sur le bouton ⌨ ou utilisez la syntaxe :\n```php\n<?php\n\necho "Hello Laravel CI !";\n```',
                    toolbar: [
                        { name: 'bold',           action: EasyMDE.toggleBold,          className: 'fa fa-bold',            title: 'Gras (Ctrl+B)' },
                        { name: 'italic',         action: EasyMDE.toggleItalic,        className: 'fa fa-italic',          title: 'Italique (Ctrl+I)' },
                        { name: 'strikethrough',  action: EasyMDE.toggleStrikethrough, className: 'fa fa-strikethrough',   title: 'Barré' },
                        '|',
                        { name: 'heading-1', action: EasyMDE.toggleHeading1, className: 'fa fa-header fa-header-x fa-header-1', title: 'Titre H1' },
                        { name: 'heading-2', action: EasyMDE.toggleHeading2, className: 'fa fa-header fa-header-x fa-header-2', title: 'Titre H2' },
                        { name: 'heading-3', action: EasyMDE.toggleHeading3, className: 'fa fa-header fa-header-x fa-header-3', title: 'Titre H3' },
                        '|',
                        {
                            name:      'insert-code',
                            className: 'fa fa-terminal',
                            title:     'Insérer un bloc de code (choisir le langage)',
                            action:    function (editor) {
                                /* Stocke l'instance pour pickLang() */
                                window._easyMde = editor;
                                new bootstrap.Modal(document.getElementById('langPickerModal')).show();
                            }
                        },
                        { name: 'quote',          action: EasyMDE.toggleBlockquote,    className: 'fa fa-quote-left',   title: 'Citation' },
                        '|',
                        { name: 'unordered-list', action: EasyMDE.toggleUnorderedList, className: 'fa fa-list-ul',      title: 'Liste à puces' },
                        { name: 'ordered-list',   action: EasyMDE.toggleOrderedList,   className: 'fa fa-list-ol',      title: 'Liste numérotée' },
                        '|',
                        { name: 'link',  action: EasyMDE.drawLink,  className: 'fa fa-link',  title: 'Lien (Ctrl+K)' },
                        { name: 'table', action: EasyMDE.drawTable, className: 'fa fa-table', title: 'Tableau' },
                        '|',
                        { name: 'preview',      action: EasyMDE.togglePreview,     className: 'fa fa-eye no-disable',                  title: 'Prévisualiser' },
                        { name: 'side-by-side', action: EasyMDE.toggleSideBySide,  className: 'fa fa-columns no-disable no-mobile',    title: 'Côte à côte' },
                        { name: 'fullscreen',   action: EasyMDE.toggleFullScreen,  className: 'fa fa-arrows-alt no-disable no-mobile', title: 'Plein écran' },
                        '|',
                        { name: 'guide', action: 'https://commonmark.org/help/', className: 'fa fa-question-circle', title: 'Guide Markdown' }
                    ],
                    renderingConfig: {
                        codeSyntaxHighlighting: true,
                        hljs: window.hljs
                    },
                    previewClass: ['editor-preview', 'article-body'],
                    status: [
                        'lines', 'words',
                        {
                            className: 'char-count',
                            defaultValue: function (el) { el.innerHTML = '0 car.'; },
                            onUpdate:     function (el, cm) { el.innerHTML = cm.getValue().length + ' car.'; }
                        }
                    ]
                });

                /* Synchronise EasyMDE → propriété Livewire à chaque modification */
                self.editor.codemirror.on('change', function () {
                    self.$wire.set('body', self.editor.value());
                });

                /* Référence globale pour pickLang() */
                window._easyMde = self.editor;
            }
        };
    });
});

/**
 * Insère un bloc de code dans l'éditeur pour le langage choisi.
 * Si du texte est sélectionné, il est enveloppé dans le bloc.
 */
function pickLang(lang) {
    var modal = bootstrap.Modal.getInstance(document.getElementById('langPickerModal'));
    if (modal) modal.hide();

    var editor = window._easyMde;
    if (!editor) return;

    var cm      = editor.codemirror;
    var sel     = cm.getSelection();
    var snippet = sel.length > 0 ? sel : 'votre code ici';
    var block   = '```' + lang + '\n' + snippet + '\n```';

    cm.replaceSelection(block);

    /* Si aucune sélection, place le curseur sur la ligne de code */
    if (sel.length === 0) {
        var cur = cm.getCursor();
        /* cursor est maintenant après le ``` final, remonter 2 lignes */
        var targetLine = cur.line - 1;
        cm.setSelection(
            { line: targetLine, ch: 0 },
            { line: targetLine, ch: snippet.length }
        );
    }

    cm.focus();
}
</script>
@endpush
