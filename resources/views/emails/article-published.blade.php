@extends('emails.layouts.base')

@section('subject', 'Votre article a été publié — Laravel CI')

@section('content')

    <p class="greeting">
        Félicitations, <span>{{ $user->name }}</span> !
    </p>

    <p class="text">
        Votre article <strong>« {{ $article->title }} »</strong> vient d'être validé et publié
        sur le blog de la communauté Laravel Côte d'Ivoire.
    </p>

    <div class="info-box">
        <div class="info-row">
            <strong>Titre</strong>
            <span>{{ $article->title }}</span>
        </div>
        <div class="info-row">
            <strong>Niveau</strong>
            <span class="badge">{{ \App\Models\Article::$levelLabels[$article->level] ?? $article->level }}</span>
        </div>
    </div>

    <div class="cta-primary">
        <a href="{{ route('blog.show', $article->slug) }}">Voir mon article publié &rarr;</a>
    </div>

    <p class="text">
        Merci de partager vos connaissances avec la communauté ! Continuez ainsi —
        chaque article aide d'autres développeurs Laravel ivoiriens à progresser. 🇨🇮
    </p>

@endsection
