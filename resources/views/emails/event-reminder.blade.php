@extends('emails.layouts.base')

@section('subject', match($type) {
    '7d' => "Dans 7 jours : {$registration->event->title} — Laravel CI",
    '1d' => "Demain : {$registration->event->title} — Laravel CI",
    default => "Rappel : {$registration->event->title} — Laravel CI",
})

@section('content')

    @php $event = $registration->event; @endphp

    <p class="greeting">
        Bonjour, <span>{{ $user->name }}</span> !
    </p>

    @if($type === '7d')
        <p class="text">
            Petit rappel : l'événement <strong>« {{ $event->title }} »</strong> auquel vous êtes inscrit(e)
            aura lieu dans <strong style="color:#FF6600;">7 jours</strong>.
        </p>
    @elseif($type === '1d')
        <p class="text">
            C'est demain ! L'événement <strong>« {{ $event->title }} »</strong> auquel vous êtes inscrit(e)
            a lieu <strong style="color:#FF6600;">demain</strong>.
        </p>
    @else
        <p class="text">
            Petit rappel : vous êtes inscrit(e) à l'événement <strong>« {{ $event->title }} »</strong>.
        </p>
    @endif

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
    </div>

    @if($event->isOnline())
        <div class="cta-primary">
            <a href="{{ $event->online_url }}">Rejoindre l'événement en ligne &rarr;</a>
        </div>
    @else
        <div class="cta-primary">
            <a href="{{ route('events.show', $event->slug) }}">Voir l'itinéraire et les détails &rarr;</a>
        </div>
    @endif

    <p class="text">
        À très bientôt ! 🇨🇮
    </p>

@endsection
