@extends('layouts.dashboard')

@section('title', 'My Dashboard')

@section('content')

@php
  /** @var \App\Models\User $me */
  $me = auth()->user();

  $questionCount   = $me->questions()->count();
  $answerCount     = $me->answers()->count();
  $articleCount    = $me->articles()->where('status', 'published')->count();
  $votesScore      = $me->questions()->sum('votes_score');

  $recentQuestions = $me->questions()->latest()->take(5)->get();

  $recentArticles  = $me->articles()->latest()->take(5)->get();

  $upcomingRegs    = $me->eventRegistrations()
                        ->with('event')
                        ->whereHas('event', fn($q) => $q->where('starts_at', '>', now()))
                        ->latest()
                        ->take(3)
                        ->get();

  $recentApps      = $me->jobApplications()
                        ->with(['jobOffer', 'jobOffer.company'])
                        ->latest()
                        ->take(5)
                        ->get();
@endphp

  <x-dashboard.breadcrumb :items="[['label' => 'Dashboard', 'href' => route('dashboard.member.overview')], ['label' => 'Overview']]" />

  <div class="row mb-4">
    <div class="col-12">
      <h1 class="fs-4 fw-bold mb-1">Welcome back, {{ $me->name }} 👋</h1>
      <p class="text-secondary mb-0">Here's your activity on Laravel CI.</p>
    </div>
  </div>

  {{-- LIVEWIRE: @livewire('dashboard.member-overview') --}}

  {{-- ── Stat cards ────────────────────────────────────── --}}
  <div class="row g-3 mb-4">
    <div class="col-lg-3 col-sm-6 col-12">
      <x-dashboard.stat-card
        title="Questions"
        :value="$questionCount"
        change="asked"
        icon="ti ti-message-circle-question"
        color="primary"
      />
    </div>
    <div class="col-lg-3 col-sm-6 col-12">
      <x-dashboard.stat-card
        title="Answers"
        :value="$answerCount"
        change="given"
        icon="ti ti-message-check"
        color="success"
      />
    </div>
    <div class="col-lg-3 col-sm-6 col-12">
      <x-dashboard.stat-card
        title="Articles"
        :value="$articleCount"
        change="published"
        icon="ti ti-file-text"
        color="info"
      />
    </div>
    <div class="col-lg-3 col-sm-6 col-12">
      <x-dashboard.stat-card
        title="Votes received"
        :value="$votesScore"
        change="on questions"
        icon="ti ti-star"
        color="warning"
      />
    </div>
  </div>

  {{-- ── Recent activity ───────────────────────────────── --}}
  <div class="row g-3">

    {{-- Questions --}}
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
          <h5 class="mb-0">My Recent Questions</h5>
          <a href="{{ route('dashboard.member.questions') }}" class="small text-primary">View all</a>
        </div>
        <ul class="list-group list-group-flush">
          @forelse($recentQuestions as $question)
            <li class="list-group-item px-4 py-3">
              <a href="{{ route('forum.show', $question->slug) }}" class="fw-semibold text-dark text-decoration-none d-block mb-1"
                 style="font-size:.9rem">{{ Str::limit($question->title, 65) }}</a>
              <div class="d-flex align-items-center gap-2 text-secondary" style="font-size:.78rem">
                <span class="{{ $question->hasAcceptedAnswer() ? 'text-success' : 'text-muted' }}">
                  <i class="ti ti-{{ $question->hasAcceptedAnswer() ? 'circle-check-filled' : 'circle' }}"></i>
                  {{ $question->hasAcceptedAnswer() ? 'Answered' : 'Open' }}
                </span>
                <span>·</span>
                <span><i class="ti ti-messages"></i> {{ $question->answers()->count() }}</span>
                <span>·</span>
                <span>{{ $question->created_at->diffForHumans() }}</span>
              </div>
            </li>
          @empty
            <li class="list-group-item px-4 py-5 text-center text-secondary">
              <i class="ti ti-message-circle-question d-block mb-2" style="font-size:1.8rem"></i>
              No questions yet.
              <a href="{{ route('forum.index') }}" class="text-primary text-decoration-none">Ask your first one!</a>
            </li>
          @endforelse
        </ul>
      </div>
    </div>

    {{-- Articles --}}
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
          <h5 class="mb-0">My Articles</h5>
          <a href="{{ route('dashboard.member.articles') }}" class="small text-primary">View all</a>
        </div>
        <ul class="list-group list-group-flush">
          @forelse($recentArticles as $article)
            <li class="list-group-item d-flex align-items-center gap-3 px-4 py-3">
              <div class="flex-grow-1">
                <a href="{{ route('blog.show', $article->slug) }}" class="fw-semibold text-dark text-decoration-none d-block mb-1"
                   style="font-size:.9rem">{{ Str::limit($article->title, 55) }}</a>
                <div class="text-secondary" style="font-size:.78rem">
                  {{ ucfirst($article->level ?? 'beginner') }}
                  · {{ $article->created_at->diffForHumans() }}
                </div>
              </div>
              @php
                $badge = match($article->status) {
                  'published' => ['bg-success-subtle text-success', 'Published'],
                  'pending'   => ['bg-warning-subtle text-warning', 'Pending'],
                  'rejected'  => ['bg-danger-subtle text-danger', 'Rejected'],
                  default     => ['bg-secondary-subtle text-secondary', 'Draft'],
                };
              @endphp
              <span class="badge {{ $badge[0] }} small">{{ $badge[1] }}</span>
            </li>
          @empty
            <li class="list-group-item px-4 py-5 text-center text-secondary">
              <i class="ti ti-file-text d-block mb-2" style="font-size:1.8rem"></i>
              No articles yet.
              <a href="{{ route('dashboard.member.articles') }}" class="text-primary text-decoration-none">Write your first!</a>
            </li>
          @endforelse
        </ul>
      </div>
    </div>

    {{-- Upcoming events --}}
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
          <h5 class="mb-0">My Upcoming Events</h5>
          <a href="{{ route('dashboard.member.events') }}" class="small text-primary">View all</a>
        </div>
        <ul class="list-group list-group-flush">
          @forelse($upcomingRegs as $reg)
            @php $event = $reg->event; @endphp
            <li class="list-group-item d-flex align-items-center gap-3 px-4 py-3">
              <div class="text-center bg-primary-subtle rounded p-2" style="min-width:48px">
                <div class="fw-bold text-primary" style="font-size:.7rem;text-transform:uppercase">
                  {{ $event->starts_at->format('M') }}
                </div>
                <div class="fw-bold text-primary" style="font-size:1.1rem;line-height:1">
                  {{ $event->starts_at->format('d') }}
                </div>
              </div>
              <div class="flex-grow-1">
                <a href="{{ route('events.show', $event->slug) }}" class="fw-semibold text-dark text-decoration-none d-block"
                   style="font-size:.9rem">{{ Str::limit($event->title, 55) }}</a>
                <div class="text-secondary" style="font-size:.78rem">
                  {{ $event->location ?? 'Online' }} · {{ $event->starts_at->format('H:i') }}
                </div>
              </div>
            </li>
          @empty
            <li class="list-group-item px-4 py-5 text-center text-secondary">
              <i class="ti ti-calendar-event d-block mb-2" style="font-size:1.8rem"></i>
              No upcoming events.
              <a href="{{ route('events.index') }}" class="text-primary text-decoration-none">Browse events!</a>
            </li>
          @endforelse
        </ul>
      </div>
    </div>

    {{-- Job applications --}}
    <div class="col-lg-6">
      <div class="card h-100">
        <div class="card-header bg-white d-flex justify-content-between align-items-center px-4 py-3">
          <h5 class="mb-0">My Applications</h5>
          <a href="{{ route('dashboard.member.applications') }}" class="small text-primary">View all</a>
        </div>
        <ul class="list-group list-group-flush">
          @forelse($recentApps as $application)
            <li class="list-group-item d-flex align-items-center gap-3 px-4 py-3">
              <div class="flex-grow-1">
                <a href="{{ route('jobs.show', $application->jobOffer->slug) }}"
                   class="fw-semibold text-dark text-decoration-none d-block mb-1"
                   style="font-size:.9rem">{{ Str::limit($application->jobOffer->title, 55) }}</a>
                <div class="text-secondary" style="font-size:.78rem">
                  {{ $application->jobOffer->company->name ?? '—' }}
                  · {{ $application->created_at->diffForHumans() }}
                </div>
              </div>
              @php
                $appBadge = match($application->status) {
                  'pending'     => ['bg-warning-subtle text-warning', 'Pending'],
                  'viewed'      => ['bg-info-subtle text-info', 'Viewed'],
                  'shortlisted' => ['bg-primary-subtle text-primary', 'Shortlisted'],
                  'accepted'    => ['bg-success-subtle text-success', 'Accepted'],
                  'rejected'    => ['bg-danger-subtle text-danger', 'Rejected'],
                  default       => ['bg-secondary-subtle text-secondary', ucfirst($application->status)],
                };
              @endphp
              <span class="badge {{ $appBadge[0] }}">{{ $appBadge[1] }}</span>
            </li>
          @empty
            <li class="list-group-item px-4 py-5 text-center text-secondary">
              <i class="ti ti-briefcase d-block mb-2" style="font-size:1.8rem"></i>
              No applications yet.
              <a href="{{ route('jobs.index') }}" class="text-primary text-decoration-none">Browse jobs!</a>
            </li>
          @endforelse
        </ul>
      </div>
    </div>

  </div>{{-- /row activity --}}

@endsection
