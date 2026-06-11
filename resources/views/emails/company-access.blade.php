@extends('emails.layouts.base')

@section('subject', 'Votre compte entreprise Laravel CI est activé')

@section('content')

    <p class="greeting">
        Bienvenue, <span>{{ $account->company?->name ?? $account->fullName() }}</span> !
    </p>

    <p class="text">
        Votre demande d'inscription au portail recruteur de <strong>Laravel Côte d'Ivoire</strong> a été validée.
        Votre espace entreprise est désormais actif.
    </p>

    <div class="info-box">
        <h3>Vos identifiants de connexion</h3>
        <div class="info-row">
            <strong>Email</strong>
            <span>{{ $account->email }}</span>
        </div>
    </div>

    <div class="credentials-box">
        <div class="label">Mot de passe temporaire</div>
        <div class="value">{{ $temporaryPassword }}</div>
    </div>

    <div class="cta-primary">
        <a href="{{ route('company.login') }}">Se connecter au portail entreprise &rarr;</a>
    </div>

    <div class="alert-box">
        ⚠️ Pour des raisons de sécurité, vous devrez changer ce mot de passe temporaire
        dès votre première connexion. Ne le partagez avec personne.
    </div>

    <p class="text">
        Une fois connecté(e), vous pourrez publier des offres d'emploi, consulter
        les candidatures reçues et gérer le profil de votre entreprise.
    </p>

@endsection
