@extends('layouts.dashboard')

@section('title', 'Mes événements')

@section('content')

  <x-dashboard.breadcrumb :items="[['label' => 'Tableau de bord', 'href' => route('dashboard.member.overview')], ['label' => 'Mes événements']]" />

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="ti ti-circle-check me-1"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
    </div>
  @endif

  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="fs-3 mb-1">Mes événements</h1>
          <p class="mb-0 text-muted">Gérez vos inscriptions, rappels et exports calendrier.</p>
        </div>
        <a href="{{ route('events.index') }}" class="btn btn-primary"><i class="ti ti-calendar-event me-1"></i> Parcourir les événements</a>
      </div>
    </div>
  </div>

  <div class="row g-3">
    @forelse($registrations as $registration)
      <div class="col-md-6 col-xl-4">
        <livewire:dashboard.registered-event-card
          :registration-id="$registration->id"
          :key="'member-event-card-'.$registration->id"
        />
      </div>
    @empty
      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body py-5 text-center text-secondary">
            <i class="ti ti-calendar-event fs-1 d-block mb-3"></i>
            <h3 class="h5">Aucune inscription</h3>
            <p class="mb-3">Vous n'êtes inscrit à aucun événement pour le moment.</p>
            <a href="{{ route('events.index') }}" class="btn btn-primary">Parcourir les événements</a>
          </div>
        </div>
      </div>
    @endforelse
  </div>

@endsection
