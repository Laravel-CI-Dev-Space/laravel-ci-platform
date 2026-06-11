@extends('layouts.web')

@section('title', ($member->name ?? 'Member') . ' — Laravel CI')

@section('content')

  <section class="page-hero">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-8">
          <div class="breadcrumb-bar">
            <a href="{{ route('home') }}">Home</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>{{ $member->name ?? 'Member profile' }}</span>
          </div>
          <div class="d-flex align-items-center gap-4 flex-wrap mt-3">
            @if($member->avatar ?? null)
              <img src="{{ $member->avatar }}" alt="{{ $member->name }}" class="avatar avatar-xl rounded-circle" style="width:80px;height:80px;object-fit:cover" />
            @else
              <span class="avatar avatar-xl av-1" style="font-size:1.8rem;width:80px;height:80px">{{ substr($member->name ?? 'U', 0, 2) }}</span>
            @endif
            <div>
              <h1 class="mb-1" style="font-size:var(--fs-h2)">{{ $member->name ?? 'Serge Brou' }}</h1>
              <div class="d-flex flex-wrap gap-3" style="font-size:.92rem;color:var(--muted)">
                @if($member->github_username ?? null)
                  <span><i class="fa-brands fa-github me-1 text-orange"></i> {{ $member->github_username }}</span>
                @endif
                @if($member->location ?? null)
                  <span><i class="fa-solid fa-location-dot me-1 text-orange"></i> {{ $member->location }}</span>
                @endif
                <span><i class="fa-solid fa-star me-1 text-orange"></i> {{ number_format($member->reputation ?? 0) }} pts</span>
                @if($member->grade ?? null)
                  <span class="badge rounded-pill" style="background-color: {{ $member->grade->color }}1a; color: {{ $member->grade->color }}; border: 1px solid {{ $member->grade->color }}">
                    <i class="{{ $member->grade->icon }} me-1"></i> {{ $member->grade->name }}
                  </span>
                @endif
                <span><i class="fa-solid fa-calendar me-1 text-orange"></i> Member since {{ $member->created_at?->format('M Y') ?? 'Jan 2026' }}</span>
              </div>
            </div>
          </div>
          @if($member->bio ?? null)
            <p class="lead mt-3 mb-0">{{ $member->bio }}</p>
          @endif
          @if($member->github_username ?? null)
            <div class="d-flex gap-2 mt-3">
              <a href="https://github.com/{{ $member->github_username }}" target="_blank" class="btn btn-github btn-sm">
                <i class="fa-brands fa-github"></i> {{ '@' . $member->github_username }}
              </a>
            </div>
          @endif
        </div>
        <div class="col-lg-4 d-none d-lg-block">
          <div class="mascot-art" style="width:clamp(130px,12vw,170px)">
            <span class="m-ring"></span><span class="m-blob"></span>
            <img src="{{ asset('assets/web/img/mascot.png') }}" alt="" />
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section-sm">
    <div class="container">
      <div class="row g-4">
        <!-- STATS -->
        <div class="col-12">
          <div class="row g-3">
            <div class="col-6 col-md-3">
              <div class="card-soft text-center p-3">
                <div class="stat-num" style="font-size:1.8rem;font-weight:700;color:var(--navy)">{{ $member->questions_count ?? 0 }}</div>
                <div class="text-muted-2">Questions asked</div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="card-soft text-center p-3">
                <div class="stat-num" style="font-size:1.8rem;font-weight:700;color:var(--navy)">{{ $member->answers_count ?? 0 }}</div>
                <div class="text-muted-2">Answers given</div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="card-soft text-center p-3">
                <div class="stat-num" style="font-size:1.8rem;font-weight:700;color:var(--navy)">{{ $member->articles_count ?? 0 }}</div>
                <div class="text-muted-2">Articles written</div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="card-soft text-center p-3">
                <div class="stat-num" style="font-size:1.8rem;font-weight:700;color:var(--orange)">{{ number_format($member->reputation ?? 0) }}</div>
                <div class="text-muted-2">Reputation</div>
              </div>
            </div>
          </div>
        </div>

        <!-- RECENT QUESTIONS -->
        <div class="col-lg-8">
          <h2 class="section-heading mb-4" style="font-size:var(--fs-h3)">Recent questions</h2>
          @forelse($member->questions ?? [] as $question)
            <div class="q-card">
              <div class="q-stats">
                <div class="q-vote"><span>{{ $question->votes_count }}</span><small>votes</small></div>
                <div class="q-answers {{ $question->is_solved ? 'accepted' : '' }}"><strong>{{ $question->answers_count }}</strong>answers</div>
              </div>
              <div class="q-body">
                <h3 class="q-title"><a href="{{ route('forum.show', $question) }}">{{ $question->title }}</a></h3>
                <div class="q-tags">
                  @foreach($question->tags as $tag)
                    <span class="tag">{{ $tag->name }}</span>
                  @endforeach
                </div>
                <div class="q-foot">
                  <span></span>
                  <span class="read-time"><i class="fa-regular fa-clock"></i> {{ $question->created_at->diffForHumans() }}</span>
                </div>
              </div>
            </div>
          @empty
            <x-web.empty-state title="No questions yet" message="This member hasn't asked any questions yet." />
          @endforelse
        </div>

        <!-- SIDEBAR -->
        <div class="col-lg-4">
          <div class="sidebar-card">
            <div class="sidebar-title">Recent articles</div>
            @forelse($member->articles ?? [] as $article)
              <div class="tag-list-item">
                <a href="{{ route('blog.show', $article) }}" style="font-weight:500">{{ $article->title }}</a>
              </div>
            @empty
              <p class="text-muted-2 mb-0" style="font-size:.9rem">No articles yet.</p>
            @endforelse
          </div>
          @if($member->skills ?? null)
            <div class="sidebar-card">
              <div class="sidebar-title">Skills</div>
              <div class="d-flex flex-wrap gap-2">
                @foreach($member->skills as $skill)
                  <span class="tag">{{ $skill }}</span>
                @endforeach
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </section>

@endsection
