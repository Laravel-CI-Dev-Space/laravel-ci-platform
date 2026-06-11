@extends('emails.layouts.base')

@section('subject', "Nouvelle candidature pour {$application->jobOffer->title} — Laravel CI")

@section('content')

    <p class="greeting">
        Bonjour, <span>{{ $account->fullName() }}</span>
    </p>

    <p class="text">
        Vous avez reçu une nouvelle candidature pour votre offre
        <strong>« {{ $application->jobOffer->title }} »</strong>.
    </p>

    <div class="info-box">
        <h3>Détails de la candidature</h3>
        <div class="info-row">
            <strong>Poste</strong>
            <span>{{ $application->jobOffer->title }}</span>
        </div>
        <div class="info-row">
            <strong>Candidat</strong>
            <span>{{ $application->user->name }}</span>
        </div>
        <div class="info-row">
            <strong>Date</strong>
            <span>{{ $application->created_at->translatedFormat('d F Y \à H:i') }}</span>
        </div>
    </div>

    <div class="cta-primary">
        <a href="{{ route('company.applications.show', $application) }}">Voir la candidature &rarr;</a>
    </div>

    <p class="text">
        Pensez à mettre à jour le statut de la candidature dans votre espace recruteur
        afin de garder une vue à jour sur vos recrutements.
    </p>

@endsection
