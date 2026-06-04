<div id="apply-form">
    @if ($submitted)
        <div class="card-soft p-4 text-center">
            <i class="fa-solid fa-circle-check fa-2x text-success mb-3"></i>
            <h4 class="text-navy mb-2">Candidature envoyée !</h4>
            <p class="text-muted-2 mb-0">
                Votre candidature a été transmise à l'entreprise. Bonne chance !
            </p>
        </div>
    @else
        <div class="card-soft p-4">
            <h3 class="mb-4" style="font-size:var(--fs-h3)">
                <i class="fa-solid fa-paper-plane me-2 text-orange"></i>Postuler à cette offre
            </h3>

            {{-- Lettre de motivation --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    Lettre de motivation
                    <span class="text-muted-2 fw-normal">(optionnel si CV fourni · min. 50 car.)</span>
                </label>
                <textarea wire:model.live="coverLetter"
                          class="form-control @error('coverLetter') is-invalid @enderror"
                          rows="8"
                          placeholder="Présentez-vous, expliquez pourquoi ce poste vous intéresse et ce que vous apportez à l'entreprise…">
                </textarea>
                @error('coverLetter')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <div class="text-muted-2 mt-1" style="font-size:.78rem">
                    {{ strlen($coverLetter) }} / 3000 caractères
                </div>
            </div>

            {{-- CV --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    CV
                    <span class="text-muted-2 fw-normal">(optionnel si lettre fournie · PDF, DOC, DOCX · max 5 Mo)</span>
                </label>
                <input type="file"
                       wire:model="cv"
                       class="form-control @error('cv') is-invalid @enderror"
                       accept=".pdf,.doc,.docx" />
                @error('cv')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <div wire:loading wire:target="cv" class="text-muted-2 mt-1" style="font-size:.78rem">
                    <i class="fa-solid fa-circle-notch fa-spin me-1"></i>Chargement du fichier…
                </div>
            </div>

            {{-- Portfolio --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    Portfolio
                    <span class="text-muted-2 fw-normal">(optionnel)</span>
                </label>
                <input type="url" wire:model.live="portfolioUrl"
                       class="form-control @error('portfolioUrl') is-invalid @enderror"
                       placeholder="https://monportfolio.dev" />
                @error('portfolioUrl')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- LinkedIn --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    Profil LinkedIn
                    <span class="text-muted-2 fw-normal">(optionnel)</span>
                </label>
                <input type="url" wire:model.live="linkedinUrl"
                       class="form-control @error('linkedinUrl') is-invalid @enderror"
                       placeholder="https://linkedin.com/in/votre-profil" />
                @error('linkedinUrl')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end gap-3">
                <button wire:click="submit" wire:loading.attr="disabled" class="btn btn-brand">
                    <span wire:loading wire:target="submit">
                        <i class="fa-solid fa-circle-notch fa-spin me-1"></i>Envoi en cours…
                    </span>
                    <span wire:loading.remove wire:target="submit">
                        <i class="fa-solid fa-paper-plane me-1"></i>Envoyer ma candidature
                    </span>
                </button>
            </div>
        </div>
    @endif
</div>
