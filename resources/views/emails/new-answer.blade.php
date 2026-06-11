@extends('emails.layouts.base')

@section('subject', 'Nouvelle réponse à votre question — Laravel CI')

@section('content')

    <p class="greeting">
        Bonjour, <span>{{ $user->name }}</span> !
    </p>

    <p class="text">
        Vous avez reçu une nouvelle réponse à votre question
        <strong>« {{ $answer->question->title }} »</strong> sur le forum Laravel CI.
    </p>

    <div class="info-box">
        <h3>Extrait de la réponse</h3>
        <p class="text" style="margin-bottom:0;">
            {{ \Illuminate\Support\Str::limit(strip_tags($answer->body), 100) }}
        </p>
    </div>

    <div class="cta-primary">
        <a href="{{ route('forum.show', $answer->question->slug) }}">Voir la réponse complète &rarr;</a>
    </div>

    <p class="text">
        Pensez à réagir et, si elle répond à votre besoin, à la marquer comme réponse acceptée.
    </p>

@endsection
