@extends('layouts.web')

@section('title', 'À propos · Laravel CI')
@section('description', "Découvrez Laravel Côte d'Ivoire : notre mission, notre vision, notre histoire et notre équipe fondatrice. La première communauté structurée de développeurs Laravel en Côte d'Ivoire.")

@section('content')

  <!-- HERO -->
  <section class="page-hero">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <span class="badge-pill badge-navy"><i class="fa-brands fa-laravel text-orange"></i> Fondé en 2026 · Open source · Abidjan, CI</span>
          <h1 class="my-3">{{ $settings->firstWhere('key', 'about_hero_title')?->value ?? "La première communauté structurée de développeurs Laravel en Côte d'Ivoire" }}</h1>
          <p class="lead" style="max-width:42rem">{{ $settings->firstWhere('key', 'about_hero_subtitle')?->value ?? "Laravel CI est née de la conviction qu'un développeur ivoirien ne devrait pas avancer seul. Fondée sur le partage des connaissances, l'inclusion et la croissance collective." }}</p>
          <a href="{{ route('join') }}" class="btn btn-brand btn-lg mt-2"><i class="fa-solid fa-user-plus"></i> Nous rejoindre</a>
        </div>
        <div class="col-lg-5 d-none d-lg-block">
          <div class="mascot-art" style="width:clamp(200px,22vw,280px)">
            <span class="m-ring"></span><span class="m-blob"></span>
            <img src="{{ asset('assets/web/img/mascot.png') }}" alt="Laravel CI mascot" />
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- MISSION -->
  <section class="section">
    <div class="container">
      <div class="text-center mb-5 reveal">
        <span class="section-eyebrow">Pourquoi nous existons</span>
        <h2 class="section-heading">Notre mission</h2>
      </div>
      <div class="row g-4">
        @php
        $missions = [
          ['icon' => 'fa-solid fa-users',        'text' => "Rassembler les développeurs Laravel de Côte d'Ivoire autour d'un espace commun, structuré et inclusif."],
          ['icon' => 'fa-solid fa-book-open',    'text' => "Produire et partager des contenus pédagogiques adaptés au contexte ivoirien : tutoriels, articles, retours d'expérience."],
          ['icon' => 'fa-solid fa-calendar-check','text' => "Organiser des événements réguliers : meetups, workshops, hackathons et webinaires pour dynamiser les échanges."],
          ['icon' => 'fa-solid fa-briefcase',    'text' => "Faciliter les connexions professionnelles entre développeurs, entreprises et recruteurs du marché local."],
          ['icon' => 'fa-solid fa-code-branch',  'text' => "Contribuer à l'open source et encourager la participation à l'écosystème Laravel mondial."],
          ['icon' => 'fa-solid fa-globe',        'text' => "Servir de pont entre les talents locaux et les opportunités professionnelles à l'international."],
        ];
        @endphp
        @foreach($missions as $i => $m)
        <div class="col-md-6 col-lg-4 reveal" @if($i > 0) data-delay="{{ $i * 0.06 }}" @endif>
          <div class="card-soft h-100" style="padding:1.75rem;display:flex;gap:1.25rem;align-items:flex-start">
            <div class="value-icon" style="margin:0;flex-shrink:0;width:2.5rem;height:2.5rem;font-size:1rem">
              <i class="{{ $m['icon'] }}"></i>
            </div>
            <p class="mb-0" style="color:var(--muted);line-height:1.65;font-size:.93rem">{{ $m['text'] }}</p>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- VISION -->
  <section style="background:var(--light);padding:3rem 0">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 reveal">
          <div class="text-center mb-3">
            <span class="section-eyebrow">Où nous allons</span>
            <h2 class="section-heading mb-0">Notre vision</h2>
          </div>
          <div style="border-left:4px solid var(--orange);padding:1.5rem 2rem;background:#fff;border-radius:0 .75rem .75rem 0;box-shadow:0 1px 8px rgba(0,0,0,.06)">
            <p style="font-size:1.05rem;line-height:1.75;color:var(--navy);font-style:italic;margin:0">
              "{{ $settings->firstWhere('key', 'about_vision')?->value ?? "Faire de Laravel Côte d'Ivoire la communauté de développeurs Laravel la plus active, la plus inclusive et la plus influente d'Afrique de l'Ouest, en contribuant à l'excellence technique et à l'employabilité des développeurs ivoiriens à l'échelle locale et internationale." }}"
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- NOTRE NAISSANCE -->
  <section style="background:linear-gradient(160deg,#0b1629 0%,#12233e 55%,#0f1e38 100%);padding:5rem 0;position:relative;overflow:hidden">
    <div aria-hidden="true" style="position:absolute;top:-8rem;right:-8rem;width:500px;height:500px;border-radius:50%;background:radial-gradient(circle,rgba(232,89,12,.08) 0%,transparent 70%);pointer-events:none"></div>
    <div aria-hidden="true" style="position:absolute;bottom:-6rem;left:-6rem;width:400px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(232,89,12,.06) 0%,transparent 70%);pointer-events:none"></div>

    <div class="container position-relative" style="z-index:1">

      <!-- Grande citation -->
      <div class="text-center mb-5 reveal">
        <span class="section-eyebrow" style="color:rgba(255,255,255,.5)">Notre naissance</span>
        <blockquote style="margin:1.25rem auto 0;max-width:48rem">
          <p style="font-size:clamp(1.1rem,2.2vw,1.55rem);line-height:1.6;color:#fff;font-style:italic;font-weight:500">
            "La Côte d'Ivoire ne manque pas de développeurs talentueux. Elle manque d'un espace commun pour les réunir, les faire grandir ensemble et les rendre visibles au reste du monde tech."
          </p>
          <footer style="color:rgba(255,255,255,.4);font-size:.82rem;font-style:normal;margin-top:1rem;font-weight:600;letter-spacing:.05em;text-transform:uppercase">Manifeste fondateur · Laravel Côte d'Ivoire</footer>
        </blockquote>
      </div>

      <!-- Histoire + Faits -->
      <div class="row g-5 align-items-start mt-2">

        <!-- Colonne gauche : récit -->
        <div class="col-lg-6 reveal">
          <h3 style="color:#fff;font-size:1.3rem;margin-bottom:1.25rem">D'une conviction à une communauté</h3>
          <div style="display:flex;flex-direction:column;gap:.9rem">
            <p style="color:rgba(255,255,255,.78);line-height:1.8;margin:0">En 2025, cherchant une communauté Laravel en Côte d'Ivoire, Wilson Kouassi et Mahamadou Diaby font le même constat : rien n'existe. Ni sur LinkedIn, ni sur Facebook, ni sur GitHub. Des centaines de développeurs ivoiriens avancent en silo, sans espace commun pour se retrouver et grandir ensemble.</p>
            <p style="color:rgba(255,255,255,.78);line-height:1.8;margin:0">L'inspiration vient des communautés francophones voisines : Laravel Cameroun, Laravel France, Laravel Sénégal. La conviction s'impose : si ça existe ailleurs, ça doit exister en Côte d'Ivoire. Avec la même énergie, la même qualité, mais ancrée dans notre réalité locale.</p>
            <p style="color:rgba(255,255,255,.78);line-height:1.8;margin:0">Un groupe WhatsApp est créé. Les développeurs sortent de l'isolement. Les discussions s'enchaînent, les collaborations émergent, les premiers événements se tiennent. En quelques semaines, la dynamique est irréversible. <strong style="color:#e8590c">Laravel CI</strong> est née.</p>
          </div>
        </div>

        <!-- Colonne droite : cartes visuelles -->
        <div class="col-lg-6 reveal" data-delay="0.1">
          <div class="row g-3">
            <div class="col-6">
              <div style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:1rem;padding:1.5rem;text-align:center">
                <div style="font-size:2.8rem;font-weight:800;color:#e8590c;line-height:1">0</div>
                <div style="color:rgba(255,255,255,.6);font-size:.82rem;margin-top:.5rem;line-height:1.4">Communauté Laravel structurée en CI avant nous</div>
              </div>
            </div>
            <div class="col-6">
              <div style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.1);border-radius:1rem;padding:1.5rem;text-align:center">
                <div style="font-size:2.8rem;font-weight:800;color:#e8590c;line-height:1">900+</div>
                <div style="color:rgba(255,255,255,.6);font-size:.82rem;margin-top:.5rem;line-height:1.4">Membres sur LinkedIn aujourd'hui</div>
              </div>
            </div>
            <div class="col-12">
              <div style="background:rgba(232,89,12,.12);border:1px solid rgba(232,89,12,.25);border-radius:1rem;padding:1.25rem 1.5rem">
                <div style="color:#e8590c;font-weight:700;font-size:.75rem;letter-spacing:.12em;text-transform:uppercase;margin-bottom:.75rem">Communautés qui nous ont inspirés</div>
                <div style="display:flex;flex-wrap:wrap;gap:.5rem">
                  <span style="background:rgba(255,255,255,.08);color:rgba(255,255,255,.85);border-radius:2rem;padding:.3rem .9rem;font-size:.84rem">Laravel Cameroun</span>
                  <span style="background:rgba(255,255,255,.08);color:rgba(255,255,255,.85);border-radius:2rem;padding:.3rem .9rem;font-size:.84rem">Laravel France</span>
                  <span style="background:rgba(255,255,255,.08);color:rgba(255,255,255,.85);border-radius:2rem;padding:.3rem .9rem;font-size:.84rem">Laravel Sénégal</span>
                </div>
              </div>
            </div>
            <div class="col-12">
              <div style="background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:1rem;padding:1.25rem 1.5rem;display:flex;align-items:flex-start;gap:.75rem">
                <i class="fa-brands fa-whatsapp" style="color:#25d366;font-size:1.5rem;flex-shrink:0;margin-top:.15rem"></i>
                <div>
                  <div style="color:rgba(255,255,255,.45);font-size:.78rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;margin-bottom:.35rem">Point de départ</div>
                  <div style="color:rgba(255,255,255,.8);font-size:.9rem;line-height:1.55">Un groupe WhatsApp, quelques développeurs passionnés et une conviction partagée. Le reste, c'est votre histoire aussi.</div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </section>

  <!-- TIMELINE -->
  <section class="section bg-light-2">
    <div class="container">
      <div class="text-center mb-5">
        <span class="section-eyebrow">Notre histoire</span>
        <h2>D'un groupe WhatsApp à un mouvement</h2>
      </div>
      <div class="timeline">
        @foreach($timeline as $event)
        <div class="tl-item reveal">
          <span class="tl-dot"></span>
          <div class="tl-card">
            <div class="tl-year">{{ $event->period }}</div>
            <h3 style="font-size:1.1rem">{{ $event->title }}</h3>
            <p class="mb-0 text-muted-2" style="font-size:.92rem">{{ $event->description }}</p>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- VALEURS -->
  <section class="section">
    <div class="container">
      <div class="text-center mb-5">
        <span class="section-eyebrow">Ce en quoi nous croyons</span>
        <h2>Nos valeurs</h2>
      </div>
      <div class="row g-4">
        @foreach($values as $value)
        <div class="col-6 col-lg-4 reveal">
          <div class="card-soft value-card">
            <div class="value-icon"><i class="{{ $value->icon }}"></i></div>
            <h3 style="font-size:1.1rem">{{ $value->title }}</h3>
            <p class="text-muted-2 mb-0" style="font-size:.9rem">{{ $value->description }}</p>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- ÉQUIPE FONDATRICE -->
  <section class="section bg-light-2">
    <div class="container">
      <div class="text-center mb-5">
        <span class="section-eyebrow">Les fondateurs</span>
        <h2>L'équipe fondatrice</h2>
      </div>
      <div class="row g-4 justify-content-center">
        @foreach($team as $member)
        <div class="col-12 col-md-6 col-lg-5 reveal">
          <div class="card-soft" style="padding:2rem;display:flex;gap:1.5rem;align-items:flex-start">
            @if($member->avatarUrl())
              <img src="{{ $member->avatarUrl() }}"
                   alt="{{ $member->fullName() }}"
                   style="width:80px;height:80px;border-radius:50%;object-fit:cover;flex-shrink:0;border:3px solid var(--orange-100,#fde8d8)">
            @else
              <span class="avatar avatar-xl {{ $member->avatar_color }}" style="flex-shrink:0">
                {{ $member->initials() }}
              </span>
            @endif
            <div>
              <h3 style="font-size:1.1rem;margin-bottom:.15rem">{{ $member->fullName() }}</h3>
              <div class="role mb-2" style="color:var(--orange,#e8590c);font-weight:600;font-size:.85rem">{{ $member->role }}</div>
              @if($member->bio)
                <p class="text-muted-2 mb-3" style="font-size:.88rem;line-height:1.65">{{ $member->bio }}</p>
              @endif
              <div class="d-flex gap-2">
                @if($member->github_url)
                  <a href="{{ $member->github_url }}" class="social-icon" target="_blank"
                     style="background:var(--light);color:var(--navy)" aria-label="GitHub">
                    <i class="fa-brands fa-github"></i>
                  </a>
                @endif
                @if($member->linkedin_url)
                  <a href="{{ $member->linkedin_url }}" class="social-icon" target="_blank"
                     style="background:#e8f0fe;color:#0a66c2" aria-label="LinkedIn">
                    <i class="fa-brands fa-linkedin-in"></i>
                  </a>
                @endif
              </div>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- PARTENAIRES -->
  <section style="padding:3.5rem 0">
    <div class="container">
      <p class="text-center text-muted-2 mb-5" style="font-weight:500;letter-spacing:.05em">Nos communautés partenaires</p>
      <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:1.25rem">
        @foreach($partners as $partner)
        <div class="reveal" style="flex:0 0 auto;width:clamp(160px,18vw,220px)">
          <div class="partner-logo" style="flex-direction:column;padding:1.75rem 1.25rem;gap:.75rem;min-height:130px;justify-content:center">
            @if($partner->logo)
              <img src="{{ $partner->logoUrl() }}" alt="{{ $partner->name }}" style="height:64px;object-fit:contain;max-width:100%">
            @else
              <i class="{{ $partner->icon ?? 'fa-solid fa-hippo' }}" style="font-size:2rem"></i>
            @endif
            <span style="font-size:.82rem;font-weight:600;text-align:center;color:var(--navy)">{{ $partner->name }}</span>
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="section pt-0">
    <div class="container">
      <div class="cta-banner reveal">
        <h2 class="mb-3">{{ $settings->firstWhere('key', 'about_cta_title')?->value ?? "Ta place dans la communauté t'attend" }}</h2>
        <p class="lead mb-4" style="color:rgba(255,255,255,.9);max-width:38rem;margin-inline:auto">
          {{ $settings->firstWhere('key', 'about_cta_text')?->value ?? "900+ développeurs ivoiriens construisent l'avenir de la tech africaine, un commit à la fois." }}
        </p>
        <a href="{{ route('join') }}" class="btn btn-light btn-lg"><i class="fa-brands fa-github"></i> Rejoindre la communauté</a>
      </div>
    </div>
  </section>

@endsection
