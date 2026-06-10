<div>
  <form wire:submit="submit" class="sidebar-card p-4">
    <fieldset class="mb-4">
      <legend class="sidebar-title border-0 p-0 mb-3">Entreprise</legend>
      <div class="mb-3">
        <label for="company_name" class="form-label fw-semibold">Nom *</label>
        <input type="text" id="company_name" wire:model="company_name" required class="form-control">
        @error('company_name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
      </div>
      <div class="mb-0">
        <label for="company_description" class="form-label fw-semibold">Description</label>
        <textarea id="company_description" wire:model="company_description" rows="2" class="form-control"></textarea>
        @error('company_description')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
      </div>
    </fieldset>

    <fieldset class="mb-4">
      <legend class="sidebar-title border-0 p-0 mb-3">Offre</legend>
      <div class="mb-3">
        <label for="title" class="form-label fw-semibold">Intitulé du poste *</label>
        <input type="text" id="title" wire:model="title" required class="form-control">
        @error('title')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
      </div>
      <div class="mb-3">
        <label for="type" class="form-label fw-semibold">Type de contrat *</label>
        <select id="type" wire:model="type" required class="form-select">
          <option value="">Choisir…</option>
          @foreach($this->offerTypes as $offerType)
            <option value="{{ $offerType->value }}">{{ $offerType->label() }}</option>
          @endforeach
        </select>
        @error('type')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
      </div>
      <div class="mb-3">
        <label for="location" class="form-label fw-semibold">Localisation *</label>
        <input type="text" id="location" wire:model="location" required
               class="form-control" placeholder="Abidjan, Remote…">
        @error('location')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
      </div>
      <div class="mb-0">
        <label for="description" class="form-label fw-semibold">Description *</label>
        <textarea id="description" wire:model="description" rows="8" required class="form-control"></textarea>
        @error('description')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
      </div>
    </fieldset>

    <button type="submit" wire:loading.attr="disabled" class="btn btn-brand w-100 btn-lg">
      <span wire:loading.remove wire:target="submit">
        <i class="fa-solid fa-paper-plane"></i> Soumettre l'offre
      </span>
      <span wire:loading wire:target="submit">
        <i class="fa-solid fa-spinner fa-spin"></i> Envoi…
      </span>
    </button>
  </form>
</div>
