@props(['event'])

@php
  $modalId = 'cancelEventModal-' . $event->id;
@endphp

<div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="{{ $modalId }}Label">Annuler l'inscription</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
      </div>
      <div class="modal-body">
        <p class="mb-2">Confirmez-vous l'annulation de votre inscription à :</p>
        <p class="fw-semibold mb-0">{{ $event->title }}</p>
        <p class="text-secondary small mt-2 mb-0">
          {{ $event->start_date->translatedFormat('l j F Y · H:i') }}
        </p>
        <p class="text-secondary small mt-3 mb-0">
          Un email de confirmation vous sera envoyé.
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Retour</button>
        <form method="POST" action="{{ route('events.cancel', $event) }}" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-danger">
            <i class="ti ti-x me-1"></i> Confirmer l'annulation
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
