<div>
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
          <div class="d-flex flex-column gap-3">
            <div class="sidebar-card">
              <div class="sidebar-title">Type de contrat</div>
              <button type="button" wire:click="setType(null)"
                      class="filter-pill d-inline-block mb-2 {{ ! $type ? 'active' : '' }}">Tous</button>
              @foreach($this->offerTypes as $offerType)
                <button type="button" wire:click="setType('{{ $offerType->value }}')"
                        class="filter-pill d-inline-block mb-2 me-1 {{ $type === $offerType->value ? 'active' : '' }}">
                  {{ $offerType->label() }}
                </button>
              @endforeach
            </div>

            <div class="sidebar-card">
              <div class="sidebar-title">Localisation</div>
              <button type="button" wire:click="setRemote(false)"
                      class="filter-pill d-inline-block mb-2 {{ ! $remote ? 'active' : '' }}">Toutes</button>
              <button type="button" wire:click="setRemote(true)"
                      class="filter-pill d-inline-block mb-2 {{ $remote ? 'active' : '' }}">Remote uniquement</button>
            </div>

            @if($this->skills->isNotEmpty())
              <div class="sidebar-card">
                <div class="sidebar-title">Compétence</div>
                <button type="button" wire:click="setSkill(null)"
                        class="filter-pill d-inline-block mb-2 {{ ! $skill ? 'active' : '' }}">Toutes</button>
                @foreach($this->skills as $jobSkill)
                  <button type="button" wire:click="setSkill('{{ $jobSkill->slug }}')"
                          class="filter-pill d-inline-block mb-2 me-1 {{ $skill === $jobSkill->slug ? 'active' : '' }}">
                    {{ $jobSkill->name }}
                  </button>
                @endforeach
              </div>
            @endif

            <div class="sidebar-card">
              <div class="sidebar-title">Tri</div>
              @foreach(['newest' => 'Plus récentes', 'title' => 'Titre A–Z'] as $value => $label)
                <button type="button" wire:click="setSort('{{ $value }}')"
                        class="filter-pill d-inline-block mb-2 me-1 {{ $sort === $value ? 'active' : '' }}">
                  {{ $label }}
                </button>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-8 col-xl-9">
      <div wire:loading.delay class="text-center py-3 text-muted-2">
        <i class="fa-solid fa-spinner fa-spin me-2"></i> Chargement…
      </div>

      @if($this->offers->isEmpty())
        <div class="text-center py-5">
          <i class="fa-solid fa-briefcase fa-3x text-muted-2 mb-3"></i>
          <p class="text-muted-2 mb-3">Aucune offre pour ces filtres.</p>
          <button type="button" wire:click="resetFilters" class="btn btn-brand">Voir toutes les offres</button>
        </div>
      @else
        <div class="d-flex flex-column gap-4" wire:loading.remove.delay>
          @foreach($this->offers as $offer)
            <x-web.job-card :job-offer="$offer" wire:key="job-{{ $offer->id }}" />
          @endforeach
        </div>
        <div class="mt-4">
          {{ $this->offers->links('vendor.pagination.web') }}
        </div>
      @endif
    </div>
  </div>
</div>
