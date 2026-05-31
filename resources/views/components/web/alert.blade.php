@props(['type' => 'info', 'dismissible' => true])

@php
  $map = [
    'success' => 'alert-success',
    'error'   => 'alert-danger',
    'warning' => 'alert-warning',
    'info'    => 'alert-info',
  ];
  $icons = [
    'success' => 'fa-solid fa-circle-check',
    'error'   => 'fa-solid fa-circle-xmark',
    'warning' => 'fa-solid fa-triangle-exclamation',
    'info'    => 'fa-solid fa-circle-info',
  ];
  $alertClass = $map[$type] ?? 'alert-info';
  $icon = $icons[$type] ?? 'fa-solid fa-circle-info';
@endphp

<div class="alert {{ $alertClass }} {{ $dismissible ? 'alert-dismissible fade show' : '' }}" role="alert">
  <i class="{{ $icon }} me-2"></i>
  {{ $slot }}
  @if($dismissible)
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  @endif
</div>
