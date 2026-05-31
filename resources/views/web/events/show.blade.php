@extends('layouts.web')

@section('title', ($event->title ?? 'CivTech Hack 2026') . ' — Laravel CI')

@section('content')

  <!-- HERO -->
  <section class="page-hero">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-9">
          <div class="breadcrumb-bar">
            <a href="{{ route('home') }}">Home</a>
            <i class="fa-solid fa-chevron-right"></i>
            <a href="{{ route('events.index') }}">Events</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>{{ $event->title ?? 'CivTech Hack 2026' }}</span>
          </div>
          @php
            $typeStyle = match(strtolower($event->type ?? 'hackathon')) {
              'meetup'  => 'class="badge-pill badge-orange"',
              'webinar' => 'class="badge-pill" style="background:#e7ebff;color:#4361ee"',
              default   => 'class="badge-pill" style="background:#f1e7ff;color:#7209b7"',
            };
            $typeIcon = match(strtolower($event->type ?? 'hackathon')) {
              'meetup'  => 'fa-solid fa-people-roof',
              'webinar' => 'fa-solid fa-video',
              default   => 'fa-solid fa-laptop-code',
            };
          @endphp
          <span {!! $typeStyle !!}><i class="{{ $typeIcon }}"></i> {{ ucfirst($event->type ?? 'Hackathon') }}</span>
          <h1 class="my-3" style="font-size:var(--fs-h1)">{{ $event->title ?? 'CivTech Hack 2026 — 48h building for local impact' }}</h1>
          <div class="d-flex flex-wrap gap-3" style="color:var(--muted);font-size:.95rem">
            <span><i class="fa-regular fa-calendar me-1 text-orange"></i> {{ $event->date_label ?? 'July 5–6, 2026' }}</span>
            <span><i class="fa-regular fa-clock me-1 text-orange"></i> {{ $event->time_label ?? 'Sat 9:00 AM – Sun 6:00 PM' }}</span>
            <span><i class="fa-solid fa-location-dot me-1 text-orange"></i> {{ $event->location ?? 'Orange Digital Center, Abidjan' }}</span>
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
            {!! $event->description_html ?? '<h2>About the event</h2><p>CivTech Hack is our flagship weekend hackathon: 48 hours, teams of up to four, one mission — build something that genuinely improves life in Côte d\'Ivoire.</p><h2>Schedule</h2>' !!}
          </div>

          @if(!empty($event->agenda ?? []))
            <div class="info-card mb-4">
              @foreach($event->agenda as $item)
                <div class="agenda-item">
                  <span class="agenda-time">{{ $item['time'] }}</span>
                  <div>
                    <strong class="text-navy">{{ $item['title'] }}</strong>
                    <div class="text-muted-2" style="font-size:.9rem">{{ $item['description'] }}</div>
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="info-card mb-4">
              <div class="agenda-item"><span class="agenda-time">Sat 09:00</span><div><strong class="text-navy">Kickoff &amp; team formation</strong><div class="text-muted-2" style="font-size:.9rem">Pitch ideas, find teammates, set up repos.</div></div></div>
              <div class="agenda-item"><span class="agenda-time">Sat 11:00</span><div><strong class="text-navy">Hacking begins</strong><div class="text-muted-2" style="font-size:.9rem">Mentors circulate; first check-in at 4 PM.</div></div></div>
              <div class="agenda-item"><span class="agenda-time">Sun 14:00</span><div><strong class="text-navy">Code freeze &amp; rehearsals</strong><div class="text-muted-2" style="font-size:.9rem">Polish demos and prep your 3-minute pitch.</div></div></div>
              <div class="agenda-item"><span class="agenda-time">Sun 16:00</span><div><strong class="text-navy">Demo day &amp; awards</strong><div class="text-muted-2" style="font-size:.9rem">Present to the jury; prizes for top 3 projects.</div></div></div>
            </div>
          @endif

          <div class="prose"><h2>Mentors &amp; speakers</h2></div>
          <div class="row g-3">
            @foreach($event->speakers ?? [] as $speaker)
              <div class="col-md-6"><div class="speaker-card"><span class="avatar avatar-lg av-1">{{ substr($speaker->name, 0, 2) }}</span><div><div class="name" style="font-weight:600">{{ $speaker->name }}</div><div class="sub text-muted-2" style="font-size:.85rem">{{ $speaker->role }}</div></div></div></div>
            @endforeach
            @if(empty($event->speakers ?? []))
              <div class="col-md-6"><div class="speaker-card"><span class="avatar avatar-lg av-1">SB</span><div><div class="name" style="font-weight:600">Serge Brou</div><div class="sub text-muted-2" style="font-size:.85rem">Architect · Payments</div></div></div></div>
              <div class="col-md-6"><div class="speaker-card"><span class="avatar avatar-lg av-3">FD</span><div><div class="name" style="font-weight:600">Fatou Diallo</div><div class="sub text-muted-2" style="font-size:.85rem">Lead engineer · APIs</div></div></div></div>
              <div class="col-md-6"><div class="speaker-card"><span class="avatar avatar-lg av-5">AD</span><div><div class="name" style="font-weight:600">Aïcha Doumbia</div><div class="sub text-muted-2" style="font-size:.85rem">DevOps · Infra</div></div></div></div>
              <div class="col-md-6"><div class="speaker-card"><span class="avatar avatar-lg av-4">YT</span><div><div class="name" style="font-weight:600">Yao Térence</div><div class="sub text-muted-2" style="font-size:.85rem">Frontend · Livewire</div></div></div></div>
            @endif
          </div>
        </div>

        <!-- SIDEBAR -->
        <div class="col-lg-4">
          <div class="apply-card">

            {{-- LIVEWIRE: @livewire('events.registration-button', ['event' => $event]) --}}

            <div class="info-card">
              <div class="d-flex gap-3 mb-3">
                <div class="event-date-chip"><div class="m">{{ $event->month_label ?? 'Jul' }}</div><div class="d">{{ $event->day_label ?? '05' }}</div></div>
                <div><div class="lbl text-muted-2" style="font-size:.8rem">{{ $event->is_paid ? 'Paid event' : 'Free to attend' }}</div><div class="val" style="font-size:1.15rem">Register to reserve</div></div>
              </div>
              @php $pct = ($event->spots_total ?? 80) > 0 ? round((($event->spots_used ?? 64) / ($event->spots_total ?? 80)) * 100) : 0; @endphp
              <div class="spots-label"><span>{{ $event->spots_used ?? 64 }} / {{ $event->spots_total ?? 80 }} spots filled</span><span>{{ ($event->spots_total ?? 80) - ($event->spots_used ?? 64) }} left</span></div>
              <div class="progress-spots mb-3"><div class="bar" style="width:{{ $pct }}%"></div></div>
              <a href="{{ route('login') }}" class="btn btn-brand w-100 mb-2"><i class="fa-solid fa-ticket"></i> Register now</a>
              <a href="#" class="btn btn-ghost w-100"><i class="fa-regular fa-calendar-plus"></i> Add to calendar</a>
            </div>

            <div class="info-card">
              <div class="sidebar-title">Details</div>
              <div class="info-row"><div class="ic"><i class="{{ $typeIcon ?? 'fa-solid fa-laptop-code' }}"></i></div><div><div class="lbl">Type</div><div class="val">{{ ucfirst($event->type ?? 'Hackathon') }}</div></div></div>
              <div class="info-row"><div class="ic"><i class="fa-regular fa-calendar"></i></div><div><div class="lbl">When</div><div class="val">{{ $event->date_label ?? 'Jul 5–6, 2026' }}</div></div></div>
              <div class="info-row"><div class="ic"><i class="fa-solid fa-location-dot"></i></div><div><div class="lbl">Where</div><div class="val">{{ $event->location ?? 'Orange Digital Center' }}</div></div></div>
              <div class="info-row"><div class="ic"><i class="fa-solid fa-user-group"></i></div><div><div class="lbl">Format</div><div class="val">{{ $event->format ?? 'Teams up to 4' }}</div></div></div>
            </div>

            <div class="info-card">
              <div class="sidebar-title">Who's coming</div>
              <div class="avatar-stack mb-2"><span class="avatar avatar-sm av-1">SB</span><span class="avatar avatar-sm av-3">FD</span><span class="avatar avatar-sm av-5">AD</span><span class="avatar avatar-sm av-4">YT</span><span class="avatar avatar-sm av-6">NG</span></div>
              <p class="text-muted-2 mb-0" style="font-size:.88rem">{{ $event->spots_used ?? 64 }} people have registered.</p>
            </div>
          </div>
        </div>
      </div>

      <!-- More events -->
      <div class="mt-5 pt-4">
        <h2 class="section-heading mb-4">More events</h2>
        <div class="row g-4">
          <div class="col-md-6 reveal">
            <article class="card-soft event-card">
              <div class="event-banner ev-meetup"></div>
              <div class="card-pad">
                <div class="d-flex gap-3">
                  <div class="event-date-chip"><div class="m">Jun</div><div class="d">14</div></div>
                  <div><span class="badge-pill badge-orange"><i class="fa-solid fa-people-roof"></i> Meetup</span><h3 class="art-title mt-2 mb-0" style="font-size:1.1rem"><a href="{{ route('events.show', 1) }}">Eloquent Deep Dive</a></h3><div class="text-muted-2 mt-1" style="font-size:.86rem">Jokkolabs, Cocody · Sat 2 PM</div></div>
                </div>
              </div>
            </article>
          </div>
          <div class="col-md-6 reveal" data-delay="0.06">
            <article class="card-soft event-card">
              <div class="event-banner ev-webinar"></div>
              <div class="card-pad">
                <div class="d-flex gap-3">
                  <div class="event-date-chip"><div class="m">Jun</div><div class="d">18</div></div>
                  <div><span class="badge-pill" style="background:#e7ebff;color:#4361ee"><i class="fa-solid fa-video"></i> Webinar</span><h3 class="art-title mt-2 mb-0" style="font-size:1.1rem"><a href="{{ route('events.show', 2) }}">Testing APIs with Pest</a></h3><div class="text-muted-2 mt-1" style="font-size:.86rem">Online · Wed 7 PM</div></div>
                </div>
              </div>
            </article>
          </div>
        </div>
      </div>
    </div>
  </section>

@endsection
