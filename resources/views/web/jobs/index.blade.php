@extends('layouts.web')

@section('title', 'Job Board — Laravel CI')

@section('content')

  <section class="page-hero">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-8">
          <div class="breadcrumb-bar"><a href="{{ route('home') }}">Home</a> <i class="fa-solid fa-chevron-right"></i> <span>Jobs</span></div>
          <h1 class="mb-2">Job Board</h1>
          <p class="lead mb-3">Laravel &amp; PHP roles for Ivorian developers — local, remote, and across the diaspora.</p>
          @auth
            <a href="{{ route('jobs.create') }}" class="btn btn-brand btn-lg"><i class="fa-solid fa-circle-plus"></i> Post a Job</a>
          @else
            <a href="{{ route('login') }}" class="btn btn-brand btn-lg"><i class="fa-solid fa-circle-plus"></i> Post a Job</a>
          @endauth
        </div>
        <div class="col-lg-4 d-none d-lg-block">
          <div class="mascot-art">
            <span class="m-ring"></span><span class="m-blob"></span>
            <img src="{{ asset('assets/web/img/mascot.png') }}" alt="Laravel CI mascot" />
          </div>
        </div>
      </div>
    </div>
  </section>

  {{-- LIVEWIRE: @livewire('jobs.job-list') --}}

  <section class="section-sm">
    <div class="container">
      <button class="btn btn-ghost d-lg-none mb-3 w-100" type="button" data-bs-toggle="offcanvas" data-bs-target="#jobFilters">
        <i class="fa-solid fa-sliders"></i> Filters
      </button>
      <div class="row g-4">

        <!-- FILTERS -->
        <div class="col-lg-4 col-xl-3">
          <div class="offcanvas-lg offcanvas-start" tabindex="-1" id="jobFilters">
            <div class="offcanvas-header">
              <h5 class="offcanvas-title">Filters</h5>
              <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#jobFilters"></button>
            </div>
            <div class="offcanvas-body d-block p-lg-0">
              <div class="sidebar-card">
                <div class="search-field"><i class="fa-solid fa-magnifying-glass"></i><input type="search" class="form-control" placeholder="Job title or keyword" /></div>
              </div>
              <div class="sidebar-card">
                <div class="sidebar-title">Contract type</div>
                <label class="filter-check"><input type="checkbox" checked /> Full-time <span class="cnt">18</span></label>
                <label class="filter-check"><input type="checkbox" /> Part-time <span class="cnt">4</span></label>
                <label class="filter-check"><input type="checkbox" /> Freelance <span class="cnt">11</span></label>
                <label class="filter-check"><input type="checkbox" /> Internship <span class="cnt">6</span></label>
              </div>
              <div class="sidebar-card">
                <div class="sidebar-title">Experience level</div>
                <label class="filter-check"><input type="checkbox" /> Junior <span class="cnt">9</span></label>
                <label class="filter-check"><input type="checkbox" checked /> Mid-level <span class="cnt">14</span></label>
                <label class="filter-check"><input type="checkbox" /> Senior <span class="cnt">7</span></label>
              </div>
              <div class="sidebar-card">
                <div class="sidebar-title">Remote</div>
                <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="remoteToggle" checked /><label class="form-check-label" for="remoteToggle">Remote only</label></div>
                <div class="mt-3"><label class="form-label" style="font-size:.85rem;font-weight:500">Location</label><input type="text" class="form-control" placeholder="e.g. Abidjan" /></div>
              </div>
              <div class="sidebar-card">
                <div class="sidebar-title">Min. salary</div>
                <input type="range" class="form-range" id="salaryRange" min="200000" max="3000000" step="100000" value="800000" />
                <div class="d-flex justify-content-between"><span class="text-muted-2" style="font-size:.8rem">200k</span><strong class="text-orange" id="salaryValue">800k FCFA/mo</strong><span class="text-muted-2" style="font-size:.8rem">3M</span></div>
              </div>
              <div class="d-flex gap-2">
                <button class="btn btn-brand flex-grow-1"><i class="fa-solid fa-filter"></i> Apply</button>
                <button class="btn btn-ghost">Reset</button>
              </div>
            </div>
          </div>
        </div>

        <!-- JOB LIST -->
        <div class="col-lg-8 col-xl-9">
          <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <span class="text-muted-2"><strong class="text-navy">39</strong> open positions</span>
            <select class="form-select" style="width:auto;border-radius:999px">
              <option>Most recent</option>
              <option>Highest salary</option>
              <option>Closing soon</option>
            </select>
          </div>

          <div class="job-card">
            <div class="d-flex gap-3">
              <div class="company-logo cl-1">PB</div>
              <div class="flex-grow-1 min-w-0">
                <div class="d-flex justify-content-between gap-2">
                  <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                      <h3 style="font-size:1.15rem;margin:0" class="text-navy"><a href="{{ route('jobs.show', 1) }}" class="text-navy">Senior Laravel Engineer</a></h3>
                      <span class="badge-pill badge-green">NEW</span>
                    </div>
                    <div class="text-muted-2" style="font-size:.9rem"><strong>PayBox CI</strong> · <i class="fa-solid fa-location-dot"></i> Abidjan, Plateau · <span class="badge-pill badge-navy ms-1">Remote OK</span></div>
                  </div>
                  <button class="save-heart" aria-label="Save job"><i class="fa-regular fa-heart"></i></button>
                </div>
                <p class="my-2" style="font-size:.92rem;color:var(--muted)">Lead the backend of our mobile money platform. You'll own architecture, mentor juniors, and ship to half a million users.</p>
                <div class="d-flex flex-wrap gap-2 mb-2"><span class="badge-pill badge-soft">Full-time</span><span class="badge-pill badge-soft">Senior</span><span class="tag">laravel</span><span class="tag">redis</span><span class="tag">aws</span></div>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                  <span class="salary">1.8M – 2.5M FCFA/mo</span>
                  <a href="{{ route('jobs.show', 1) }}" class="btn btn-brand">Apply <i class="fa-solid fa-arrow-right"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="job-card">
            <div class="d-flex gap-3">
              <div class="company-logo cl-4">OK</div>
              <div class="flex-grow-1 min-w-0">
                <div class="d-flex justify-content-between gap-2">
                  <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                      <h3 style="font-size:1.15rem;margin:0" class="text-navy"><a href="{{ route('jobs.show', 2) }}" class="text-navy">Full-Stack Developer (Laravel + Vue)</a></h3>
                      <span class="badge-pill" style="background:#fdeaec;color:var(--level-advanced)">URGENT</span>
                    </div>
                    <div class="text-muted-2" style="font-size:.9rem"><strong>Orange Digital CI</strong> · <i class="fa-solid fa-location-dot"></i> Abidjan, Cocody</div>
                  </div>
                  <button class="save-heart" aria-label="Save job"><i class="fa-regular fa-heart"></i></button>
                </div>
                <p class="my-2" style="font-size:.92rem;color:var(--muted)">Join the team building citizen-facing digital services. Modern stack, real impact, great mentorship.</p>
                <div class="d-flex flex-wrap gap-2 mb-2"><span class="badge-pill badge-soft">Full-time</span><span class="badge-pill badge-soft">Mid-level</span><span class="tag">laravel</span><span class="tag">vue</span><span class="tag">mysql</span></div>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                  <span class="salary">1.0M – 1.6M FCFA/mo</span>
                  <a href="{{ route('jobs.show', 2) }}" class="btn btn-brand">Apply <i class="fa-solid fa-arrow-right"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="job-card">
            <div class="d-flex gap-3">
              <div class="company-logo cl-3">JL</div>
              <div class="flex-grow-1 min-w-0">
                <div class="d-flex justify-content-between gap-2">
                  <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                      <h3 style="font-size:1.15rem;margin:0" class="text-navy"><a href="{{ route('jobs.show', 3) }}" class="text-navy">Laravel Backend Developer</a></h3>
                    </div>
                    <div class="text-muted-2" style="font-size:.9rem"><strong>Jokko Labs</strong> · <i class="fa-solid fa-globe"></i> Fully remote · <span class="badge-pill badge-navy ms-1">Remote</span></div>
                  </div>
                  <button class="save-heart" aria-label="Save job"><i class="fa-regular fa-heart"></i></button>
                </div>
                <p class="my-2" style="font-size:.92rem;color:var(--muted)">Build APIs for a pan-African logistics startup. Work async with a distributed team across the diaspora.</p>
                <div class="d-flex flex-wrap gap-2 mb-2"><span class="badge-pill badge-soft">Freelance</span><span class="badge-pill badge-soft">Mid-level</span><span class="tag">laravel</span><span class="tag">api</span><span class="tag">docker</span></div>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                  <span class="salary">900k – 1.4M FCFA/mo</span>
                  <a href="{{ route('jobs.show', 3) }}" class="btn btn-brand">Apply <i class="fa-solid fa-arrow-right"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="job-card">
            <div class="d-flex gap-3">
              <div class="company-logo cl-5">AF</div>
              <div class="flex-grow-1 min-w-0">
                <div class="d-flex justify-content-between gap-2">
                  <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                      <h3 style="font-size:1.15rem;margin:0" class="text-navy"><a href="{{ route('jobs.show', 4) }}" class="text-navy">Junior PHP Developer (Internship)</a></h3>
                      <span class="badge-pill badge-green">NEW</span>
                    </div>
                    <div class="text-muted-2" style="font-size:.9rem"><strong>Afrikrea</strong> · <i class="fa-solid fa-location-dot"></i> Abidjan, Marcory</div>
                  </div>
                  <button class="save-heart" aria-label="Save job"><i class="fa-regular fa-heart"></i></button>
                </div>
                <p class="my-2" style="font-size:.92rem;color:var(--muted)">A 6-month internship for an aspiring developer. Pair with seniors, learn Laravel properly, and ship real features.</p>
                <div class="d-flex flex-wrap gap-2 mb-2"><span class="badge-pill badge-soft">Internship</span><span class="badge-pill badge-soft">Junior</span><span class="tag">php</span><span class="tag">laravel</span></div>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                  <span class="salary">250k FCFA/mo</span>
                  <a href="{{ route('jobs.show', 4) }}" class="btn btn-brand">Apply <i class="fa-solid fa-arrow-right"></i></a>
                </div>
              </div>
            </div>
          </div>

          <nav class="mt-4 d-flex justify-content-center">
            <ul class="pagination">
              <li class="page-item disabled"><a class="page-link" href="#"><i class="fa-solid fa-chevron-left"></i></a></li>
              <li class="page-item active"><a class="page-link" href="#">1</a></li>
              <li class="page-item"><a class="page-link" href="#">2</a></li>
              <li class="page-item"><a class="page-link" href="#">3</a></li>
              <li class="page-item"><a class="page-link" href="#"><i class="fa-solid fa-chevron-right"></i></a></li>
            </ul>
          </nav>
        </div>
      </div>
    </div>
  </section>

@endsection
