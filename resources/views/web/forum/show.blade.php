@extends('layouts.web')

@section('title', ($question->title ?? 'Question') . ' — Laravel CI Forum')

@section('content')

  <section class="page-hero">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-9">
          <div class="breadcrumb-bar">
            <a href="{{ route('home') }}">Home</a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('forum.index') }}">Forum</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>{{ $question->tags->first()->name ?? 'Question' }}</span>
          </div>
          <h1 class="mb-3" style="font-size:var(--fs-h1)">{{ $question->title ?? 'How to structure a multi-tenant Laravel app for a fintech in Abidjan?' }}</h1>
          <div class="d-flex flex-wrap gap-3 align-items-center" style="font-size:.88rem;color:var(--muted)">
            <span><i class="fa-regular fa-clock me-1"></i> Asked {{ $question->created_at?->diffForHumans() ?? '2 hours ago' }}</span>
            <span><i class="fa-regular fa-eye me-1"></i> {{ number_format($question->views_count ?? 1204) }} views</span>
            @if($question->is_solved ?? true)
              <span><i class="fa-solid fa-check text-green me-1"></i> Solved</span>
            @endif
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

  <section class="section-sm">
    <div class="container">
      <div class="row g-4">
        <div class="col-lg-8">

          {{-- LIVEWIRE: @livewire('forum.question-detail', ['question' => $question]) --}}

          <!-- QUESTION -->
          <div class="answer-card">
            <div class="d-flex gap-4">
              {{-- LIVEWIRE: @livewire('forum.vote-buttons', ['model' => $question]) --}}
              <div class="vote-col" data-vote>
                <button class="vote-btn" data-dir="up" aria-label="Upvote"><i class="fa-solid fa-chevron-up"></i></button>
                <span class="vote-score">{{ $question->votes_count ?? 42 }}</span>
                <button class="vote-btn" data-dir="down" aria-label="Downvote"><i class="fa-solid fa-chevron-down"></i></button>
              </div>
              <div class="flex-grow-1">
                <div>{!! $question->body_html ?? '<p>We\'re building a mobile money aggregator serving multiple merchants in Abidjan. Each merchant is a tenant and must <strong>never</strong> see another merchant\'s transactions.</p>' !!}</div>
                <div class="q-tags my-3">
                  @foreach($question->tags ?? [] as $tag)
                    <span class="tag">{{ $tag->name }}</span>
                  @endforeach
                  @if(empty($question->tags ?? []))
                    <span class="tag">laravel-11</span>
                    <span class="tag">multi-tenancy</span>
                    <span class="tag">architecture</span>
                  @endif
                </div>
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pt-2">
                  <div class="d-flex gap-3" style="font-size:.85rem">
                    <a href="#" class="text-muted-2"><i class="fa-solid fa-share me-1"></i> Share</a>
                    @auth
                      <a href="#" class="text-muted-2"><i class="fa-regular fa-bookmark me-1"></i> Save</a>
                    @endauth
                  </div>
                  <div class="author-row">
                    <span class="avatar av-1">{{ substr($question->author->name ?? 'KA', 0, 2) }}</span>
                    <div class="meta">
                      <div class="name">{{ $question->author->name ?? 'Kouamé Aristide' }}</div>
                      <div class="sub">{{ $question->author->reputation ?? '2.1k' }} rep</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <h2 class="section-heading my-4" style="font-size:var(--fs-h3)">{{ $question->answers_count ?? 3 }} Answers</h2>

          @foreach($question->answers ?? [] as $answer)
            <div class="answer-card {{ $answer->is_accepted ? 'accepted' : '' }}">
              @if($answer->is_accepted)
                <span class="badge-pill badge-green mb-3"><i class="fa-solid fa-circle-check"></i> Accepted Solution</span>
              @endif
              <div class="d-flex gap-4">
                <div class="vote-col" data-vote>
                  <button class="vote-btn" data-dir="up" aria-label="Upvote"><i class="fa-solid fa-chevron-up"></i></button>
                  <span class="vote-score">{{ $answer->votes_count }}</span>
                  <button class="vote-btn" data-dir="down" aria-label="Downvote"><i class="fa-solid fa-chevron-down"></i></button>
                </div>
                <div class="flex-grow-1">
                  <div>{!! $answer->body_html !!}</div>
                  <div class="d-flex justify-content-between align-items-center gap-3 pt-2 mt-3" style="border-top:1px solid var(--border)">
                    <div class="d-flex gap-3" style="font-size:.85rem"><a href="#" class="text-muted-2"><i class="fa-solid fa-share me-1"></i> Share</a></div>
                    <div class="author-row">
                      <span class="avatar av-3">{{ substr($answer->author->name ?? 'FD', 0, 2) }}</span>
                      <div class="meta"><div class="name">{{ $answer->author->name ?? 'Fatou Diallo' }}</div><div class="sub">{{ $answer->author->reputation ?? '9.1k' }} rep</div></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          @endforeach

          @if(empty($question->answers ?? []))
            <!-- Static sample answer for display -->
            <div class="answer-card accepted">
              <span class="badge-pill badge-green mb-3"><i class="fa-solid fa-circle-check"></i> Accepted Solution</span>
              <div class="d-flex gap-4">
                <div class="vote-col" data-vote>
                  <button class="vote-btn" data-dir="up" aria-label="Upvote"><i class="fa-solid fa-chevron-up"></i></button>
                  <span class="vote-score">58</span>
                  <button class="vote-btn" data-dir="down" aria-label="Downvote"><i class="fa-solid fa-chevron-down"></i></button>
                </div>
                <div class="flex-grow-1">
                  <p>For fintech, I'd <strong>start with a single database + global scopes</strong> and only move to database-per-tenant once you hit real scaling or compliance limits.</p>
                  <div class="d-flex justify-content-end pt-2 mt-3" style="border-top:1px solid var(--border)">
                    <div class="author-row"><span class="avatar av-3">FD</span><div class="meta"><div class="name">Fatou Diallo</div><div class="sub">Lead engineer · 9.1k rep</div></div></div>
                  </div>
                </div>
              </div>
            </div>
          @endif

          <!-- ANSWER EDITOR -->
          {{-- LIVEWIRE: @livewire('forum.answer-form', ['question' => $question]) --}}
          <div class="mt-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h2 class="section-heading mb-0" style="font-size:var(--fs-h3)">Your Answer</h2>
              <button class="btn btn-ghost" id="previewToggle"><i class="fa-solid fa-eye"></i> Preview</button>
            </div>
            @auth
              <div class="answer-editor">
                <textarea class="form-control" id="answerEditor" placeholder="Share your solution. You can use Markdown and fenced code blocks…"></textarea>
                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mt-3">
                  <span class="text-muted-2" style="font-size:.82rem"><i class="fa-brands fa-markdown me-1"></i> Markdown supported · be kind and constructive</span>
                  <button class="btn btn-brand"><i class="fa-solid fa-paper-plane"></i> Post your answer</button>
                </div>
              </div>
            @else
              <div class="card-soft p-4 text-center">
                <p class="mb-3">You need to be signed in to post an answer.</p>
                <a href="{{ route('login') }}" class="btn btn-brand"><i class="fa-brands fa-github"></i> Sign in with GitHub</a>
              </div>
            @endauth
          </div>
        </div>

        <!-- SIDEBAR -->
        <div class="col-lg-4">
          <div class="sidebar-card">
            <div class="sidebar-title">Related questions</div>
            <div class="tag-list">
              <a class="tag-list-item" href="{{ route('forum.index') }}"><span style="font-weight:500">Global scopes vs query scopes — when to use which?</span></a>
              <a class="tag-list-item" href="{{ route('forum.index') }}"><span style="font-weight:500">How to test multi-tenant isolation in Pest?</span></a>
              <a class="tag-list-item" href="{{ route('forum.index') }}"><span style="font-weight:500">Sharding a Laravel app across regions</span></a>
              <a class="tag-list-item" href="{{ route('forum.index') }}"><span style="font-weight:500">Caching strategies for tenant-scoped data</span></a>
            </div>
          </div>
          <div class="sidebar-card">
            <div class="sidebar-title">Question stats</div>
            <div class="d-flex justify-content-between py-1"><span class="text-muted-2">Asked</span><strong>{{ $question->created_at?->diffForHumans() ?? '2 hours ago' }}</strong></div>
            <div class="d-flex justify-content-between py-1"><span class="text-muted-2">Viewed</span><strong>{{ number_format($question->views_count ?? 1204) }} times</strong></div>
            <div class="d-flex justify-content-between py-1"><span class="text-muted-2">Answers</span><strong>{{ $question->answers_count ?? 3 }}</strong></div>
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection
