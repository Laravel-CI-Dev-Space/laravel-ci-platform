@extends('emails.layout')

@section('title', 'Nouvelle candidature')

@section('content')
    @php
        $offer = $application->jobOffer;
        $candidate = $application->user;
    @endphp

    <p>Bonjour,</p>
    <p>Vous avez reçu une nouvelle candidature pour l'offre suivante :</p>

    <p class="lead">{{ $offer->title }}</p>
    <p class="meta">
        {{ $offer->company->name }}
        @if($offer->location)
            · {{ $offer->location }}
        @endif
        · {{ $offer->type->label() }}
    </p>

    <p>
        <strong>{{ $candidate->name }}</strong>
        (<a href="mailto:{{ $candidate->email }}" style="color:#FF6600;">{{ $candidate->email }}</a>)
        @if($candidate->github_username)
            · <a href="{{ $candidate->githubUrl() }}" style="color:#FF6600;">@{{ $candidate->github_username }}</a>
        @endif
    </p>

    @if($application->cover_letter)
        <div class="panel">
            <h2>Lettre de motivation</h2>
            <p>{{ $application->cover_letter }}</p>
        </div>
    @endif

    <a href="{{ route('jobs.show', $offer) }}" class="btn">
        Voir l'offre
    </a>
@endsection
