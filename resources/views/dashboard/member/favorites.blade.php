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

  <div class="row g-3">
    @forelse($favorites as $job)
      @php $card = $job->toWebCardProps(); @endphp
      <div class="col-12">
        <div class="card">
          <div class="card-body p-4">
            <div class="d-flex gap-3 align-items-start">
              <div class="company-logo {{ $card['logoClass'] }}">{{ $card['logoText'] }}</div>
              <div class="flex-grow-1 min-w-0">
                <div>
                  <h3 class="mb-1" style="font-size:1.1rem">
                    <a href="{{ route('jobs.show', $job) }}" class="text-navy">{{ $card['title'] }}</a>
                  </h3>
                  <div class="text-secondary small">
                    <strong>{{ $card['company'] }}</strong> · {{ $card['location'] }}
                    @if($card['remote'])
                      · <span class="badge bg-primary-subtle text-primary">Télétravail</span>
                    @endif
                  </div>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2">
                  <span class="badge-pill badge-soft">{{ $card['contractType'] }}</span>
                  @foreach($card['tags'] as $tag)
                    <span class="tag">{{ $tag }}</span>
                  @endforeach
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                  <span class="salary">{{ $card['salary'] }}</span>
                  <a href="{{ route('jobs.show', $job) }}" class="btn btn-primary btn-sm">
                    Postuler <i class="ti ti-arrow-right"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="card">
          <div class="card-body py-5 text-center text-secondary">
            <i class="ti ti-heart fs-1 d-block mb-3"></i>
            <h3 class="h5">Aucune offre sauvegardée</h3>
            <p class="mb-3">Enregistrez les offres qui vous intéressent.</p>
            <a href="{{ route('jobs.index') }}" class="btn btn-primary">Parcourir les offres</a>
          </div>
        </div>
      </div>
    @endforelse
  </div>

@endsection
