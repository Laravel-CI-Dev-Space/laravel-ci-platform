@php
  use App\Enums\Events\EventReminderType;
  $modalId = $modalId ?? 'reminderModal';
@endphp

@if($showReminderModal)
  <div class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true" style="background:rgba(15,15,20,.45);">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg dash-reminder-modal">
        <div class="modal-header border-0 pb-0">
          <div>
            <h5 class="modal-title fw-semibold mb-1">Rappels par email</h5>
            <p class="text-secondary small mb-0">Choisissez quand vous souhaitez être prévenu.</p>
          </div>
          <button type="button" class="btn-close" wire:click="closeReminderModal" aria-label="Fermer"></button>
        </div>

        <div class="modal-body pt-3">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-secondary small">Options disponibles</span>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-sm btn-light" wire:click="selectAllReminders">
                Tout cocher
              </button>
              <button type="button" class="btn btn-sm btn-light" wire:click="clearAllReminders">
                Tout décocher
              </button>
            </div>
          </div>

          <div class="dash-reminder-options">
            @foreach(EventReminderType::cases() as $type)
              <label class="dash-reminder-option">
                <input type="checkbox"
                       class="form-check-input mt-0"
                       wire:model="selectedReminders"
                       value="{{ $type->value }}">
                <span class="dash-reminder-option__icon"><i class="ti ti-bell"></i></span>
                <span class="dash-reminder-option__text">
                  <strong>{{ $type->label() }}</strong>
                  <small class="text-secondary d-block">{{ $type->value }}</small>
                </span>
              </label>
            @endforeach
          </div>
        </div>

        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light" wire:click="closeReminderModal">Annuler</button>
          <button type="button"
                  class="btn btn-primary"
                  wire:click="saveReminderPreferences"
                  wire:loading.attr="disabled"
                  wire:target="saveReminderPreferences">
            <span wire:loading.remove wire:target="saveReminderPreferences">Enregistrer</span>
            <span wire:loading wire:target="saveReminderPreferences">
              <i class="ti ti-loader-2 ti-spin"></i> Enregistrement…
            </span>
          </button>
        </div>
      </div>
    </div>
  </div>
@endif
