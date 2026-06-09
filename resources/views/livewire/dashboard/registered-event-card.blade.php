@php
  $isUpcoming = ! $event->isPast();
  $modalId = 'cancelEventModal-' . $event->id;
  $coverUrl = $event->coverUrl();
@endphp

<div>
  @if($variant === 'row')
    <div class="dash-event-row" wire:key="event-row-{{ $registration->id }}">
      <a href="{{ route('events.show', $event) }}" class="dash-event-row__thumb" aria-hidden="true" tabindex="-1">
        @if($coverUrl)
          <img src="{{ $coverUrl }}" alt="" loading="lazy" class="dash-event-row__img">
        @else
          <div class="dash-event-row__fallback"><i class="ti ti-calendar-event"></i></div>
        @endif
        <div class="dash-event-row__date">
          <span>{{ $event->start_date->translatedFormat('M') }}</span>
          <strong>{{ $event->start_date->format('d') }}</strong>
        </div>
      </a>

      <div class="dash-event-row__main">
        <a href="{{ route('events.show', $event) }}" class="dash-event-row__title">{{ $event->title }}</a>
        <div class="dash-event-row__meta">
          {{ $event->location ?? 'En ligne' }} · {{ $event->start_date->format('H:i') }}
          @if($registration->wantsReminders() && $isUpcoming)
            · <span class="dash-event-row__reminder"><i class="ti ti-bell-ringing"></i> {{ $registration->reminderTypesLabel() }}</span>
          @endif
        </div>
      </div>

      @include('livewire.dashboard.partials.event-action-bar')
    </div>
  @else
    <article class="dash-event-card" wire:key="event-card-{{ $registration->id }}">
      <a href="{{ route('events.show', $event) }}" class="dash-event-card__cover">
        @if($coverUrl)
          <img src="{{ $coverUrl }}" alt="{{ $event->title }}" loading="lazy" class="dash-event-card__img">
        @else
          <div class="dash-event-card__fallback">
            <i class="ti ti-calendar-event"></i>
          </div>
        @endif
        <div class="dash-event-card__cover-overlay"></div>

        <div class="dash-event-card__cover-top">
          <span class="dash-event-status {{ $isUpcoming ? 'dash-event-status--upcoming' : 'dash-event-status--past' }}">
            <span class="dash-event-status__dot"></span>
            {{ $isUpcoming ? 'À venir' : 'Passé' }}
          </span>
          @if($event->type)
            <span class="dash-event-card__type">{{ $event->type->name }}</span>
          @endif
        </div>

        <div class="dash-event-date dash-event-date--cover">
          <div class="dash-event-date__month">{{ $event->start_date->translatedFormat('M') }}</div>
          <div class="dash-event-date__day">{{ $event->start_date->format('d') }}</div>
        </div>
      </a>

      <div class="dash-event-card__body">
        <a href="{{ route('events.show', $event) }}" class="dash-event-card__title">{{ $event->title }}</a>

        <div class="dash-event-card__meta">
          <span><i class="ti ti-clock"></i> {{ $event->start_date->translatedFormat('H:i') }} — {{ $event->end_date->format('H:i') }}</span>
          <span><i class="ti ti-map-pin"></i> {{ $event->location ?? 'En ligne' }}</span>
        </div>

        @if($isUpcoming)
          <span class="dash-event-reminder-badge {{ $registration->wantsReminders() ? 'is-active' : '' }}">
            <i class="ti ti-bell{{ $registration->wantsReminders() ? '-ringing' : '-off' }}"></i>
            {{ $registration->wantsReminders() ? $registration->reminderTypesLabel() : 'Aucun rappel configuré' }}
          </span>
        @endif
      </div>

      @include('livewire.dashboard.partials.event-action-bar')
    </article>
  @endif

  @if($isUpcoming && auth()->user()?->can('cancelRegistration', $event))
    <x-dashboard.cancel-event-modal :event="$event" />
  @endif

  @include('livewire.partials.event-reminder-modal', ['modalId' => 'reminderModal-' . $event->id])
</div>
