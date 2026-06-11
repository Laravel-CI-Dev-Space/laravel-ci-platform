@extends('emails.layouts.base')

@section('subject', "Inscription confirmée : {$registration->event->title} — Laravel CI")

@section('content')

    @php $event = $registration->event; @endphp

    <p class="greeting">
        C'est confirmé, <span>{{ $user->name }}</span> !
    </p>

    <p class="text">
        Votre inscription à l'événement <strong>« {{ $event->title }} »</strong> a bien été enregistrée.
        @if($registration->isWaitlisted())
            Vous êtes actuellement sur liste d'attente — nous vous préviendrons dès qu'une place se libère.
        @else
            Nous avons hâte de vous y retrouver !
        @endif
    </p>

    <div class="info-box">
        <h3>Détails de l'événement</h3>
        <div class="info-row">
            <strong>Date</strong>
            <span>{{ $event->starts_at->translatedFormat('l d F Y \à H:i') }}</span>
        </div>
        @if($event->isOnline())
            <div class="info-row">
                <strong>Format</strong>
                <span>En ligne</span>
            </div>
        @else
            <div class="info-row">
                <strong>Lieu</strong>
                <span>{{ $event->location }}</span>
            </div>
        @endif
        @if($registration->ticket_number)
            <div class="info-row">
                <strong>N° de ticket</strong>
                <span>{{ $registration->ticket_number }}</span>
            </div>
        @endif
    </div>

    <div class="cta-primary">
        <a href="{{ route('events.show', $event->slug) }}">Voir la page de l'événement &rarr;</a>
    </div>

    <div class="cta-secondary">
        <a href="{{ route('events.ical', $registration) }}">📅 Ajouter à mon calendrier (.ics)</a>
    </div>

    @if($registration->canCancel())
        <div class="alert-box">
            Un empêchement ? Vous pouvez annuler votre inscription depuis votre tableau de bord
            jusqu'à 48h avant le début de l'événement.
        </div>
    @endif

@endsection
