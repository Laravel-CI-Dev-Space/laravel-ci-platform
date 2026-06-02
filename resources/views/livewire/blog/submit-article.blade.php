<div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <section class="section-sm">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="mb-0" style="font-size:var(--fs-h3)">Rédiger un article</h2>
                        <button wire:click="togglePreview" class="btn btn-ghost btn-sm">
                            <i class="fa-solid fa-{{ $preview ? 'pen-to-square' : 'eye' }} me-1"></i>
                            {{ $preview ? 'Modifier' : 'Prévisualiser' }}
                        </button>
                    </div>

                    @if ($preview)
                        {{-- ===== MODE PRÉVISUALISATION ===== --}}
                        <div class="card-soft p-4 p-lg-5">
                            @if ($coverImage)
                                <img src="{{ $coverImage->temporaryUrl() }}" alt="Couverture" class="img-fluid rounded mb-4" style="width:100%;max-height:300px;object-fit:cover;" />
                            @endif

                            <div class="mb-2">
                                <span class="badge-pill badge-{{ $level === 'beginner' ? 'green' : ($level === 'intermediate' ? 'orange' : '') }}">
                                    {{ ['beginner' => 'Débutant', 'intermediate' => 'Intermédiaire', 'advanced' => 'Avancé'][$level] ?? $level }}
                                </span>
                            </div>

                            <h3>{{ $title ?: '(Sans titre)' }}</h3>

                            @if ($excerpt)
                                <p class="lead" style="color:var(--muted)">{{ $excerpt }}</p>
                            @endif

                            <div class="article-body mt-3">
                                {!! nl2br(e($body)) !!}
                            </div>

                            @if (count($selectedTags) > 0)
                                <div class="q-tags mt-3">
                                    @foreach ($tags->whereIn('id', $selectedTags) as $tag)
                                        <span class="tag">{{ $tag->name }}</span>
                                    @endforeach
                                </div>
                            @endif

                            <button wire:click="togglePreview" class="btn btn-ghost mt-4">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Continuer d'éditer
                            </button>
                        </div>
                    @else
                        {{-- ===== FORMULAIRE D'ÉDITION ===== --}}
                        <div class="card-soft p-4 p-lg-5">

                            {{-- Titre --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Titre <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    wire:model.live="title"
                                    class="form-control @error('title') is-invalid @enderror"
                                    placeholder="Un titre clair et précis (min. 10 caractères)"
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
                                    <span class="text-muted-2 fw-normal">(optionnel — affiché sur la liste)</span>
                                </label>
                                <textarea
                                    wire:model.live="excerpt"
                                    class="form-control @error('excerpt') is-invalid @enderror"
                                    rows="2"
                                    placeholder="Un résumé accrocheur de votre article…"
                                    maxlength="500"
                                ></textarea>
                                @error('excerpt')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Corps --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Contenu <span class="text-danger">*</span>
                                </label>
                                <textarea
                                    wire:model.live="body"
                                    class="form-control @error('body') is-invalid @enderror"
                                    rows="16"
                                    placeholder="Rédigez votre article ici. Markdown supporté…"
                                ></textarea>
                                @error('body')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="text-muted-2 mt-1" style="font-size:.82rem">
                                    <i class="fa-brands fa-markdown me-1"></i> Markdown supporté · min. 100 caractères
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
                                            <span
                                                class="tag"
                                                style="cursor:pointer"
                                                wire:click="removeTag({{ $tag->id }})"
                                            >
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

                            {{-- Image de couverture --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    Image de couverture
                                    <span class="text-muted-2 fw-normal">(optionnel — max. 2 Mo)</span>
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
                                        <img
                                            src="{{ $coverImage->temporaryUrl() }}"
                                            alt="Aperçu"
                                            style="max-height:150px; border-radius:var(--radius);"
                                        />
                                    </div>
                                @endif
                            </div>

                            {{-- Actions --}}
                            <div class="d-flex justify-content-end gap-3">
                                <a href="{{ route('blog.index') }}" class="btn btn-ghost">Annuler</a>
                                <button
                                    wire:click="save"
                                    wire:loading.attr="disabled"
                                    class="btn btn-brand"
                                >
                                    <span wire:loading wire:target="save">
                                        <i class="fa-solid fa-circle-notch fa-spin me-1"></i>
                                    </span>
                                    <span wire:loading.remove wire:target="save">
                                        <i class="fa-solid fa-floppy-disk me-1"></i>
                                    </span>
                                    Sauvegarder en brouillon
                                </button>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </section>
</div>
