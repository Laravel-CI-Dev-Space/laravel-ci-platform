@extends('layouts.web')

@section('title', '404 — Page Not Found · Laravel CI')

@section('content')

  <div class="container d-flex align-items-center justify-content-center" style="min-height:70vh">
    <div class="text-center" style="max-width:500px;width:100%">
      <div class="mb-4">
        <a href="{{ route('home') }}" class="brand-logo d-inline-flex mb-4">
          <span class="brand-mark"><img src="{{ asset('assets/web/img/logo-mark.png') }}" alt="Laravel CI" width="36" /></span>
          <span class="ms-2" style="font-weight:700;font-size:1.3rem;color:var(--navy)">Laravel CI</span>
        </a>
      </div>

      <div class="mb-4" style="font-size:6rem;font-weight:700;color:var(--orange);line-height:1">404</div>
      <h1 class="mb-3" style="font-size:var(--fs-h2)">Page Not Found</h1>
      <p class="text-muted-2 mb-4" style="font-size:1.05rem">Sorry, the page you're looking for doesn't exist or has been moved.</p>

      <div class="d-flex flex-wrap gap-3 justify-content-center">
        <a href="{{ route('home') }}" class="btn btn-brand btn-lg"><i class="fa-solid fa-house"></i> Go Home</a>
        <a href="{{ route('forum.index') }}" class="btn btn-outline-navy btn-lg"><i class="fa-solid fa-comments"></i> Forum</a>
      </div>

      <div class="mt-5">
        <p class="text-muted-2" style="font-size:.9rem">You might be looking for one of these:</p>
        <div class="d-flex flex-wrap gap-2 justify-content-center">
          <a href="{{ route('forum.index') }}" class="tag">Forum</a>
          <a href="{{ route('blog.index') }}" class="tag">Blog</a>
          <a href="#" class="tag">Events</a>
          <a href="#" class="tag">Jobs</a>
          <a href="{{ route('about') }}" class="tag">About</a>
        </div>
      </div>
    </div>
  </div>

@endsection
