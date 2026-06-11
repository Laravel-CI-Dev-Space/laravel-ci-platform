@if($variant === 'dashboard')
  <button type="button"
          wire:click="toggle"
          wire:loading.attr="disabled"
          class="dash-save-heart {{ $isFavorited ? 'is-saved' : '' }} {{ $animate ? 'is-popping' : '' }}"
          aria-label="{{ $isFavorited ? 'Retirer des favoris' : 'Enregistrer l\'offre' }}"
          aria-pressed="{{ $isFavorited ? 'true' : 'false' }}">
    <span wire:loading.remove wire:target="toggle">
      <i class="ti {{ $isFavorited ? 'ti-heart-filled' : 'ti-heart' }}"></i>
    </span>
    <span wire:loading wire:target="toggle">
      <i class="ti ti-loader-2 dash-save-heart__spinner"></i>
    </span>
  </button>
@else
  <button type="button"
          wire:click="toggle"
          wire:loading.attr="disabled"
          class="save-heart {{ $isFavorited ? 'saved' : '' }} {{ $animate ? 'is-popping' : '' }}"
          aria-label="{{ $isFavorited ? 'Retirer des favoris' : 'Enregistrer l\'offre' }}"
          aria-pressed="{{ $isFavorited ? 'true' : 'false' }}">
    <span wire:loading.remove wire:target="toggle">
      <i class="{{ $isFavorited ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
    </span>
    <span wire:loading wire:target="toggle">
      <i class="fa-solid fa-spinner fa-spin"></i>
    </span>
  </button>
@endif
