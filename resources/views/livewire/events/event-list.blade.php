<div>
  <section class="section-sm pb-0">
    <div class="container">
      <div class="d-flex flex-wrap gap-2">
        @foreach(['upcoming' => 'À venir', 'past' => 'Passés', 'all' => 'Tous'] as $value => $label)
          <button type="button"
                  wire:click="setPeriod('{{ $value }}')"
                  class="filter-pill {{ $period === $value ? 'active' : '' }}">
            {{ $label }}
          </button>
        @endforeach
      </div>
    </div>
  </section>

  <section class="section">
    <div class="container">
      @if($this->types->isNotEmpty())
        <div class="d-flex flex-wrap gap-2 mb-4">
          <span class="text-muted-2 small fw-semibold align-self-center me-1">Type</span>
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

      <div wire:loading.delay class="text-center py-3 text-muted-2">
        <i class="fa-solid fa-spinner fa-spin me-2"></i> Chargement…
      </div>

      @if($this->events->isEmpty())
        <div class="text-center py-5">
          <i class="fa-regular fa-calendar-xmark fa-3x text-muted-2 mb-3"></i>
          <p class="text-muted-2 mb-3">Aucun événement pour ces filtres.</p>
          <button type="button" wire:click="setPeriod('upcoming'); setType(null)" class="btn btn-brand">
            Voir les événements à venir
          </button>
        </div>
      @else
        <div class="row g-4" wire:loading.remove.delay>
          @foreach($this->events as $event)
            <x-web.event-card :event="$event" wire:key="event-{{ $event->id }}" />
          @endforeach
        </div>
        <div class="mt-4">
          {{ $this->events->links('vendor.pagination.web') }}
        </div>
      @endif
    </div>
  </section>
</div>
