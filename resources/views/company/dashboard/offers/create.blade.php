@extends('layouts.dashboard')

@section('title', 'Publier une offre — Laravel CI')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="fs-3 fw-bold mb-1">Publier une offre d'emploi</h1>
        <p class="text-muted mb-0">Votre offre sera vérifiée par notre équipe avant publication.</p>
    </div>
    <a href="{{ route('company.offers.index') }}" class="btn btn-ghost">
        <i class="ti ti-arrow-left me-1"></i>Retour
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger mb-4">
        <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ route('company.offers.store') }}">
    @csrf

    <div class="row g-4">
        {{-- Colonne principale --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-3">Informations principales</h5>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Intitulé du poste <span class="text-danger">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}"
                           class="form-control @error('title') is-invalid @enderror"
                           placeholder="ex: Développeur Laravel Senior" maxlength="200" />
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Description du poste <span class="text-danger">*</span></label>
                    <textarea name="description"
                              class="form-control @error('description') is-invalid @enderror"
                              rows="12"
                              placeholder="Décrivez le poste, les responsabilités, les prérequis et les avantages…">{{ old('description') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="text-muted" style="font-size:.78rem">Markdown supporté · minimum 100 caractères</div>
                </div>
            </div>

            <div class="card border-0 shadow-sm p-4 mb-4">
                <h5 class="fw-bold mb-3">Catégories & Compétences</h5>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Catégories <span class="text-danger">*</span></label>
                    <select name="categories[]" multiple
                            class="form-select @error('categories') is-invalid @enderror" size="5">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ in_array($cat->id, old('categories', [])) ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('categories') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-0">
                    <label class="form-label fw-semibold">Compétences requises <span class="text-danger">*</span>
                        <span class="text-muted fw-normal">(max 10)</span></label>
                    <select name="skills[]" multiple
                            class="form-select @error('skills') is-invalid @enderror" size="6">
                        @foreach ($skills as $skill)
                            <option value="{{ $skill->id }}" {{ in_array($skill->id, old('skills', [])) ? 'selected' : '' }}>
                                {{ $skill->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('skills') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
        </div>

        {{-- Colonne latérale --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 mb-3">
                <h5 class="fw-bold mb-3">Détails du contrat</h5>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Type de contrat <span class="text-danger">*</span></label>
                    <select name="contract_type" class="form-select @error('contract_type') is-invalid @enderror">
                        <option value="">Choisir…</option>
                        @foreach (['cdi' => 'CDI', 'cdd' => 'CDD', 'freelance' => 'Freelance', 'internship' => 'Stage', 'apprenticeship' => 'Alternance'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('contract_type') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @error('contract_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Niveau requis <span class="text-danger">*</span></label>
                    <select name="level" class="form-select @error('level') is-invalid @enderror">
                        @foreach (['junior' => 'Junior', 'intermediate' => 'Intermédiaire', 'senior' => 'Senior', 'lead' => 'Lead', 'any' => 'Tous niveaux'] as $val => $lbl)
                            <option value="{{ $val }}" {{ old('level') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @error('level') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Ville / Localisation</label>
                    <input type="text" name="location" value="{{ old('location') }}"
                           class="form-control" placeholder="ex: Abidjan, Cocody" />
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Pays</label>
                    <input type="text" name="country" value="{{ old('country', 'Côte d\'Ivoire') }}"
                           class="form-control" />
                </div>

                <div class="d-flex gap-3 mb-0">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_remote" id="is_remote"
                               value="1" {{ old('is_remote') ? 'checked' : '' }} />
                        <label class="form-check-label" for="is_remote">Télétravail possible</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_hybrid" id="is_hybrid"
                               value="1" {{ old('is_hybrid') ? 'checked' : '' }} />
                        <label class="form-check-label" for="is_hybrid">Hybride</label>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm p-4 mb-3">
                <h5 class="fw-bold mb-3">Rémunération</h5>

                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label fw-semibold" style="font-size:.85rem">Min (FCFA)</label>
                        <input type="number" name="salary_min" value="{{ old('salary_min') }}"
                               class="form-control" placeholder="500000" min="0" />
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold" style="font-size:.85rem">Max (FCFA)</label>
                        <input type="number" name="salary_max" value="{{ old('salary_max') }}"
                               class="form-control" placeholder="1500000" min="0" />
                    </div>
                </div>
                <input type="hidden" name="currency" value="XOF" />
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="salary_visible" id="salary_visible"
                           value="1" {{ old('salary_visible', 1) ? 'checked' : '' }} />
                    <label class="form-check-label" for="salary_visible" style="font-size:.85rem">Afficher la rémunération</label>
                </div>
            </div>

            <div class="card border-0 shadow-sm p-4 mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_urgent" id="is_urgent"
                           value="1" {{ old('is_urgent') ? 'checked' : '' }} />
                    <label class="form-check-label fw-semibold" for="is_urgent">Offre urgente</label>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-send me-1"></i>Soumettre pour validation
                </button>
                <a href="{{ route('company.offers.index') }}" class="btn btn-ghost text-center">Annuler</a>
            </div>
        </div>
    </div>
</form>

@endsection
