<div class="container pb-5">
  <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
    @foreach(['upcoming' => 'À venir', 'past' => 'Passés', 'all' => 'Tous'] as $value => $label)
      <button type="button"
              wire:click="setPeriod('{{ $value }}')"
              class="filter-pill {{ $period === $value ? 'active' : '' }}">
        {{ $label }}
      </button>
    @endforeach
  </div>

  @if($this->types->isNotEmpty())
    <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
      <span class="text-muted-2 small fw-semibold">Type</span>
      <button type="button" wire:click="setType(null)"
              class="filter-pill {{ ! $type ? 'active' : '' }}">Tous</button>
      @foreach($this->types as $eventType)
        <button type="button" wire:click="setType('{{ $eventType->slug }}')"
                class="filter-pill {{ $type === $eventType->slug ? 'active' : '' }}">
          {{ $eventType->name }}
        </button>
      @endforeach
    </div>
  @endif

  @if($this->events->isEmpty())
    <div class="text-center py-5">
      <i class="fa-regular fa-calendar-xmark fa-3x text-muted-2 mb-3"></i>
      <p class="text-muted-2 mb-3">Aucun événement pour ces filtres.</p>
      <button type="button" wire:click="setPeriod('upcoming'); setType(null)" class="btn btn-brand">
        Voir les événements à venir
      </button>
    </div>
  @else
    <div wire:key="events-{{ $period }}-{{ $type ?? 'all' }}-{{ $this->events->currentPage() }}">
      <div wire:loading.delay class="text-center py-2 text-muted-2">
        <i class="fa-solid fa-spinner fa-spin me-2"></i> Chargement…
      </div>

      <div class="row g-4">
        @foreach($this->events as $event)
          <div class="col-md-6 col-lg-4 event-col" wire:key="event-{{ $event->id }}">
            <x-web.event-card :event="$event" :reveal="false" />
          </div>
        @endforeach
      </div>

      <div class="mt-4">
        {{ $this->events->links('vendor.pagination.web') }}
      </div>
    </div>
  @endif
</div>
