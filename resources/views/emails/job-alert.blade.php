@extends('emails.layouts.base')

@section('subject', 'Nouvelles offres correspondant à votre profil — Laravel CI')

@section('content')

    <p class="greeting">
        Bonjour, <span>{{ $user->name }}</span> !
    </p>

    <p class="text">
        De nouvelles offres d'emploi correspondant à votre profil viennent d'être publiées
        sur le Job Board de Laravel Côte d'Ivoire.
    </p>

    @foreach($offers as $offer)
        <div class="offer-card">
            <h4>{{ $offer->title }}</h4>
            <div class="meta">
                {{ $offer->company?->name }} ·
                {{ $offer->contract_type->label() }} ·
                {{ $offer->level->label() }}
                @if($offer->is_remote) · Remote @endif
            </div>
            <a href="{{ route('jobs.show', $offer->slug) }}">Voir l'offre &rarr;</a>
        </div>
    @endforeach

    <div class="cta-primary">
        <a href="{{ route('jobs.index') }}">Voir toutes les offres &rarr;</a>
    </div>

    <p class="text" style="font-size:0.85rem; color:#888;">
        Vous recevez cet email car vous avez activé les alertes emploi.
        Vous pouvez ajuster vos préférences à tout moment depuis votre profil.
    </p>

    <div class="cta-secondary">
        <a href="{{ route('profile.edit') }}">Gérer mes préférences &rarr;</a>
    </div>

@endsection
