@extends('layouts.web')

@section('title', 'Connexion Entreprise — Laravel CI')

@section('content')
<section class="section" style="min-height:70vh; display:flex; align-items:center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">

                <div class="text-center mb-4">
                    <img src="{{ asset('assets/logo.jpeg') }}" alt="Laravel CI" style="height:52px;" class="mb-3" />
                    <h1 style="font-size:1.6rem" class="text-navy">Espace Entreprise</h1>
                    <p class="text-muted-2">Connectez-vous pour gérer vos offres d'emploi.</p>
                </div>

                @if (session('success'))
                    <div class="alert alert-success mb-3">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger mb-3">{{ session('error') }}</div>
                @endif
                @if (session('warning'))
                    <div class="alert alert-warning mb-3">{{ session('warning') }}</div>
                @endif

                <div class="card-soft p-4 p-lg-5">
                    <form method="POST" action="{{ route('company.login.submit') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Adresse email</label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="contact@votreentreprise.ci"
                                   autocomplete="email" autofocus />
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Mot de passe</label>
                            <input type="password" name="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="••••••••••"
                                   autocomplete="current-password" />
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <label class="form-check-label d-flex align-items-center gap-2" style="cursor:pointer">
                                <input type="checkbox" name="remember" class="form-check-input mt-0" />
                                <span style="font-size:.88rem">Se souvenir de moi</span>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-brand w-100">
                            <i class="fa-solid fa-sign-in-alt me-1"></i>Se connecter
                        </button>
                    </form>
                </div>

                <div class="text-center mt-4" style="font-size:.88rem">
                    <p class="text-muted-2 mb-1">
                        Pas encore de compte ?
                        <a href="{{ route('company.register') }}" class="text-orange fw-semibold">Faire une demande d'accès</a>
                    </p>
                    <p class="text-muted-2">
                        Vous êtes développeur ?
                        <a href="{{ route('login') }}">Connexion membre</a>
                    </p>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
