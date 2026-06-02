<div>
    <section class="section-sm">
        <div class="container">

            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div>
                    <div class="breadcrumb-bar mb-1">
                        <a href="{{ route('home') }}">Accueil</a>
                        <i class="fa-solid fa-chevron-right"></i>
                        <a href="{{ route('forum.index') }}">Forum</a>
                        <i class="fa-solid fa-chevron-right"></i>
                        <span>Modifier</span>
                    </div>
                    <h2 class="mb-0" style="font-size:var(--fs-h3)">Modifier la question</h2>
                </div>
                <a href="{{ route('forum.index') }}" class="btn btn-ghost btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i>Retour
                </a>
            </div>

            <div class="row g-4 align-items-start">

                {{-- Formulaire --}}
                <div class="col-lg-8">
                    <div class="card-soft p-4">

                        {{-- Titre --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Titre <span class="text-danger">*</span></label>
                            <input type="text" wire:model.live="title"
                                   class="form-control @error('title') is-invalid @enderror"
                                   maxlength="300" />
                            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="text-muted-2 mt-1" style="font-size:.8rem">{{ strlen($title) }} / 300</div>
                        </div>

                        {{-- Corps --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Description <span class="text-danger">*</span>
                            </label>
                            <textarea wire:model.live="body"
                                      class="form-control @error('body') is-invalid @enderror"
                                      rows="14"
                                      placeholder="Décrivez votre problème en détail…"></textarea>
                            @error('body') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="text-muted-2 mt-1" style="font-size:.8rem">
                                <i class="fa-brands fa-markdown me-1"></i>Markdown supporté
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex justify-content-end gap-3 pt-2"
                             style="border-top:1px solid var(--border,#eef0f4)">
                            <a href="{{ route('forum.index') }}" class="btn btn-ghost">Annuler</a>
                            <button wire:click="save" wire:loading.attr="disabled" class="btn btn-brand">
                                <span wire:loading wire:target="save">
                                    <i class="fa-solid fa-circle-notch fa-spin me-1"></i>
                                </span>
                                <span wire:loading.remove wire:target="save">
                                    <i class="fa-solid fa-floppy-disk me-1"></i>
                                </span>
                                Enregistrer les modifications
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Tags sidebar --}}
                <div class="col-lg-4" style="position:sticky; top:1.5rem;">
                    <div class="card-soft p-4 mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <label class="form-label fw-semibold mb-0">Tags <span class="text-danger">*</span></label>
                            <span class="text-muted-2" style="font-size:.78rem">{{ count($selectedTags) }}/5</span>
                        </div>
                        @error('selectedTags')
                            <div class="text-danger mb-2" style="font-size:.82rem">{{ $message }}</div>
                        @enderror
                        @if (count($selectedTags) > 0)
                            <div class="d-flex flex-wrap gap-1 mb-2">
                                @foreach ($tags->whereIn('id', $selectedTags) as $tag)
                                    <span class="badge d-inline-flex align-items-center gap-1"
                                          style="background:var(--orange);color:#fff;border-radius:2rem;font-size:.75rem;padding:.25rem .65rem;cursor:pointer"
                                          wire:click="removeTag({{ $tag->id }})">
                                        {{ $tag->name }}<i class="fa-solid fa-xmark" style="font-size:.6rem"></i>
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted-2 mb-3" style="font-size:.8rem">Sélectionnez 1 à 5 tags.</p>
                        @endif
                        <div style="max-height:280px; overflow-y:auto">
                            @foreach ($tags->whereNotIn('id', $selectedTags) as $tag)
                                <div class="d-flex justify-content-between align-items-center px-2 py-2 rounded-2 mb-1"
                                     style="cursor:pointer; font-size:.85rem"
                                     wire:click="addTag({{ $tag->id }})"
                                     onmouseover="this.style.background='var(--light)'"
                                     onmouseout="this.style.background='transparent'">
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
                            <i class="fa-solid fa-lightbulb text-orange me-1"></i>Rappel
                        </div>
                        <ul class="mb-0 ps-3" style="font-size:.78rem; color:var(--muted); line-height:1.8">
                            <li>Modification possible <strong>48h</strong> après la publication</li>
                            <li>La communauté peut déjà avoir répondu</li>
                            <li>Précisez ce que vous avez modifié</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>
