@extends('layouts.web')

@section('title', 'Offres d\'emploi — Laravel CI')

@push('head')
    <meta name="description" content="Offres Laravel et PHP de la communauté Laravel Côte d'Ivoire.">
    <link rel="canonical" href="{{ route('jobs.index') }}">
@endpush

@section('content')

  <section class="page-hero">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-8">
          <div class="breadcrumb-bar">
            <a href="{{ route('home') }}">Accueil</a>
            <i class="fa-solid fa-chevron-right"></i>
            <span>Emplois</span>
          </div>
          <h1 class="mb-2">Job board</h1>
          <p class="lead mb-3">Offres Laravel, PHP et tech publiées par la communauté Laravel CI.</p>
          @auth
            @if(auth()->user()->hasRole('member'))
              <a href="{{ route('jobs.create') }}" class="btn btn-brand btn-lg">
                <i class="fa-solid fa-circle-plus"></i> Publier une offre
              </a>
            @endif
          @else
            <a href="{{ route('login') }}" class="btn btn-brand btn-lg">
              <i class="fa-solid fa-circle-plus"></i> Publier une offre
            </a>
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

  <section class="section-sm">
    <div class="container">
      <button class="btn btn-ghost d-lg-none mb-3 w-100" type="button" data-bs-toggle="offcanvas" data-bs-target="#jobFilters">
        <i class="fa-solid fa-sliders"></i> Filtres
      </button>
      <div class="row g-4">
        <div class="col-lg-4 col-xl-3">
          <div class="offcanvas-lg offcanvas-start" tabindex="-1" id="jobFilters">
            <div class="offcanvas-header">
              <h5 class="offcanvas-title">Filtres</h5>
              <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#jobFilters"></button>
            </div>
            <div class="offcanvas-body d-block p-lg-0">
              <form method="GET" action="{{ route('jobs.index') }}" class="d-flex flex-column gap-3">
                <div class="sidebar-card">
                  <div class="sidebar-title">Type de contrat</div>
                  <a href="{{ route('jobs.index', array_filter(['remote' => $remote ? 1 : null, 'skill' => $skill, 'category' => $category, 'sort' => $sort])) }}"
                     class="filter-pill d-inline-block mb-2 {{ ! $type ? 'active' : '' }}">Tous</a>
                  @foreach(\App\Enums\Jobs\JobOfferType::cases() as $offerType)
                    <a href="{{ route('jobs.index', array_filter(['type' => $offerType->value, 'remote' => $remote ? 1 : null, 'skill' => $skill, 'category' => $category, 'sort' => $sort])) }}"
                       class="filter-pill d-inline-block mb-2 me-1 {{ $type === $offerType->value ? 'active' : '' }}">
                      {{ $offerType->label() }}
                    </a>
                  @endforeach
                </div>

                <div class="sidebar-card">
                  <div class="sidebar-title">Localisation</div>
                  <a href="{{ route('jobs.index', array_filter(['type' => $type, 'skill' => $skill, 'category' => $category, 'sort' => $sort])) }}"
                     class="filter-pill d-inline-block mb-2 {{ ! $remote ? 'active' : '' }}">Toutes</a>
                  <a href="{{ route('jobs.index', array_filter(['type' => $type, 'remote' => 1, 'skill' => $skill, 'category' => $category, 'sort' => $sort])) }}"
                     class="filter-pill d-inline-block mb-2 {{ $remote ? 'active' : '' }}">Remote uniquement</a>
                </div>

                @if($skills->isNotEmpty())
                  <div class="sidebar-card">
                    <div class="sidebar-title">Compétence</div>
                    <a href="{{ route('jobs.index', array_filter(['type' => $type, 'remote' => $remote ? 1 : null, 'category' => $category, 'sort' => $sort])) }}"
                       class="filter-pill d-inline-block mb-2 {{ ! $skill ? 'active' : '' }}">Toutes</a>
                    @foreach($skills as $jobSkill)
                      <a href="{{ route('jobs.index', array_filter(['type' => $type, 'remote' => $remote ? 1 : null, 'skill' => $jobSkill->slug, 'category' => $category, 'sort' => $sort])) }}"
                         class="filter-pill d-inline-block mb-2 me-1 {{ $skill === $jobSkill->slug ? 'active' : '' }}">
                        {{ $jobSkill->name }}
                      </a>
                    @endforeach
                  </div>
                @endif

                <div class="sidebar-card">
                  <div class="sidebar-title">Tri</div>
                  @foreach(['newest' => 'Plus récentes', 'title' => 'Titre A–Z'] as $value => $label)
                    <a href="{{ route('jobs.index', array_filter(['type' => $type, 'remote' => $remote ? 1 : null, 'skill' => $skill, 'category' => $category, 'sort' => $value])) }}"
                       class="filter-pill d-inline-block mb-2 me-1 {{ $sort === $value ? 'active' : '' }}">
                      {{ $label }}
                    </a>
                  @endforeach
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="col-lg-8 col-xl-9">
          @if($offers->isEmpty())
            <div class="text-center py-5">
              <i class="fa-solid fa-briefcase fa-3x text-muted-2 mb-3"></i>
              <p class="text-muted-2 mb-3">Aucune offre pour ces filtres.</p>
              <a href="{{ route('jobs.index') }}" class="btn btn-brand">Voir toutes les offres</a>
            </div>
          @else
            <div class="d-flex flex-column gap-4">
              @foreach($offers as $offer)
                <x-web.job-card :job-offer="$offer" />
              @endforeach
            </div>
            {{ $offers->links('vendor.pagination.web') }}
          @endif
        </div>
      </div>
    </div>
  </section>

@endsection
