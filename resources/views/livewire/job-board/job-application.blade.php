@php
  $jobOffer = $this->jobOffer;
@endphp

<div>
  <div class="info-card">
    <h2 class="sidebar-title mb-3">Candidature</h2>

    @if($jobOffer->salary)
      <div class="salary mb-3">{{ $jobOffer->salary }}</div>
    @endif

    @if($flashMessage)
      <div class="alert alert-success mb-0">
        <i class="fa-solid fa-circle-check me-2"></i> {{ $flashMessage }}
      </div>
    @elseif($this->application)
      <div class="alert alert-success mb-0">
        <i class="fa-solid fa-circle-check me-2"></i> Vous avez déjà postulé à cette offre.
      </div>
    @elseif($this->canApply)
      <form wire:submit="apply" class="d-flex flex-column gap-3">
        <div>
          <label for="cover_letter" class="form-label fw-semibold">Lettre de motivation (optionnel)</label>
          <textarea id="cover_letter" wire:model="coverLetter" rows="4" class="form-control"
                    placeholder="Présentez-vous en quelques lignes…"></textarea>
          @error('coverLetter')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>
        <button type="submit" wire:loading.attr="disabled" class="btn btn-brand w-100">
          <span wire:loading.remove wire:target="apply">
            <i class="fa-solid fa-paper-plane"></i> Envoyer ma candidature
          </span>
          <span wire:loading wire:target="apply">
            <i class="fa-solid fa-spinner fa-spin"></i> Envoi…
          </span>
        </button>
      </form>
      <p class="text-muted-2 text-center mt-2 mb-0" style="font-size:.8rem">Réservé aux membres actifs</p>
    @elseif(auth()->check() && ! auth()->user()->hasRole('member'))
      <p class="text-muted-2 mb-0">Seuls les membres actifs peuvent postuler.</p>
    @elseif(! $jobOffer->isApplyable())
      <p class="text-muted-2 mb-0">Les candidatures sont closes pour cette offre.</p>
    @else
      <a href="{{ route('login') }}" class="btn btn-brand w-100">
        <i class="fa-brands fa-github"></i> Se connecter pour postuler
      </a>
    @endif

    @error('apply')
      <div class="alert alert-danger mt-3 mb-0">{{ $message }}</div>
    @enderror
  </div>
</div>
