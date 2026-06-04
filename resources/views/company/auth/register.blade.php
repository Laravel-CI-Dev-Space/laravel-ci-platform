@extends('layouts.web')

@section('title', "Demande d'accès Entreprise — Laravel CI")

@section('content')
<section class="section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <div class="text-center mb-5">
                    <h1 style="font-size:1.8rem" class="text-navy">Demande d'accès Entreprise</h1>
                    <p class="lead text-muted-2">
                        Remplissez ce formulaire pour accéder au Job Board Laravel CI.
                        Votre demande sera examinée sous 48h.
                    </p>
                </div>

                @if (session('success'))
                    <div class="alert alert-success mb-4">
                        <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger mb-4">
                        <strong>Veuillez corriger les erreurs suivantes :</strong>
                        <ul class="mb-0 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('company.register.submit') }}">
                    @csrf

                    {{-- Informations personnelles --}}
                    <div class="card-soft p-4 mb-4">
                        <h5 class="fw-bold mb-3 text-navy">
                            <i class="fa-solid fa-user me-2 text-orange"></i>Informations personnelles
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Prénom <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}"
                                       class="form-control @error('first_name') is-invalid @enderror"
                                       maxlength="100" />
                                @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nom <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}"
                                       class="form-control @error('last_name') is-invalid @enderror"
                                       maxlength="100" />
                                @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Poste occupé <span class="text-danger">*</span></label>
                                <input type="text" name="position" value="{{ old('position') }}"
                                       class="form-control @error('position') is-invalid @enderror"
                                       placeholder="ex: Directeur RH, CEO…" maxlength="100" />
                                @error('position') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Téléphone</label>
                                <input type="tel" name="phone" value="{{ old('phone') }}"
                                       class="form-control @error('phone') is-invalid @enderror"
                                       placeholder="+225 07 00 00 00 00" maxlength="30" />
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Informations entreprise --}}
                    <div class="card-soft p-4 mb-4">
                        <h5 class="fw-bold mb-3 text-navy">
                            <i class="fa-solid fa-building me-2 text-orange"></i>Informations entreprise
                        </h5>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-semibold">Nom de l'entreprise <span class="text-danger">*</span></label>
                                <input type="text" name="company_name" value="{{ old('company_name') }}"
                                       class="form-control @error('company_name') is-invalid @enderror"
                                       maxlength="200" />
                                @error('company_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Secteur d'activité <span class="text-danger">*</span></label>
                                <input type="text" name="business_domain" value="{{ old('business_domain') }}"
                                       class="form-control @error('business_domain') is-invalid @enderror"
                                       placeholder="ex: Fintech, EdTech…" maxlength="150" />
                                @error('business_domain') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Pays <span class="text-danger">*</span></label>
                                <input type="text" name="country" value="{{ old('country', 'Côte d\'Ivoire') }}"
                                       class="form-control @error('country') is-invalid @enderror"
                                       maxlength="100" />
                                @error('country') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ville</label>
                                <input type="text" name="city" value="{{ old('city') }}"
                                       class="form-control @error('city') is-invalid @enderror"
                                       placeholder="ex: Abidjan" maxlength="100" />
                                @error('city') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-semibold">Site web</label>
                                <input type="url" name="website" value="{{ old('website') }}"
                                       class="form-control @error('website') is-invalid @enderror"
                                       placeholder="https://votreentreprise.ci" />
                                @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Contact & Message --}}
                    <div class="card-soft p-4 mb-4">
                        <h5 class="fw-bold mb-3 text-navy">
                            <i class="fa-solid fa-envelope me-2 text-orange"></i>Contact & Message
                        </h5>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email professionnel <span class="text-danger">*</span></label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="rh@votreentreprise.ci" />
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div class="text-muted-2 mt-1" style="font-size:.8rem">
                                Cet email sera votre identifiant de connexion.
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold">Message de présentation</label>
                            <textarea name="motivation"
                                      class="form-control @error('motivation') is-invalid @enderror"
                                      rows="4"
                                      placeholder="Présentez brièvement votre entreprise et vos besoins en recrutement…"
                                      maxlength="1000">{{ old('motivation') }}</textarea>
                            @error('motivation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-3">
                        <a href="{{ route('company.login') }}" class="btn btn-ghost">Annuler</a>
                        <button type="submit" class="btn btn-brand">
                            <i class="fa-solid fa-paper-plane me-1"></i>Soumettre ma demande
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</section>
@endsection
