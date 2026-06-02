@extends('layouts.dashboard')

@section('title', 'My Events')

@section('content')

  <x-dashboard.breadcrumb :items="[['label' => 'Dashboard', 'href' => route('dashboard.member.overview')], ['label' => 'My Events']]" />

  <div class="row">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="fs-3 mb-1">My Events</h1>
          <p class="mb-0 text-muted">Events you have registered for.</p>
        </div>
        <a href="#" class="btn btn-primary"><i class="ti ti-calendar-event me-1"></i> Browse Events</a>
      </div>
    </div>
  </div>

  <div class="row g-3">
    @forelse($events ?? [] as $event)
      <div class="col-md-6 col-lg-4">
        <div class="card h-100">
          <div class="card-body p-4">
            <div class="d-flex gap-3 mb-3">
              <div class="event-date-chip">
                <div class="m">{{ $event->date->format('M') }}</div>
                <div class="d">{{ $event->date->format('d') }}</div>
              </div>
              <div>
                <span class="badge {{ $event->isPast() ? 'bg-secondary' : 'bg-primary' }}-subtle text-{{ $event->isPast() ? 'secondary' : 'primary' }}">
                  {{ $event->isPast() ? 'Past' : 'Upcoming' }}
                </span>
                <h3 class="mt-1 mb-0" style="font-size:1rem"><a href="#" class="text-navy">{{ $event->title }}</a></h3>
              </div>
            </div>
            <div class="text-secondary small mb-2">
              <div><i class="ti ti-clock me-1"></i> {{ $event->time_label ?? $event->date->format('g:i A') }}</div>
              <div><i class="ti ti-map-pin me-1"></i> {{ $event->location }}</div>
            </div>
            <a href="#" class="btn btn-outline-primary btn-sm w-100 mt-2">View Event</a>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="card">
          <div class="card-body py-5 text-center text-secondary">
            <i class="ti ti-calendar-event fs-1 d-block mb-3"></i>
            <h3 class="h5">No events registered</h3>
            <p class="mb-3">You haven't registered for any events yet.</p>
            <a href="#" class="btn btn-primary">Browse Events</a>
          </div>
        </div>
      </div>
    @endforelse
  </div>

@endsection
