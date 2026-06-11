<div>
  <div class="row g-3">
    @forelse($this->favorites as $job)
      @php $card = $job->toWebCardProps(); @endphp
      <div class="col-12" wire:key="dash-fav-row-{{ $job->id }}">
        <div class="card">
          <div class="card-body p-4">
            <div class="d-flex gap-3 align-items-start">
              <div class="company-logo {{ $card['logoClass'] }}">{{ $card['logoText'] }}</div>
              <div class="flex-grow-1 min-w-0">
                <div>
                  <h3 class="mb-1" style="font-size:1.1rem">
                    <a href="{{ route('jobs.show', $job) }}" class="text-navy">{{ $card['title'] }}</a>
                  </h3>
                  <div class="text-secondary small">
                    <strong>{{ $card['company'] }}</strong> · {{ $card['location'] }}
                    @if($card['remote'])
                      · <span class="badge bg-primary-subtle text-primary">Télétravail</span>
                    @endif
                  </div>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-2">
                  <span class="badge-pill badge-soft">{{ $card['contractType'] }}</span>
                  @foreach($card['tags'] as $tag)
                    <span class="tag">{{ $tag }}</span>
                  @endforeach
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                  <span class="salary">{{ $card['salary'] }}</span>
                  <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button type="button"
                            wire:click="openRemoveModal({{ $job->id }})"
                            class="btn btn-outline-danger btn-sm">
                      <i class="ti ti-trash me-1"></i> Supprimer
                    </button>
                    <a href="{{ route('jobs.show', $job) }}" class="btn btn-primary btn-sm">
                      Postuler <i class="ti ti-arrow-right ms-1"></i>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="card">
          <div class="card-body py-5 text-center text-secondary">
            <i class="ti ti-heart fs-1 d-block mb-3"></i>
            <h3 class="h5">Aucune offre sauvegardée</h3>
            <p class="mb-3">Enregistrez les offres qui vous intéressent depuis le job board.</p>
            <a href="{{ route('jobs.index') }}" class="btn btn-primary">Parcourir les offres</a>
          </div>
        </div>
      </div>
    @endforelse
  </div>

  @if($jobOfferIdToRemove && $this->jobOfferToRemove)
    @php $offer = $this->jobOfferToRemove; @endphp
    <div class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true" style="background:rgba(15,15,20,.45);">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Retirer des favoris</h5>
            <button type="button" class="btn-close" wire:click="closeRemoveModal" aria-label="Fermer"></button>
          </div>
          <div class="modal-body">
            <p class="mb-2">Confirmez-vous le retrait de cette offre de vos favoris ?</p>
            <p class="fw-semibold mb-1">{{ $offer->title }}</p>
            <p class="text-secondary small mb-0">{{ $offer->company->name }}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" wire:click="closeRemoveModal">Annuler</button>
            <button type="button"
                    class="btn btn-danger"
                    wire:click="confirmRemove"
                    wire:loading.attr="disabled"
                    wire:target="confirmRemove">
              <span wire:loading.remove wire:target="confirmRemove">
                <i class="ti ti-trash me-1"></i> Confirmer la suppression
              </span>
              <span wire:loading wire:target="confirmRemove">
                <i class="ti ti-loader-2 me-1"></i> Suppression…
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>
  @endif
</div>
