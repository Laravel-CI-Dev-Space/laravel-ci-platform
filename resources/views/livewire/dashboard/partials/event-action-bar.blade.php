@php
  $isUpcoming = ! $event->isPast();
  $modalId = 'cancelEventModal-' . $event->id;
  $hasReminders = $registration->wantsReminders();
@endphp

<div class="dash-event-actions">
  <a href="{{ route('events.show', $event) }}"
     class="dash-event-action"
     data-bs-toggle="tooltip"
     data-bs-placement="top"
     title="Voir l'événement">
    <i class="ti ti-external-link"></i>
  </a>

  @if($isUpcoming && auth()->user()?->can('downloadIcs', $event))
    <a href="{{ route('events.calendar', $event) }}"
       class="dash-event-action"
       data-bs-toggle="tooltip"
       data-bs-placement="top"
       title="Ajouter au calendrier">
      <i class="ti ti-calendar-plus"></i>
    </a>
  @endif

  @if($isUpcoming && auth()->user()?->can('manageReminders', $event))
    <button type="button"
            wire:click="openReminderModal"
            class="dash-event-action {{ $hasReminders ? 'is-active' : '' }}"
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            title="Configurer les rappels par email">
      <i class="ti ti-bell{{ $hasReminders ? '-ringing' : '' }}"></i>
    </button>
  @endif

  @if($isUpcoming && auth()->user()?->can('cancelRegistration', $event))
    <span data-bs-toggle="tooltip" data-bs-placement="top" title="Annuler l'inscription">
      <button type="button"
              class="dash-event-action dash-event-action--danger"
              data-bs-toggle="modal"
              data-bs-target="#{{ $modalId }}">
        <i class="ti ti-x"></i>
      </button>
    </span>
  @endif
</div>
