@props([
    'title'   => '',
    'value'   => '',
    'change'  => '',
    'icon'    => 'ti ti-chart-bar',
    'color'   => 'primary',
    'positive' => true,
])

<div class="card p-4 bg-{{ $color }} bg-opacity-10 border border-{{ $color }} border-opacity-25 rounded-2">
  <div class="d-flex gap-3">
    <div class="icon-shape icon-md bg-{{ $color }} text-white rounded-2">
      <i class="{{ $icon }} fs-4"></i>
    </div>
    <div>
      <h2 class="mb-3 fs-6">{{ $title }}</h2>
      <h3 class="fw-bold mb-0">{{ $value }}</h3>
      @if($change)
        <p class="text-{{ $color }} mb-0 small">{{ $positive ? '+' : '' }}{{ $change }}</p>
      @endif
    </div>
  </div>
</div>
