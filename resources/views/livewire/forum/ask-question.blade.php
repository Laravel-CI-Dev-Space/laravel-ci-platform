<div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <section class="section-sm">
        <div class="container">

            {{-- En-tête --}}
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <h2 class="mb-1" style="font-size:var(--fs-h3)">Poser une question</h2>
                    <p class="text-muted-2 mb-0" style="font-size:.88rem">
                        Décrivez votre problème clairement pour obtenir une réponse rapide.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <button wire:click="togglePreview" class="btn btn-ghost btn-sm">
                        <i class="fa-solid fa-{{ $preview ? 'pen-to-square' : 'eye' }} me-1"></i>
                        {{ $preview ? 'Modifier' : 'Prévisualiser' }}
                    </button>
                    <a href="{{ route('forum.index') }}" class="btn btn-ghost btn-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i>Retour
                    </a>
                </div>
            </div>

            @if ($preview)
                {{-- ─── PRÉVISUALISATION (pleine largeur) ─── --}}
                <div class="card-soft p-4 p-lg-5">
                    <h3 class="mb-3">{{ $title ?: '(Sans titre)' }}</h3>
                    <div class="prose mb-3">{!! nl2br(e($body)) !!}</div>
                    @if (count($selectedTags) > 0)
                        <div class="q-tags mb-4">
                            @foreach ($tags->whereIn('id', $selectedTags) as $tag)
                                <span class="tag">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @endif
                    <button wire:click="togglePreview" class="btn btn-ghost">
                        <i class="fa-solid fa-pen-to-square me-1"></i>Continuer d'éditer
                    </button>
                </div>

            @else
                {{-- ─── FORMULAIRE : formulaire gauche + tags droite ─── --}}
                <div class="row g-4 align-items-start">

                    {{-- ══ COLONNE GAUCHE : Titre + Corps + Actions ══ --}}
                    <div class="col-lg-8">
                        <div class="card-soft p-4">

                            {{-- Titre --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Titre <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    wire:model.live="title"
                                    class="form-control @error('title') is-invalid @enderror"
                                    placeholder="Soyez précis — imaginez que vous posez la question à un collègue"
                                    maxlength="300"
                                />
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="text-muted-2 mt-1" style="font-size:.8rem">
                                    {{ strlen($title) }} / 300 caractères
                                </div>
                            </div>

                            {{-- Corps --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Description <span class="text-danger">*</span>
                                </label>
                                <textarea
                                    wire:model.live="body"
                                    class="form-control @error('body') is-invalid @enderror"
                                    rows="14"
                                    data-mention
                                    placeholder="Décrivez votre problème en détail.&#10;&#10;Incluez :&#10;• Le code concerné&#10;• Les messages d'erreur&#10;• Ce que vous avez déjà essayé…"
                                ></textarea>
                                @error('body')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="text-muted-2 mt-1" style="font-size:.8rem">
                                    <i class="fa-brands fa-markdown me-1"></i>Markdown supporté ·
                                    <code style="font-size:.75rem">```php</code> pour les blocs de code
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="d-flex justify-content-end gap-3 pt-2" style="border-top:1px solid var(--border,#eef0f4)">
                                <a href="{{ route('forum.index') }}" class="btn btn-ghost">Annuler</a>
                                <button wire:click="save" wire:loading.attr="disabled" class="btn btn-brand">
                                    <span wire:loading wire:target="save">
                                        <i class="fa-solid fa-circle-notch fa-spin me-1"></i>Publication…
                                    </span>
                                    <span wire:loading.remove wire:target="save">
                                        <i class="fa-solid fa-paper-plane me-1"></i>Publier la question
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ══ COLONNE DROITE : Tags ══ --}}
                    <div class="col-lg-4" style="position:sticky; top:1.5rem;">

                        {{-- Tags sélectionnés --}}
                        <div class="card-soft p-4 mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
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

                            @if (count($selectedTags) > 0)
                                <div class="d-flex flex-wrap gap-1 mb-3">
                                    @foreach ($tags->whereIn('id', $selectedTags) as $tag)
                                        <span
                                            class="badge d-inline-flex align-items-center gap-1"
                                            style="background:var(--orange,#e8590c);color:#fff;border-radius:2rem;font-size:.75rem;padding:.25rem .65rem;cursor:pointer;"
                                            wire:click="removeTag({{ $tag->id }})"
                                            title="Retirer {{ $tag->name }}"
                                        >
                                            {{ $tag->name }}
                                            <i class="fa-solid fa-xmark" style="font-size:.6rem"></i>
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted-2 mb-3" style="font-size:.8rem">
                                    Sélectionnez 1 à 5 tags ci-dessous pour aider la communauté à trouver votre question.
                                </p>
                            @endif

                            {{-- Liste tags disponibles --}}
                            <div style="max-height:280px; overflow-y:auto;">
                                @foreach ($tags->whereNotIn('id', $selectedTags) as $tag)
                                    <div
                                        class="d-flex justify-content-between align-items-center px-2 py-2 rounded-2 mb-1"
                                        style="cursor:pointer; font-size:.85rem; transition:.1s;"
                                        wire:click="addTag({{ $tag->id }})"
                                        onmouseover="this.style.background='var(--light,#f5f6fa)'"
                                        onmouseout="this.style.background='transparent'"
                                    >
                                        <span class="mono">{{ $tag->name }}</span>
                                        <span class="badge bg-light text-secondary border" style="font-size:.7rem">
                                            {{ number_format($tag->usage_count) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Conseils --}}
                        <div class="card-soft p-3">
                            <div class="fw-semibold mb-2" style="font-size:.82rem">
                                <i class="fa-solid fa-lightbulb text-orange me-1"></i>
                                Conseils pour une bonne question
                            </div>
                            <ul class="mb-0 ps-3" style="font-size:.78rem; color:var(--muted); line-height:1.8">
                                <li>Titre court et précis</li>
                                <li>Contexte : version Laravel, PHP, OS</li>
                                <li>Code minimal reproduisant le problème</li>
                                <li>Message d'erreur complet</li>
                                <li>Ce que vous avez déjà essayé</li>
                            </ul>
                        </div>

                    </div>{{-- /col-lg-4 --}}
                </div>{{-- /row --}}
            @endif

        </div>
    </section>
</div>
