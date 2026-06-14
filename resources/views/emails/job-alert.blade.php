@extends('emails.layout')

@section('title', 'Nouvelle offre correspondant à votre alerte')

@section('content')
    <p>Bonjour {{ $user->name }},</p>
    <p>Une nouvelle offre correspond à l'une de vos alertes emploi :</p>

    <p class="lead">{{ $offer->title }}</p>
    <p class="meta">
        {{ $offer->company->name }}
        @if($offer->location)
            · {{ $offer->location }}
        @endif
        · {{ $offer->type->label() }}
    </p>

    @if($offer->salary)
        <p class="meta">Salaire : {{ $offer->salary }}</p>
    @endif

    <a href="{{ route('jobs.show', $offer) }}" class="btn">
        Voir l'offre
    </a>

    <p class="meta" style="margin-top:24px;">
        Gérez vos alertes depuis votre
        <a href="{{ route('dashboard.member.alerts') }}" style="color:#FF6600;">tableau de bord</a>.
    </p>
@endsection
