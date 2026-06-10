@extends('layouts.web')

@section('title', 'Sign in — Laravel CI')
@section('description', 'Sign in to Laravel Côte d\'Ivoire with your GitHub account to access the forum, events, jobs, and your developer profile.')
@section('robots', 'noindex, nofollow')

@section('content')

  <div class="auth-wrap">
    <!-- LEFT CARD -->
    <div class="auth-left">
      <div class="auth-card">
        <div class="logo-row">
          <span class="brand-mark"><img src="{{ asset('assets/web/img/logo-mark.png') }}" alt="Laravel CI" /></span>
          <span style="font-weight:700;font-size:1.3rem;color:var(--navy)">Laravel CI</span>
        </div>
        <h1 class="mb-2" style="font-size:var(--fs-h2)">Welcome back</h1>
        <p class="lead mb-4">Sign in to access the Laravel CI community.</p>

        @if(session('error'))
          <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        @endif

        @if(session('status'))
          <div class="alert alert-success" role="alert">{{ session('status') }}</div>
        @endif

        <a href="{{ route('auth.github.redirect') }}" class="btn-github-lg" id="githubLoginBtn">
          <i class="fa-brands fa-github"></i> Continue with GitHub
        </a>

        <div class="auth-divider">or</div>

        <div class="mb-3">
          <label class="form-label" style="font-weight:500;font-size:.9rem">Email address</label>
          <input type="email" class="form-control" placeholder="you@example.com" disabled
            data-bs-toggle="tooltip" title="Email sign-in coming soon — use GitHub for now"
            style="min-height:50px;border-radius:var(--radius)" />
        </div>
        <button class="btn btn-navy w-100 mb-3" disabled style="min-height:50px;opacity:.6">
          Sign in with email <span class="badge-pill badge-soft ms-2">Coming soon</span>
        </button>

        <p style="font-size:.8rem;color:var(--muted)">By continuing you agree to our <a href="#">Terms of Service</a> and <a href="#">Code of Conduct</a>. We only request your public GitHub profile.</p>

        <p class="text-center mt-4 mb-0" style="font-size:.95rem">New to Laravel CI? <a href="{{ route('about') }}" style="font-weight:600">Join the community</a></p>
      </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="auth-right">
      <div class="inner">
        <div class="mascot-circle"><img src="{{ asset('assets/web/img/mascot.png') }}" alt="Laravel CI mascot" /></div>
        <h2 style="color:#fff;font-size:var(--fs-h2)">Build with the Ivorian Laravel community</h2>
        <p style="color:rgba(255,255,255,.9);font-size:1.05rem">500+ developers sharing knowledge, opportunities and friendship — in Abidjan and across the diaspora.</p>
        <div class="auth-stats">
          <div><div class="n">500+</div><div class="l">Members</div></div>
          <div><div class="n">1.2k+</div><div class="l">Questions</div></div>
          <div><div class="n">24+</div><div class="l">Events</div></div>
        </div>
        <div class="testimonial-card">
          <p class="mb-3" style="font-size:.98rem">"Laravel CI is where I found my mentor, my first remote job, and a community that actually has my back. This is what we needed."</p>
          <div class="author-row"><span class="avatar av-2" style="border:2px solid rgba(255,255,255,.4)">YT</span><div class="meta"><div class="name" style="color:#fff">Yao Térence</div><div class="sub" style="color:rgba(255,255,255,.8)">Backend developer, Abidjan</div></div></div>
        </div>
      </div>
    </div>
  </div>

@endsection
