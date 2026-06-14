@extends('emails.layouts.base')

@section('subject', "Récapitulatif disponible : {$event->title} — Laravel CI")

@section('content')

    <p class="greeting">
        Bonjour, <span>{{ $user->name }}</span> !
    </p>

    <p class="text">
        Le récapitulatif de l'événement <strong>« {{ $event->title }} »</strong>
        ({{ $event->starts_at->translatedFormat('d F Y') }}) est désormais disponible.
    </p>

    @if($event->recap_summary)
        <div class="info-box">
            <h3>Résumé</h3>
            <p class="text" style="margin-bottom:0;">
                {{ \Illuminate\Support\Str::limit(strip_tags($event->recap_summary), 200) }}
            </p>
        </div>
    @endif

    <div class="cta-primary">
        <a href="{{ route('events.show', $event->slug) . '#recap' }}">Voir le récapitulatif &rarr;</a>
    </div>

@endsection
