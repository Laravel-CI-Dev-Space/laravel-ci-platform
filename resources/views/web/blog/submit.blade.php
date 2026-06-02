@extends('layouts.web')

@section('title', 'Soumettre un article — Laravel CI')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde@2.18.0/dist/easymde.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/highlight.js@11.9.0/styles/github-dark.min.css" />
<style>
/* ── EasyMDE ──────────────────────────────────── */
.EasyMDEContainer { border-radius: var(--radius,.75rem); overflow: hidden; }
.editor-toolbar  { border-radius: var(--radius,.75rem) var(--radius,.75rem) 0 0; background:#f8f9fa; border-color:#dee2e6; padding:.3rem .5rem; flex-wrap:wrap; gap:.1rem; }
.editor-toolbar button { border-radius:6px; color:#374151; }
.editor-toolbar button:hover,.editor-toolbar button.active { background:#e9ecef; color:var(--orange,#e8590c); }
.editor-toolbar i.separator { border-color:#dee2e6; }
.CodeMirror { border:1px solid #dee2e6; border-top:none; border-radius:0 0 var(--radius,.75rem) var(--radius,.75rem); font-family:'JetBrains Mono','Fira Code',monospace; font-size:.9rem; line-height:1.7; min-height:400px; }
.CodeMirror-focused { border-color:var(--orange,#e8590c) !important; box-shadow:0 0 0 .2rem rgba(232,89,12,.12); }
.editor-preview,.editor-preview-side { background:#fff; padding:1.5rem; }
.editor-preview pre,.editor-preview-side pre { background:#0d1117; border-radius:8px; overflow:hidden; border:1px solid #30363d; margin:1rem 0; position:relative; }
.editor-preview pre code,.editor-preview-side pre code { display:block; padding:1rem 1.25rem; font-family:'JetBrains Mono',monospace; font-size:.875rem; background:transparent !important; }
.editor-toolbar.fullscreen,.CodeMirror-fullscreen { z-index:9000; }

/* ── Modal sélection de langage ───────────────── */
.lang-tile { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.4rem; padding:.85rem .4rem; border-radius:10px; cursor:pointer; border:2px solid transparent; transition:.15s; text-align:center; min-height:80px; }
.lang-tile:hover { transform:translateY(-2px); box-shadow:0 4px 14px rgba(0,0,0,.15); }
.lang-tile .ti { font-size:1.55rem; line-height:1; }
.lang-tile .lt  { font-size:.72rem; font-weight:700; letter-spacing:.02em; }

/* ── Code editor textarea ─────────────────────── */
#codeEditor {
    background: #0d1117 !important;
    color: #e6edf3 !important;
    -webkit-text-fill-color: #e6edf3 !important;
    caret-color: #e6edf3;
}
#codeEditor::placeholder { color: #6e7681 !important; -webkit-text-fill-color: #6e7681 !important; }

/* ── EasyMDE : code blocks plus visibles ─────── */
.CodeMirror .cm-comment {
    font-family: 'JetBrains Mono', 'Fira Code', monospace !important;
    background: rgba(110, 118, 129, .08);
    border-radius: 3px;
    color: #0550ae;
}
.CodeMirror-line .cm-string { color: #0a3069; }
.CodeMirror .cm-formatting-code,
.CodeMirror .cm-formatting-code-block { color: var(--orange, #e8590c); font-weight: 600; }

/* ── Panneau d'aide formatage ────────────────── */
.md-help-panel { background: #f6f8fa; border: 1px solid #d1d9e0; border-radius: 8px; }
.md-help-panel .syntax-row { display: flex; gap: .75rem; align-items: flex-start; padding: .35rem 0; border-bottom: 1px solid #e8ebee; font-size: .8rem; }
.md-help-panel .syntax-row:last-child { border-bottom: none; }
.md-help-panel code { background: #e8ebee; padding: .1em .35em; border-radius: 4px; font-size: .78rem; font-family: 'JetBrains Mono', monospace; color: #0550ae; white-space: nowrap; }
.md-help-panel .result { color: #57606a; flex-shrink: 0; width: 130px; }
</style>
@endpush

@section('content')
    @livewire('blog.submit-article')

    {{-- ════════════════════════════════════════════
         Modal — Sélection de langage + Éditeur Monaco
         ════════════════════════════════════════════ --}}
    <div class="modal fade" id="langPickerModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg overflow-hidden">

                {{-- ── Étape 1 : choix du langage ── --}}
                <div id="lpStep1">
                    <div class="modal-header border-0 pb-1">
                        <h6 class="modal-title fw-bold">
                            <i class="fa-solid fa-terminal me-2 text-orange"></i>
                            Insérer un bloc de code — Choisir le langage
                        </h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-2">
                            @php
                            $langs = [
                                ['php',        'PHP',        '#8892BF', '#f0f0ff', 'fa-brands fa-php'],
                                ['javascript', 'JavaScript', '#e8a000', '#fffde7', 'fa-brands fa-js'],
                                ['typescript', 'TypeScript', '#007ACC', '#e3f2fd', 'fa-solid fa-code'],
                                ['html',       'HTML',       '#E34F26', '#fff3e0', 'fa-brands fa-html5'],
                                ['css',        'CSS',        '#1572B6', '#e8f4fd', 'fa-brands fa-css3-alt'],
                                ['blade',      'Blade',      '#FF2D20', '#fff0ef', 'fa-brands fa-laravel'],
                                ['vue',        'Vue',        '#42B883', '#e8f8f0', 'fa-brands fa-vuejs'],
                                ['python',     'Python',     '#3776AB', '#e3f2fd', 'fa-brands fa-python'],
                                ['sql',        'SQL',        '#F29111', '#fff8e1', 'fa-solid fa-database'],
                                ['bash',       'Bash / Shell','#4EAA25','#f0fff0', 'fa-solid fa-terminal'],
                                ['json',       'JSON',       '#5c6370', '#f8f9fa', 'fa-solid fa-braces'],
                                ['yaml',       'YAML',       '#CB171E', '#fff0ef', 'fa-solid fa-file-code'],
                                ['text',       'Texte brut', '#6C757D', '#f8f9fa', 'fa-solid fa-file-lines'],
                            ];
                            @endphp

                            @foreach ($langs as [$code, $label, $color, $bg, $icon])
                                <div class="col-4 col-sm-3 col-md-2">
                                    <div class="lang-tile"
                                         style="background:{{ $bg }}; border-color:{{ $bg }};"
                                         onclick="lpSelectLang('{{ $code }}')"
                                         onmouseover="this.style.borderColor='{{ $color }}'"
                                         onmouseout="this.style.borderColor='{{ $bg }}'">
                                        <i class="{{ $icon }} ti" style="color:{{ $color }}"></i>
                                        <span class="lt" style="color:{{ $color }}">{{ $label }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <p class="text-muted mb-0" style="font-size:.78rem">
                            <i class="fa-solid fa-lightbulb me-1 text-orange"></i>
                            Sélectionnez du texte dans l'éditeur avant de cliquer pour l'envelopper automatiquement.
                        </p>
                    </div>
                </div>

                {{-- ── Étape 2 : éditeur de code (textarea sombre) ── --}}
                <div id="lpStep2" style="display:none">
                    <div class="modal-header border-0 pb-1 d-flex align-items-center gap-2"
                         style="background:#161b22; border-radius:.5rem .5rem 0 0;">
                        <button style="background:none;border:none;cursor:pointer;color:#aaa;padding:0"
                                onclick="lpReset()" title="Retour">
                            <i class="fa-solid fa-arrow-left"></i>
                        </button>
                        <h6 class="modal-title fw-bold mb-0 text-white">
                            <i id="lpLangIcon" class="me-1"></i>
                            <span id="lpLangLabel"></span>
                            <span class="text-muted fw-normal" style="font-size:.8rem"> — éditeur de code</span>
                        </h6>
                        <div class="ms-auto d-flex gap-2 align-items-center">
                            <span class="text-muted" style="font-size:.72rem">Tab = 4 espaces</span>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    onclick="lpReset()"></button>
                        </div>
                    </div>
                    {{-- Faux terminal header --}}
                    <div style="background:#1a1b26; padding:.4rem 1rem; border-bottom:1px solid #30363d; display:flex; align-items:center; gap:.4rem;">
                        <span style="width:12px;height:12px;border-radius:50%;background:#ff5f57;display:inline-block"></span>
                        <span style="width:12px;height:12px;border-radius:50%;background:#ffbd2e;display:inline-block"></span>
                        <span style="width:12px;height:12px;border-radius:50%;background:#28ca41;display:inline-block"></span>
                        <span id="lpLangTab" class="ms-2 text-muted" style="font-size:.75rem;font-family:'JetBrains Mono',monospace"></span>
                    </div>
                    <div style="background:#0d1117; padding:0;">
                        <textarea
                            id="codeEditor"
                            spellcheck="false"
                            autocomplete="off"
                            autocorrect="off"
                            autocapitalize="off"
                            placeholder="// Saisissez votre code ici…"
                            style="
                                width:100%; height:320px;
                                background:#0d1117; color:#e6edf3;
                                font-family:'JetBrains Mono','Fira Code',Consolas,monospace;
                                font-size:.9rem; line-height:1.75;
                                padding:1rem 1.25rem; border:none;
                                resize:none; outline:none; tab-size:4;
                                display:block;
                            "
                        ></textarea>
                    </div>
                    <div class="modal-footer border-0 gap-2" style="background:#161b22; border-radius:0 0 .5rem .5rem">
                        <button class="btn btn-ghost btn-sm text-muted" onclick="lpReset()">
                            <i class="fa-solid fa-arrow-left me-1"></i>Changer de langage
                        </button>
                        <button class="btn btn-brand btn-sm ms-auto" onclick="lpInsert()">
                            <i class="fa-solid fa-circle-plus me-1"></i>Insérer dans l'article
                        </button>
                    </div>
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
 * EasyMDE — initialisation JS pure (sans Alpine, sans Monaco).
 * Pourquoi sans Monaco : son AMD loader (loader.js) entre en conflit
 * avec le module AMD nprogress bundlé par Livewire → Uncaught Error.
 * Le code editor est remplacé par une <textarea> sombre monospace.
 */

var _easyMde      = null;
var _selectedLang = 'text';

/* ── Initialise EasyMDE ──────────────────────── */
function initEasyMDE() {
    var textarea = document.getElementById('article-mde');
    if (!textarea || typeof EasyMDE === 'undefined' || textarea._mdeInit) return;
    textarea._mdeInit = true;

    var wrapper = document.getElementById('mde-wrapper');
    var initial = (wrapper && wrapper.dataset.initial) ? wrapper.dataset.initial : '';

    _easyMde = new EasyMDE({
        element: textarea,
        initialValue: initial,
        spellChecker: false,
        autosave: { enabled: false },
        placeholder: 'Rédigez votre article en Markdown…\n\nUtilisez ⌨ dans la barre pour ouvrir l\'éditeur de code Monaco.',
        toolbar: [
            { name: 'bold',           action: EasyMDE.toggleBold,          className: 'fa fa-bold',           title: 'Gras (Ctrl+B)' },
            { name: 'italic',         action: EasyMDE.toggleItalic,        className: 'fa fa-italic',         title: 'Italique (Ctrl+I)' },
            { name: 'strikethrough',  action: EasyMDE.toggleStrikethrough, className: 'fa fa-strikethrough',  title: 'Barré' },
            '|',
            { name: 'h1', action: EasyMDE.toggleHeading1, className: 'fa fa-header fa-header-x fa-header-1', title: 'H1' },
            { name: 'h2', action: EasyMDE.toggleHeading2, className: 'fa fa-header fa-header-x fa-header-2', title: 'H2' },
            { name: 'h3', action: EasyMDE.toggleHeading3, className: 'fa fa-header fa-header-x fa-header-3', title: 'H3' },
            '|',
            {
                name: 'insert-code',
                className: 'fa fa-terminal',
                title: 'Insérer un bloc de code (Monaco)',
                action: function (ed) {
                    _easyMde = ed;
                    window._easyMde = ed;
                    new bootstrap.Modal(document.getElementById('langPickerModal')).show();
                }
            },
            { name: 'quote',   action: EasyMDE.toggleBlockquote,   className: 'fa fa-quote-left', title: 'Citation' },
            '|',
            { name: 'ul',      action: EasyMDE.toggleUnorderedList, className: 'fa fa-list-ul',   title: 'Liste à puces' },
            { name: 'ol',      action: EasyMDE.toggleOrderedList,   className: 'fa fa-list-ol',   title: 'Liste numérotée' },
            '|',
            { name: 'link',    action: EasyMDE.drawLink,   className: 'fa fa-link',  title: 'Lien (Ctrl+K)' },
            { name: 'table',   action: EasyMDE.drawTable,  className: 'fa fa-table', title: 'Tableau' },
            '|',
            { name: 'preview',      action: EasyMDE.togglePreview,    className: 'fa fa-eye no-disable',                 title: 'Prévisualiser' },
            { name: 'side-by-side', action: EasyMDE.toggleSideBySide, className: 'fa fa-columns no-disable no-mobile',   title: 'Côte à côte' },
            { name: 'fullscreen',   action: EasyMDE.toggleFullScreen, className: 'fa fa-arrows-alt no-disable no-mobile',title: 'Plein écran' },
            '|',
            { name: 'guide', action: 'https://commonmark.org/help/', className: 'fa fa-question-circle', title: 'Guide Markdown' }
        ],
        renderingConfig: { codeSyntaxHighlighting: true, hljs: window.hljs },
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

    window._easyMde = _easyMde;

    /* Synchronise EasyMDE → input caché → Livewire (wire:model.live.debounce.500ms) */
    _easyMde.codemirror.on('change', function () {
        var hidden = document.getElementById('mde-body-sync');
        if (hidden) {
            hidden.value = _easyMde.value();
            hidden.dispatchEvent(new Event('input', { bubbles: true }));
        }
    });

    /* Rattache le modal bootstrap */
    var modalEl = document.getElementById('langPickerModal');
    if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', function () { lpReset(); });
    }
}

/*
 * On attend 400 ms après le chargement des scripts pour laisser
 * Livewire terminer son hydratation initiale du DOM.
 * Sans ce délai, Livewire remet en place le textarea brut
 * et détruit EasyMDE juste après son init.
 */
setTimeout(initEasyMDE, 400);

/* Livewire SPA navigation */
document.addEventListener('livewire:navigated', function () {
    _easyMde = null;
    var ta = document.getElementById('article-mde');
    if (ta) delete ta._mdeInit;
    setTimeout(initEasyMDE, 400);
});

/* ══════════════════════════════════════════════════
   Modal sélection de langage + textarea code editor
   ══════════════════════════════════════════════════ */
var langMeta = {
    php:        { label:'PHP',         icon:'fa-brands fa-php',       color:'#8892BF' },
    javascript: { label:'JavaScript',  icon:'fa-brands fa-js',        color:'#e8a000' },
    typescript: { label:'TypeScript',  icon:'fa-solid fa-code',       color:'#007ACC' },
    html:       { label:'HTML',        icon:'fa-brands fa-html5',     color:'#E34F26' },
    css:        { label:'CSS',         icon:'fa-brands fa-css3-alt',  color:'#1572B6' },
    blade:      { label:'Blade',       icon:'fa-brands fa-laravel',   color:'#FF2D20' },
    vue:        { label:'Vue',         icon:'fa-brands fa-vuejs',     color:'#42B883' },
    python:     { label:'Python',      icon:'fa-brands fa-python',    color:'#3776AB' },
    sql:        { label:'SQL',         icon:'fa-solid fa-database',   color:'#F29111' },
    bash:       { label:'Bash/Shell',  icon:'fa-solid fa-terminal',   color:'#4EAA25' },
    json:       { label:'JSON',        icon:'fa-solid fa-braces',     color:'#5c6370' },
    yaml:       { label:'YAML',        icon:'fa-solid fa-file-code',  color:'#CB171E' },
    text:       { label:'Texte brut',  icon:'fa-solid fa-file-lines', color:'#6C757D' }
};

/* Étape 1 → 2 : sélection du langage */
function lpSelectLang(lang) {
    _selectedLang = lang;
    var m = langMeta[lang] || { label: lang, icon: 'fa-solid fa-code', color: '#6c757d' };

    document.getElementById('lpLangIcon').className   = m.icon + ' me-1';
    document.getElementById('lpLangIcon').style.color  = m.color;
    document.getElementById('lpLangLabel').textContent = m.label;
    document.getElementById('lpLangTab').textContent   = lang + '.txt';

    document.getElementById('lpStep1').style.display = 'none';
    document.getElementById('lpStep2').style.display = 'block';

    var ta = document.getElementById('codeEditor');
    if (ta) {
        ta.value = '';
        ta.placeholder = lpPlaceholder(lang);
        setTimeout(function () { ta.focus(); }, 80);
    }
}

/* Placeholder contextuel par langage */
function lpPlaceholder(lang) {
    var ph = {
        php:        '<?php\n\n// Votre code PHP ici\n',
        javascript: '// Votre code JavaScript ici\n',
        typescript: '// Votre code TypeScript ici\n',
        html:       '<!-- Votre HTML ici -->\n',
        css:        '/* Votre CSS ici */\n',
        blade:      '{{-- Votre template Blade ici --}}\n',
        sql:        '-- Votre requête SQL ici\nSELECT * FROM table;\n',
        bash:       '#!/bin/bash\n# Votre script shell ici\n',
        python:     '# Votre code Python ici\n',
        json:       '{\n  "key": "value"\n}\n',
        yaml:       '# Votre YAML ici\nkey: value\n',
        vue:        '<template>\n  <!-- Votre template Vue -->\n</template>\n',
    };
    return ph[lang] || '// Votre code ici\n';
}

/* Tab → 4 espaces dans la textarea */
document.addEventListener('keydown', function (e) {
    if (e.key === 'Tab' && e.target && e.target.id === 'codeEditor') {
        e.preventDefault();
        var ta = e.target;
        var s = ta.selectionStart;
        var end = ta.selectionEnd;
        ta.value = ta.value.substring(0, s) + '    ' + ta.value.substring(end);
        ta.selectionStart = ta.selectionEnd = s + 4;
    }
});

/* Retour à l'étape 1 */
function lpReset() {
    document.getElementById('lpStep1').style.display = 'block';
    document.getElementById('lpStep2').style.display = 'none';
}

/* Insérer le code dans EasyMDE */
function lpInsert() {
    var ta   = document.getElementById('codeEditor');
    var code = ta ? ta.value : '';
    if (!_easyMde) return;

    var block = '```' + _selectedLang + '\n' + code + '\n```';
    _easyMde.codemirror.replaceSelection(block);

    if (!code.trim()) {
        var cur = _easyMde.codemirror.getCursor();
        _easyMde.codemirror.setCursor({ line: cur.line - 1, ch: 0 });
    }
    _easyMde.codemirror.focus();

    var bsModal = bootstrap.Modal.getInstance(document.getElementById('langPickerModal'));
    if (bsModal) bsModal.hide();
    lpReset();
}
</script>
@endpush
