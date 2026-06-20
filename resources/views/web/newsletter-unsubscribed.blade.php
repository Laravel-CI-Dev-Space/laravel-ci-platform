@extends('layouts.web')

@section('title', 'Désabonnement — Laravel CI')
@section('robots', 'noindex, nofollow')

@section('content')
  <section class="section" style="min-height:60vh;display:flex;align-items:center">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-5 text-center">
          <img src="{{ asset('assets/web/img/mascot.png') }}" alt="" style="height:100px;margin-bottom:24px;opacity:.7" />
          <h1 style="font-size:1.75rem;margin-bottom:12px">Tu t'es désabonné</h1>
          <p class="text-muted mb-4">Tu ne recevras plus nos emails. Tu peux te réabonner à tout moment depuis n'importe quelle page du site.</p>
          <a href="{{ route('home') }}" class="btn btn-outline-navy">Retour à l'accueil</a>
        </div>
      </div>
    </div>
  </section>
@endsection
