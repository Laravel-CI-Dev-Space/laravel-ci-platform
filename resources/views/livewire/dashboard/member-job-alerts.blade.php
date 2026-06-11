<div>
  <div class="card mb-4">
    <div class="card-body p-4">
      <h2 class="h5 mb-3">Nouvelle alerte</h2>
      <p class="text-secondary small mb-3">
        Recevez un email lorsqu'une offre publiée correspond à vos critères. Renseignez au moins un critère.
      </p>

      <form wire:submit="createAlert" class="row g-3">
        <div class="col-md-4">
          <label for="alert-keywords" class="form-label">Mots-clés</label>
          <input id="alert-keywords"
                 type="text"
                 wire:model="keywords"
                 class="form-control @error('keywords') is-invalid @enderror"
                 placeholder="Ex. laravel symfony">
          @error('keywords') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label for="alert-location" class="form-label">Lieu</label>
          <input id="alert-location"
                 type="text"
                 wire:model="location"
                 class="form-control @error('location') is-invalid @enderror"
                 placeholder="Ex. Abidjan">
          @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="col-md-4">
          <label for="alert-type" class="form-label">Type de contrat</label>
          <select id="alert-type"
                  wire:model="type"
                  class="form-select @error('type') is-invalid @enderror">
            <option value="">— Tous —</option>
            @foreach($contractTypes as $contractType)
              <option value="{{ $contractType->value }}">{{ $contractType->label() }}</option>
            @endforeach
          </select>
          @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        @error('criteria')
          <div class="col-12">
            <div class="alert alert-danger mb-0 py-2">{{ $message }}</div>
          </div>
        @enderror

        <div class="col-12">
          <button type="submit"
                  class="btn btn-primary"
                  wire:loading.attr="disabled"
                  wire:target="createAlert">
            <span wire:loading.remove wire:target="createAlert">
              <i class="ti ti-bell-plus me-1"></i> Créer l'alerte
            </span>
            <span wire:loading wire:target="createAlert">
              <i class="ti ti-loader-2 me-1"></i> Création…
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>

  <div class="row g-3">
    @forelse($this->alerts as $alert)
      <div class="col-12" wire:key="job-alert-{{ $alert->id }}">
        <div class="card">
          <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
              <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                  <h3 class="h6 mb-0">Alerte #{{ $alert->id }}</h3>
                  @if($alert->is_active)
                    <span class="badge bg-success-subtle text-success">Active</span>
                  @else
                    <span class="badge bg-secondary-subtle text-secondary">Inactive</span>
                  @endif
                </div>

                <ul class="list-unstyled mb-0 small text-secondary">
                  <li>
                    <strong>Mots-clés :</strong>
                    {{ $alert->keywords ?: '—' }}
                  </li>
                  <li>
                    <strong>Lieu :</strong>
                    {{ $alert->location ?: '—' }}
                  </li>
                  <li>
                    <strong>Type :</strong>
                    {{ $alert->type?->label() ?? '—' }}
                  </li>
                </ul>
              </div>

              <div class="d-flex gap-2 flex-wrap">
                <button type="button"
                        wire:click="toggleActive({{ $alert->id }})"
                        class="btn btn-sm {{ $alert->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                        wire:loading.attr="disabled"
                        wire:target="toggleActive({{ $alert->id }})">
                  {{ $alert->is_active ? 'Désactiver' : 'Activer' }}
                </button>
                <button type="button"
                        wire:click="openDeleteModal({{ $alert->id }})"
                        class="btn btn-sm btn-outline-danger">
                  <i class="ti ti-trash me-1"></i> Supprimer
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="card">
          <div class="card-body py-5 text-center text-secondary">
            <i class="ti ti-bell-ringing fs-1 d-block mb-3"></i>
            <h3 class="h5">Aucune alerte configurée</h3>
            <p class="mb-0">Créez une alerte pour être notifié des offres qui vous intéressent.</p>
          </div>
        </div>
      </div>
    @endforelse
  </div>

  @if($alertIdToDelete && $this->alertToDelete)
    @php $alert = $this->alertToDelete; @endphp
    <div class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true" style="background:rgba(15,15,20,.45);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Supprimer l'alerte</h5>
            <button type="button" class="btn-close" wire:click="closeDeleteModal" aria-label="Fermer"></button>
          </div>
          <div class="modal-body">
            <p class="mb-0">Confirmez-vous la suppression de cette alerte ?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" wire:click="closeDeleteModal">Annuler</button>
            <button type="button"
                    class="btn btn-danger"
                    wire:click="confirmDelete"
                    wire:loading.attr="disabled"
                    wire:target="confirmDelete">
              Supprimer
            </button>
          </div>
        </div>
      </div>
    </div>
  @endif
</div>
