@props(['title' => 'Nothing here yet', 'message' => '', 'icon' => 'fa-solid fa-inbox', 'action' => null, 'actionLabel' => ''])

<div class="text-center py-5">
  <div class="mb-4" style="font-size:3rem;color:var(--muted);opacity:.4">
    <i class="{{ $icon }}"></i>
  </div>
  <h3 class="text-navy mb-2" style="font-size:var(--fs-h3)">{{ $title }}</h3>
  @if($message)
    <p class="text-muted-2 mb-4">{{ $message }}</p>
  @endif
  @if($action)
    <a href="{{ $action }}" class="btn btn-brand">{{ $actionLabel ?: 'Get started' }}</a>
  @endif
</div>
