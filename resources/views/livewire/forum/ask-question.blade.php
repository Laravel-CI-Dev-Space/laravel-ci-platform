<div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card-soft p-4 p-lg-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0" style="font-size:var(--fs-h3)">Poser une question</h2>
            <button
                wire:click="togglePreview"
                class="btn btn-ghost btn-sm"
            >
                <i class="fa-solid fa-{{ $preview ? 'pen-to-square' : 'eye' }} me-1"></i>
                {{ $preview ? 'Modifier' : 'Prévisualiser' }}
            </button>
        </div>

        @if ($preview)
            {{-- MODE PRÉVISUALISATION --}}
            <div class="mb-4">
                <h3>{{ $title ?: '(Sans titre)' }}</h3>
                <div class="prose mt-3">
                    {!! nl2br(e($body)) !!}
                </div>
                @if (count($selectedTags) > 0)
                    <div class="q-tags mt-3">
                        @foreach ($tags->whereIn('id', $selectedTags) as $tag)
                            <span class="tag">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
            <button wire:click="togglePreview" class="btn btn-ghost">
                <i class="fa-solid fa-pen-to-square me-1"></i> Continuer d'éditer
            </button>
        @else
            {{-- FORMULAIRE --}}
            <div class="mb-3">
                <label class="form-label fw-semibold">Titre <span class="text-danger">*</span></label>
                <input
                    type="text"
                    wire:model.live="title"
                    class="form-control @error('title') is-invalid @enderror"
                    placeholder="Soyez précis et imaginez que vous posez la question à une autre personne"
                    maxlength="300"
                />
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="text-muted-2 mt-1" style="font-size:.82rem">
                    {{ strlen($title) }} / 300 caractères
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Description <span class="text-danger">*</span>
                </label>
                <textarea
                    wire:model.live="body"
                    class="form-control @error('body') is-invalid @enderror"
                    rows="12"
                    placeholder="Décrivez votre problème en détail. Incluez le code, les messages d'erreur et ce que vous avez déjà essayé…"
                ></textarea>
                @error('body')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="text-muted-2 mt-1" style="font-size:.82rem">
                    <i class="fa-brands fa-markdown me-1"></i> Markdown supporté
                </div>
            </div>

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
                                {{ $tag->name }}
                                <i class="fa-solid fa-xmark ms-1"></i>
                            </span>
                        @endforeach
                    </div>
                @endif

                <div class="tag-list">
                    @foreach ($tags->whereNotIn('id', $selectedTags) as $tag)
                        <div
                            class="tag-list-item"
                            style="cursor:pointer"
                            wire:click="addTag({{ $tag->id }})"
                        >
                            <span class="mono">{{ $tag->name }}</span>
                            <span class="count">{{ number_format($tag->usage_count) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="d-flex justify-content-end gap-3">
                <a href="{{ route('forum.index') }}" class="btn btn-ghost">Annuler</a>
                <button wire:click="save" wire:loading.attr="disabled" class="btn btn-brand">
                    <span wire:loading wire:target="save">
                        <i class="fa-solid fa-circle-notch fa-spin me-1"></i>
                    </span>
                    <span wire:loading.remove wire:target="save">
                        <i class="fa-solid fa-paper-plane me-1"></i>
                    </span>
                    Publier la question
                </button>
            </div>
        @endif
    </div>
</div>
