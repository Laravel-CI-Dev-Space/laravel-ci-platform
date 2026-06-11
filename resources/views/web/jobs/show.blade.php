@extends('layouts.web')

@section('title', $jobOffer->title . ' — ' . $jobOffer->company->name . ' · Laravel CI')

@push('head')
    <meta name="description" content="{{ str(strip_tags($jobOffer->description))->limit(160) }}">
    <meta property="og:title" content="{{ $jobOffer->title }} — Laravel CI">
    <meta property="og:description" content="{{ str(strip_tags($jobOffer->description))->limit(160) }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ route('jobs.show', $jobOffer) }}">
    <link rel="canonical" href="{{ route('jobs.show', $jobOffer) }}">
@endpush

@section('content')

  @php
    $logoClass = 'cl-'.((($jobOffer->id ?? 1) % 6) + 1);
    $logoText = strtoupper(substr($jobOffer->company->name, 0, 2));
    $isNew = $jobOffer->created_at && $jobOffer->created_at->diffInDays(now()) <= 7;
  @endphp

  <section class="page-hero">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-9">
          <div class="breadcrumb-bar">
            <a href="{{ route('home') }}">Accueil</a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('jobs.index') }}">Emplois</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>{{ $jobOffer->title }}</span>
          </div>
          <div class="d-flex gap-3 align-items-start flex-wrap">
            <div class="company-logo {{ $logoClass }}" style="width:68px;height:68px;font-size:1.4rem">{{ $logoText }}</div>
            <div class="flex-grow-1">
              <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                <h1 class="mb-0" style="font-size:var(--fs-h1)">{{ $jobOffer->title }}</h1>
                @if($isNew)
                  <span class="badge-pill badge-green">Nouveau</span>
                @endif
              </div>
              <div class="d-flex flex-wrap gap-3" style="color:var(--muted);font-size:.95rem">
                <span><i class="fa-solid fa-building me-1 text-orange"></i> {{ $jobOffer->company->name }}</span>
                @if($jobOffer->location)
                  <span><i class="fa-solid fa-location-dot me-1 text-orange"></i> {{ $jobOffer->location }}</span>
                @endif
                @if($jobOffer->type === \App\Enums\Jobs\JobOfferType::REMOTE)
                  <span><i class="fa-solid fa-wifi me-1 text-orange"></i> Remote</span>
                @endif
                @if($jobOffer->created_at)
                  <span><i class="fa-regular fa-clock me-1 text-orange"></i> Publié {{ $jobOffer->created_at->diffForHumans() }}</span>
                @endif
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-3 d-none d-lg-block">
          <div class="mascot-art" style="width:clamp(130px,12vw,170px)">
            <span class="m-ring"></span><span class="m-blob"></span>
            <img src="{{ asset('assets/web/img/mascot.png') }}" alt="Laravel CI mascot" />
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-8">
          <div class="prose mb-4">
            {!! nl2br(e($jobOffer->description)) !!}
          </div>

          <div class="d-flex flex-wrap gap-2 mt-2">
            <span class="badge-pill badge-soft">{{ $jobOffer->type->label() }}</span>
            @if($jobOffer->salary)
              <span class="badge-pill badge-soft">{{ $jobOffer->salary }}</span>
            @endif
            @if($jobOffer->deadline)
              <span class="badge-pill badge-orange">Date limite {{ $jobOffer->deadline->format('d/m/Y') }}</span>
            @endif
            @foreach($jobOffer->skills as $skill)
              <span class="tag">{{ $skill->name }}</span>
            @endforeach
          </div>

          @if($jobOffer->company->website)
            <a href="{{ $jobOffer->company->website }}" target="_blank" rel="noopener" class="btn btn-ghost mt-4">
              <i class="fa-solid fa-globe"></i> Site de l'entreprise
            </a>
          @endif
        </div>

        <div class="col-lg-4">
          <div class="apply-card">
            <div class="d-flex justify-content-end mb-3">
              <livewire:job-board.job-favorite-toggle :job-offer-id="$jobOffer->id" />
            </div>
            <livewire:job-board.job-application :job-offer-id="$jobOffer->id" />
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection
