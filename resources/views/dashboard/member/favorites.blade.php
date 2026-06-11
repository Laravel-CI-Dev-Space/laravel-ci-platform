@extends('layouts.dashboard')

@section('title', 'Offres sauvegardées')

@section('content')

  <x-dashboard.breadcrumb :items="[['label' => 'Tableau de bord', 'href' => route('dashboard.member.overview')], ['label' => 'Offres sauvegardées']]" />

  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="fs-3 mb-1">Offres sauvegardées</h1>
          <p class="mb-0 text-muted">Les offres que vous avez mises de côté.</p>
        </div>
        <a href="{{ route('jobs.index') }}" class="btn btn-primary"><i class="ti ti-briefcase me-1"></i> Parcourir les offres</a>
      </div>
    </div>
  </div>

  <livewire:dashboard.member-job-favorites />

@endsection
