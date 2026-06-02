@extends('layouts.web')

@section('title', 'Modifier l\'article — Laravel CI')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde@2.18.0/dist/easymde.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/highlight.js@11.9.0/styles/github-dark.min.css" />
<style>
.EasyMDEContainer { border-radius: var(--radius,.75rem); overflow: hidden; }
.editor-toolbar  { border-radius: var(--radius,.75rem) var(--radius,.75rem) 0 0; background:#f8f9fa; border-color:#dee2e6; padding:.3rem .5rem; flex-wrap:wrap; gap:.1rem; }
.editor-toolbar button { border-radius:6px; color:#374151; }
.editor-toolbar button:hover,.editor-toolbar button.active { background:#e9ecef; color:var(--orange,#e8590c); }
.CodeMirror { border:1px solid #dee2e6; border-top:none; border-radius:0 0 var(--radius,.75rem) var(--radius,.75rem); font-family:'JetBrains Mono',monospace; font-size:.9rem; line-height:1.7; min-height:380px; }
.CodeMirror-focused { border-color:var(--orange,#e8590c) !important; box-shadow:0 0 0 .2rem rgba(232,89,12,.12); }
</style>
@endpush

@section('content')
    @livewire('blog.edit-article', ['article' => $article])
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/highlight.js@11.9.0/highlight.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/easymde@2.18.0/dist/easymde.min.js"></script>
<script>
var _editMde = null;

function initEditMDE() {
    var textarea = document.getElementById('article-edit-mde');
    if (!textarea || typeof EasyMDE === 'undefined' || textarea._mdeInit) return;
    textarea._mdeInit = true;

    var wrapper  = document.getElementById('mde-edit-wrapper');
    var initial  = (wrapper && wrapper.dataset.initial) ? wrapper.dataset.initial : '';

    _editMde = new EasyMDE({
        element:       textarea,
        initialValue:  initial,
        spellChecker:  false,
        autosave:      { enabled: false },
        toolbar: [
            { name:'bold',          action:EasyMDE.toggleBold,          className:'fa fa-bold',           title:'Gras' },
            { name:'italic',        action:EasyMDE.toggleItalic,        className:'fa fa-italic',         title:'Italique' },
            '|',
            { name:'h2',            action:EasyMDE.toggleHeading2,      className:'fa fa-header fa-header-x fa-header-2', title:'H2' },
            { name:'h3',            action:EasyMDE.toggleHeading3,      className:'fa fa-header fa-header-x fa-header-3', title:'H3' },
            '|',
            { name:'code',          action:EasyMDE.toggleCodeBlock,     className:'fa fa-code',           title:'Bloc de code' },
            { name:'quote',         action:EasyMDE.toggleBlockquote,    className:'fa fa-quote-left',     title:'Citation' },
            '|',
            { name:'ul',            action:EasyMDE.toggleUnorderedList, className:'fa fa-list-ul',        title:'Liste' },
            { name:'link',          action:EasyMDE.drawLink,            className:'fa fa-link',           title:'Lien' },
            '|',
            { name:'preview',       action:EasyMDE.togglePreview,       className:'fa fa-eye no-disable', title:'Prévisualiser' },
            { name:'fullscreen',    action:EasyMDE.toggleFullScreen,    className:'fa fa-arrows-alt no-disable no-mobile', title:'Plein écran' },
        ],
        renderingConfig: { codeSyntaxHighlighting: true, hljs: window.hljs },
        status: ['lines','words'],
    });

    _editMde.codemirror.on('change', function () {
        var hidden = document.getElementById('mde-edit-body-sync');
        if (hidden) {
            hidden.value = _editMde.value();
            hidden.dispatchEvent(new Event('input', { bubbles: true }));
        }
    });
}

setTimeout(initEditMDE, 400);
document.addEventListener('livewire:navigated', function () {
    _editMde = null;
    var ta = document.getElementById('article-edit-mde');
    if (ta) delete ta._mdeInit;
    setTimeout(initEditMDE, 400);
});
</script>
@endpush
