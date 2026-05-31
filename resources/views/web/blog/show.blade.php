@extends('layouts.web')

@section('title', ($article->title ?? 'Article') . ' — Laravel CI')

@section('content')

  <!-- ============ COVER ============ -->
  <header class="page-hero">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-8">
          <div class="breadcrumb-bar">
            <a href="{{ route('home') }}">Home</a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('blog.index') }}">Blog</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>{{ $article->tags->first()->name ?? 'Payments' }}</span>
          </div>
          <x-web.level-badge :level="$article->level ?? 'Intermediate'" />
          <h1 class="mt-3">{{ $article->title ?? 'Building a Wave payment integration the clean way' }}</h1>
          <div class="cover-meta">
            <div class="author-row">
              <span class="avatar av-1">{{ substr($article->author->name ?? 'SB', 0, 2) }}</span>
              <div class="meta">
                <div class="name">{{ $article->author->name ?? 'Serge Brou' }}</div>
                <div class="sub">{{ $article->author->title ?? 'Software Architect' }}</div>
              </div>
            </div>
            <span class="text-muted-2"><i class="fa-regular fa-calendar me-1 text-orange"></i> {{ $article->published_at?->format('M d, Y') ?? 'May 24, 2026' }}</span>
            <span class="text-muted-2"><i class="fa-regular fa-clock me-1 text-orange"></i> {{ $article->reading_time ?? '11' }} min read</span>
          </div>
        </div>
        <div class="col-lg-4 d-none d-lg-block">
          <div class="mascot-art">
            <span class="m-ring"></span><span class="m-blob"></span>
            <img src="{{ asset('assets/web/img/mascot.png') }}" alt="Laravel CI mascot" />
          </div>
        </div>
      </div>
    </div>
  </header>

  <section class="section">
    <div class="container">
      <div class="row g-4 gx-lg-5">
        <!-- TOC -->
        <div class="col-lg-4 order-lg-2">
          <div class="toc-card">
            <div class="sidebar-title">On this page</div>
            @foreach($article->toc ?? [] as $item)
              <a href="#{{ $item['slug'] }}">{{ $item['title'] }}</a>
            @endforeach
            @if(empty($article->toc ?? []))
              <a href="#intro">Why an abstraction layer?</a>
              <a href="#contract">Defining the contract</a>
              <a href="#driver">Implementing the Wave driver</a>
              <a href="#webhooks">Handling webhooks</a>
              <a href="#idempotency">Idempotency &amp; retries</a>
            @endif
            <div class="mt-3 pt-3" style="border-top:1px solid var(--border)">
              <div class="d-flex gap-2">
                <a href="#" class="social-icon" style="background:var(--light);color:var(--navy)" aria-label="Share on X"><i class="fa-brands fa-x-twitter"></i></a>
                <a href="#" class="social-icon" style="background:var(--light);color:var(--navy)" aria-label="Share on LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                <a href="#" class="social-icon" style="background:var(--light);color:var(--navy)" aria-label="Copy link"><i class="fa-solid fa-link"></i></a>
              </div>
            </div>
          </div>
        </div>

        <!-- BODY -->
        <div class="col-lg-8 order-lg-1">
          <article class="article-body">
            {!! $article->body_html ?? '<h2 id="intro">Why an abstraction layer?</h2><p>In Côte d\'Ivoire, most apps need to support more than one mobile money provider — Wave, Orange Money, MTN MoMo and Moov all coexist. If you scatter provider-specific calls across your controllers, swapping or adding a provider becomes a nightmare.</p>' !!}
          </article>

          <!-- Author bio -->
          <div class="author-bio">
            <span class="avatar avatar-lg av-1">{{ substr($article->author->name ?? 'SB', 0, 2) }}</span>
            <div>
              <h3 style="font-size:1.1rem;margin-bottom:.3rem" class="text-navy">{{ $article->author->name ?? 'Serge Brou' }}</h3>
              <p class="mb-2" style="font-size:.92rem;color:var(--muted)">{{ $article->author->bio ?? 'Software architect in Abidjan, payments nerd, and a core maintainer of the Laravel CI community.' }}</p>
              <a href="{{ route('members.show', $article->author->github_username ?? '#') }}" class="btn btn-ghost btn-sm"><i class="fa-brands fa-github"></i> Follow</a>
            </div>
          </div>

          <!-- Comments -->
          {{-- LIVEWIRE: @livewire('blog.comment-section', ['article' => $article]) --}}
          <div class="mt-5">
            <h2 class="section-heading mb-4" style="font-size:var(--fs-h3)">Comments ({{ $article->comments_count ?? 3 }})</h2>
            @auth
              <div class="card-soft" style="padding:1.3rem">
                <textarea class="form-control mb-3" rows="3" placeholder="Add a thoughtful comment…" style="border-radius:var(--radius)"></textarea>
                <div class="text-end"><button class="btn btn-brand"><i class="fa-solid fa-paper-plane"></i> Post comment</button></div>
              </div>
            @else
              <div class="card-soft p-4 text-center">
                <p class="mb-3">Sign in to leave a comment.</p>
                <a href="{{ route('login') }}" class="btn btn-brand"><i class="fa-brands fa-github"></i> Sign in</a>
              </div>
            @endauth

            @foreach($article->comments ?? [] as $comment)
              <div class="comment">
                <span class="avatar av-3">{{ substr($comment->author->name ?? 'FD', 0, 2) }}</span>
                <div class="c-body">
                  <div class="c-head"><span class="name" style="font-weight:600">{{ $comment->author->name ?? 'Fatou Diallo' }}</span><span class="sub" style="font-size:.8rem;color:var(--muted)">{{ $comment->created_at?->diffForHumans() ?? '3 days ago' }}</span></div>
                  <p class="mb-1">{{ $comment->body ?? 'Great article!' }}</p>
                  <a href="#" class="text-muted-2" style="font-size:.82rem"><i class="fa-regular fa-thumbs-up me-1"></i> {{ $comment->likes_count ?? 0 }}</a>
                </div>
              </div>
            @endforeach

            @if(empty($article->comments ?? []))
              <div class="comment">
                <span class="avatar av-3">FD</span>
                <div class="c-body">
                  <div class="c-head"><span class="name" style="font-weight:600">Fatou Diallo</span><span class="sub" style="font-size:.8rem;color:var(--muted)">3 days ago</span></div>
                  <p class="mb-1">This is exactly the pattern we use in production. One addition: cache the access token so you're not authenticating on every charge.</p>
                  <a href="#" class="text-muted-2" style="font-size:.82rem"><i class="fa-regular fa-thumbs-up me-1"></i> 12</a>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>

      <!-- Related -->
      <div class="mt-5 pt-4">
        <h2 class="section-heading mb-4">Related articles</h2>
        <div class="row g-4">
          @foreach($relatedArticles ?? [] as $related)
            <div class="col-md-4 reveal">
              <article class="card-soft article-card">
                <div class="level-banner lv-{{ strtolower($related->level) }}"></div>
                <div class="card-pad">
                  <x-web.level-badge :level="$related->level" />
                  <h3 class="art-title"><a href="{{ route('blog.show', $related) }}">{{ $related->title }}</a></h3>
                  <div class="art-foot">
                    <div class="author-row"><span class="avatar avatar-sm av-4">{{ substr($related->author->name, 0, 2) }}</span><div class="meta"><div class="name">{{ $related->author->name }}</div></div></div>
                    <span class="read-time"><i class="fa-regular fa-clock"></i> {{ $related->reading_time }} min</span>
                  </div>
                </div>
              </article>
            </div>
          @endforeach
          @if(empty($relatedArticles ?? []))
            <div class="col-md-4 reveal">
              <article class="card-soft article-card"><div class="level-banner lv-beginner"></div><div class="card-pad"><span class="badge-pill badge-green"><span class="lv-dot" style="background:var(--green)"></span> Beginner</span><h3 class="art-title"><a href="{{ route('blog.index') }}">Understanding Eloquent relationships with real examples</a></h3><div class="art-foot"><div class="author-row"><span class="avatar avatar-sm av-4">YT</span><div class="meta"><div class="name">Yao Térence</div></div></div><span class="read-time"><i class="fa-regular fa-clock"></i> 8 min</span></div></div></article>
            </div>
          @endif
        </div>
      </div>
    </div>
  </section>

@endsection
