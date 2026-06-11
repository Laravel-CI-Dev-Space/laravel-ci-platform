@php
  $event = $this->event;
  $taken = (int) ($event->confirmed_registrations_count ?? 0);
  $total = (int) ($event->capacity ?? 0);
  $pct = $total > 0 ? min(100, ($taken / $total) * 100) : 0;
  $registration = $this->registration;
@endphp

<div>
  <div class="info-card">
    <h2 class="sidebar-title mb-3">Participation</h2>

    <div class="d-flex gap-3 mb-3">
      <div class="event-date-chip">
        <div class="m">{{ $event->start_date->translatedFormat('M') }}</div>
        <div class="d">{{ $event->start_date->format('d') }}</div>
      </div>
      <div>
        @if($total > 0)
          <div class="spots-label">
            <span>{{ $taken }} / {{ $total }} inscrits</span>
            <span>{{ max(0, $total - $taken) }} restantes</span>
          </div>
          <div class="progress-spots mb-0"><div class="bar" style="width:{{ $pct }}%"></div></div>
        @else
          <div class="val" style="font-size:1rem">Places illimitées</div>
        @endif
      </div>
    </div>

    @if($flashMessage)
      <div class="alert alert-success mb-3">
        <i class="fa-solid fa-circle-check me-2"></i> {{ $flashMessage }}
      </div>
    @elseif($registration)
      <div class="alert alert-success mb-0">
        <i class="fa-solid fa-circle-check me-2"></i> Vous êtes inscrit à cet événement.
      </div>
    @elseif($this->waitlist)
      <div class="alert alert-warning mb-3">
        <i class="fa-solid fa-hourglass-half me-2"></i>
        Liste d'attente — position <strong>#{{ $this->waitlist->position }}</strong>.
      </div>

      @if($this->canLeaveWaitlist)
        <button type="button"
                wire:click="leaveWaitlist"
                wire:loading.attr="disabled"
                wire:confirm="Quitter la liste d'attente ?"
                class="btn btn-outline-secondary w-100">
          <span wire:loading.remove wire:target="leaveWaitlist">
            <i class="fa-solid fa-right-from-bracket me-2"></i> Quitter la liste d'attente
          </span>
          <span wire:loading wire:target="leaveWaitlist">
            <i class="fa-solid fa-spinner fa-spin"></i> Traitement…
          </span>
        </button>
      @endif
    @elseif($this->canRegister)
      <button type="button" wire:click="register" wire:loading.attr="disabled" class="btn btn-brand w-100">
        <span wire:loading.remove wire:target="register">
          <i class="fa-solid fa-ticket"></i>
          {{ $event->isFull() ? 'Rejoindre la liste d\'attente' : 'Confirmer mon inscription' }}
        </span>
        <span wire:loading wire:target="register">
          <i class="fa-solid fa-spinner fa-spin"></i> Traitement…
        </span>
      </button>
      <p class="text-muted-2 text-center mt-2 mb-0" style="font-size:.8rem">Réservé aux membres actifs</p>
    @elseif(auth()->check() && ! auth()->user()->hasRole('member'))
      <p class="text-muted-2 mb-0">Seuls les membres actifs peuvent s'inscrire.</p>
    @elseif(! $event->isRegisterable())
      <p class="text-muted-2 mb-0">Les inscriptions sont closes pour cet événement.</p>
    @else
      <a href="{{ route('login') }}" class="btn btn-brand w-100">
        <i class="fa-brands fa-github"></i> Se connecter pour s'inscrire
      </a>
    @endif

    @error('register')
      <div class="alert alert-danger mt-3 mb-0">{{ $message }}</div>
    @enderror

  </div>
</div>
