@extends('emails.layouts.base')

@section('subject', "Votre demande d'inscription — Laravel CI")

@section('content')

    <p class="greeting">
        Bonjour, <span>{{ $request->first_name }} {{ $request->last_name }}</span>
    </p>

    <p class="text">
        Nous vous remercions pour l'intérêt porté à <strong>Laravel Côte d'Ivoire</strong> et
        pour votre demande d'inscription au nom de <strong>{{ $request->company_name }}</strong>.
    </p>

    <p class="text">
        Après examen, nous ne sommes malheureusement pas en mesure de valider cette demande
        pour le moment.
    </p>

    <div class="info-box">
        <h3>Motif</h3>
        <p class="text" style="margin-bottom:0;">
            {{ $reason }}
        </p>
    </div>

    <p class="text">
        Vous pouvez corriger les éléments mentionnés ci-dessus et soumettre une nouvelle
        demande d'inscription à tout moment.
    </p>

    <div class="cta-primary">
        <a href="{{ route('company.register') }}">Soumettre une nouvelle demande &rarr;</a>
    </div>

    <p class="text">
        Pour toute question, n'hésitez pas à nous contacter à
        <a href="mailto:contact@laravelci.com" style="color:#FF6600;">contact@laravelci.com</a>.
    </p>

@endsection
