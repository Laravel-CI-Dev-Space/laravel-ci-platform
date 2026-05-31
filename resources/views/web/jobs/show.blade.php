@extends('layouts.web')

@section('title', ($job->title ?? 'Senior Laravel Engineer') . ' — ' . ($job->company ?? 'PayBox CI') . ' · Laravel CI')

@section('content')

  <!-- HERO -->
  <section class="page-hero">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-9">
          <div class="breadcrumb-bar">
            <a href="{{ route('home') }}">Home</a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('jobs.index') }}">Jobs</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>{{ $job->title ?? 'Senior Laravel Engineer' }}</span>
          </div>
          <div class="d-flex gap-3 align-items-start flex-wrap">
            <div class="company-logo {{ $job->logo_class ?? 'cl-1' }}" style="width:68px;height:68px;font-size:1.4rem">{{ $job->logo_text ?? 'PB' }}</div>
            <div class="flex-grow-1">
              <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                <h1 class="mb-0" style="font-size:var(--fs-h1)">{{ $job->title ?? 'Senior Laravel Engineer' }}</h1>
                @if($job->is_new ?? true)
                  <span class="badge-pill badge-green">NEW</span>
                @endif
              </div>
              <div class="d-flex flex-wrap gap-3" style="color:var(--muted);font-size:.95rem">
                <span><i class="fa-solid fa-building me-1 text-orange"></i> {{ $job->company ?? 'PayBox CI' }}</span>
                <span><i class="fa-solid fa-location-dot me-1 text-orange"></i> {{ $job->location ?? 'Abidjan, Plateau' }}</span>
                @if($job->remote ?? true)
                  <span><i class="fa-solid fa-wifi me-1 text-orange"></i> Remote OK</span>
                @endif
                <span><i class="fa-regular fa-clock me-1 text-orange"></i> Posted {{ $job->created_at?->diffForHumans() ?? '2 days ago' }}</span>
              </div>
            </div>
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

  <section class="section">
    <div class="container">
      <div class="row g-4">
        <!-- BODY -->
        <div class="col-lg-8">
          <div class="prose">
            {!! $job->description_html ?? '<h2>About the role</h2><p>PayBox CI is the mobile money aggregator powering payments for over half a million Ivorians. We\'re looking for a senior Laravel engineer to lead the backend of our core platform.</p><h2>What you\'ll do</h2><ul><li>Own the architecture of our payments backend across Wave, Orange Money and MTN MoMo.</li><li>Design resilient, idempotent transaction flows.</li><li>Mentor mid-level and junior engineers.</li><li>Champion testing — we ship with Pest.</li></ul><h2>What we\'re looking for</h2><ul><li>5+ years building production PHP, with at least 3 in Laravel.</li><li>Deep understanding of queues, caching and database design.</li><li>Clear written communication.</li></ul>' !!}
          </div>

          <div class="d-flex flex-wrap gap-2 mt-4">
            @foreach($job->contractTypes ?? [] as $type)
              <span class="badge-pill badge-soft">{{ $type }}</span>
            @endforeach
            @if(empty($job->contractTypes ?? []))
              <span class="badge-pill badge-soft">Full-time</span>
              <span class="badge-pill badge-soft">Senior</span>
            @endif
            @foreach($job->tags ?? [] as $tag)
              <span class="tag">{{ $tag }}</span>
            @endforeach
            @if(empty($job->tags ?? []))
              <span class="tag">laravel</span><span class="tag">php</span><span class="tag">redis</span><span class="tag">aws</span><span class="tag">pest</span><span class="tag">mysql</span>
            @endif
          </div>
        </div>

        <!-- SIDEBAR -->
        <div class="col-lg-4">
          <div class="apply-card">
            <div class="info-card">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                  <div class="lbl text-muted-2" style="font-size:.8rem">Salary range</div>
                  <div class="salary" style="font-size:1.25rem">{{ $job->salary_range ?? '1.8M – 2.5M' }}</div>
                  <div class="text-muted-2" style="font-size:.82rem">FCFA / month</div>
                </div>
                <button class="save-heart" aria-label="Save job"><i class="fa-regular fa-heart"></i></button>
              </div>
              @auth
                <a href="{{ route('jobs.apply', $job->id ?? 1) }}" class="btn btn-brand w-100 mb-2"><i class="fa-solid fa-paper-plane"></i> Apply now</a>
              @else
                <a href="{{ route('login') }}" class="btn btn-brand w-100 mb-2"><i class="fa-solid fa-paper-plane"></i> Apply now</a>
              @endauth
              <a href="#" class="btn btn-ghost w-100"><i class="fa-solid fa-share-nodes"></i> Share this job</a>
            </div>

            <div class="info-card">
              <div class="sidebar-title">Job overview</div>
              <div class="info-row"><div class="ic"><i class="fa-solid fa-briefcase"></i></div><div><div class="lbl">Contract</div><div class="val">{{ $job->contract_type ?? 'Full-time' }}</div></div></div>
              <div class="info-row"><div class="ic"><i class="fa-solid fa-layer-group"></i></div><div><div class="lbl">Level</div><div class="val">{{ $job->level ?? 'Senior · 5+ yrs' }}</div></div></div>
              <div class="info-row"><div class="ic"><i class="fa-solid fa-location-dot"></i></div><div><div class="lbl">Location</div><div class="val">{{ $job->location ?? 'Abidjan · Remote OK' }}</div></div></div>
              <div class="info-row"><div class="ic"><i class="fa-solid fa-users"></i></div><div><div class="lbl">Applicants</div><div class="val">{{ $job->applicants_count ?? 23 }} so far</div></div></div>
            </div>

            <div class="info-card">
              <div class="sidebar-title">About {{ $job->company ?? 'PayBox CI' }}</div>
              <p class="text-muted-2 mb-3" style="font-size:.9rem">{{ $job->company_description ?? 'A fintech building the rails for mobile money in West Africa. 60 people, headquartered in Abidjan.' }}</p>
              @if($job->company_website ?? null)
                <a href="{{ $job->company_website }}" target="_blank" class="view-all-link">Visit website <i class="fa-solid fa-arrow-up-right-from-square"></i></a>
              @endif
            </div>
          </div>
        </div>
      </div>

      <!-- Similar jobs -->
      <div class="mt-5 pt-4">
        <h2 class="section-heading mb-4">Similar roles</h2>
        <div class="job-card">
          <div class="d-flex gap-3">
            <div class="company-logo cl-4">OK</div>
            <div class="flex-grow-1 min-w-0">
              <div class="d-flex justify-content-between gap-2">
                <div><h3 style="font-size:1.1rem;margin:0" class="text-navy"><a href="{{ route('jobs.show', 2) }}" class="text-navy">Full-Stack Developer (Laravel + Vue)</a></h3><div class="text-muted-2" style="font-size:.9rem"><strong>Orange Digital CI</strong> · Abidjan, Cocody</div></div>
                <span class="salary text-nowrap">1.0M – 1.6M</span>
              </div>
              <div class="d-flex flex-wrap gap-2 mt-2"><span class="badge-pill badge-soft">Full-time</span><span class="tag">laravel</span><span class="tag">vue</span></div>
            </div>
          </div>
        </div>
        <div class="job-card">
          <div class="d-flex gap-3">
            <div class="company-logo cl-3">JL</div>
            <div class="flex-grow-1 min-w-0">
              <div class="d-flex justify-content-between gap-2">
                <div><h3 style="font-size:1.1rem;margin:0" class="text-navy"><a href="{{ route('jobs.show', 3) }}" class="text-navy">Laravel Backend Developer</a></h3><div class="text-muted-2" style="font-size:.9rem"><strong>Jokko Labs</strong> · Fully remote</div></div>
                <span class="salary text-nowrap">900k – 1.4M</span>
              </div>
              <div class="d-flex flex-wrap gap-2 mt-2"><span class="badge-pill badge-soft">Freelance</span><span class="tag">laravel</span><span class="tag">api</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection
