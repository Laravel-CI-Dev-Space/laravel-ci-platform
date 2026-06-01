@extends('layouts.web')

@section('title', 'Publier une offre — Laravel CI')

@section('content')

  <section class="page-hero">
    <div class="container">
      <div class="breadcrumb-bar">
        <a href="{{ route('home') }}">Accueil</a>
        <i class="fa-solid fa-chevron-right"></i>
        <a href="{{ route('jobs.index') }}">Emplois</a>
        <i class="fa-solid fa-chevron-right"></i>
        <span>Publier une offre</span>
      </div>
      <h1 class="mb-2">Publier une offre</h1>
      <p class="lead mb-0">Votre offre sera enregistrée en brouillon et validée par l'équipe avant publication.</p>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <form method="POST" action="{{ route('jobs.store') }}" class="sidebar-card p-4">
            @csrf

            <fieldset class="mb-4">
              <legend class="sidebar-title border-0 p-0 mb-3">Entreprise</legend>
              <div class="mb-3">
                <label for="company_name" class="form-label fw-semibold">Nom *</label>
                <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" required class="form-control">
                @error('company_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
              </div>
              <div class="mb-0">
                <label for="company_description" class="form-label fw-semibold">Description</label>
                <textarea id="company_description" name="company_description" rows="2" class="form-control">{{ old('company_description') }}</textarea>
              </div>
            </fieldset>

            <fieldset class="mb-4">
              <legend class="sidebar-title border-0 p-0 mb-3">Offre</legend>
              <div class="mb-3">
                <label for="title" class="form-label fw-semibold">Intitulé du poste *</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required class="form-control">
                @error('title')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
              </div>
              <div class="mb-3">
                <label for="type" class="form-label fw-semibold">Type de contrat *</label>
                <select id="type" name="type" required class="form-select">
                  <option value="">Choisir…</option>
                  @foreach(\App\Enums\Jobs\JobOfferType::cases() as $offerType)
                    <option value="{{ $offerType->value }}" @selected(old('type') === $offerType->value)>{{ $offerType->label() }}</option>
                  @endforeach
                </select>
                @error('type')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
              </div>
              <div class="mb-3">
                <label for="location" class="form-label fw-semibold">Localisation *</label>
                <input type="text" id="location" name="location" value="{{ old('location') }}" required
                       class="form-control" placeholder="Abidjan, Remote…">
                @error('location')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
              </div>
              <div class="mb-0">
                <label for="description" class="form-label fw-semibold">Description *</label>
                <textarea id="description" name="description" rows="8" required class="form-control">{{ old('description') }}</textarea>
                @error('description')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
              </div>
            </fieldset>

            <button type="submit" class="btn btn-brand w-100 btn-lg">
              <i class="fa-solid fa-paper-plane"></i> Soumettre l'offre
            </button>
          </form>
        </div>
      </div>
    </div>
  </section>

@endsection
