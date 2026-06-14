@extends('layouts.dashboard')

@section('title', 'Alertes emploi')

@section('content')

  <x-dashboard.breadcrumb :items="[['label' => 'Tableau de bord', 'href' => route('dashboard.member.overview')], ['label' => 'Alertes emploi']]" />

  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="fs-3 mb-1">Alertes emploi</h1>
          <p class="mb-0 text-muted">Soyez notifié par email lorsqu'une offre correspond à vos critères.</p>
        </div>
        <a href="{{ route('jobs.index') }}" class="btn btn-outline-primary"><i class="ti ti-briefcase me-1"></i> Parcourir les offres</a>
      </div>
    </div>
  </div>

  <livewire:dashboard.member-job-alerts />

@endsection
