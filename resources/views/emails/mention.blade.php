@extends('emails.layouts.base')

@section('subject', "{$author->name} vous a mentionné — Laravel CI")

@section('content')

    <p class="greeting">
        Bonjour, <span>{{ $mentionedUser->name }}</span> !
    </p>

    <p class="text">
        <strong>{{ $author->name }}</strong> vous a mentionné {{ $context }} sur Laravel CI.
    </p>

    <div class="info-box">
        <h3>Extrait du message</h3>
        <p class="text" style="margin-bottom:0;">
            {{ $excerpt }}
        </p>
    </div>

    <div class="cta-primary">
        <a href="{{ $url }}">Voir le message &rarr;</a>
    </div>

@endsection
