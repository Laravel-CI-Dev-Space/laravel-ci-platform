<div>
    @if ($submitted)
        {{-- Confirmation --}}
        <div class="text-center py-3">
            <div class="mb-3">
                @if ($isWaitlisted)
                    <div style="width:56px;height:56px;border-radius:50%;background:#fef3c7;display:flex;align-items:center;justify-content:center;margin:0 auto">
                        <i class="fa-solid fa-clock" style="font-size:1.5rem;color:#d97706"></i>
                    </div>
                @else
                    <div style="width:56px;height:56px;border-radius:50%;background:#d1fae5;display:flex;align-items:center;justify-content:center;margin:0 auto">
                        <i class="fa-solid fa-circle-check" style="font-size:1.5rem;color:#059669"></i>
                    </div>
                @endif
            </div>
            <h5 class="fw-semibold mb-2" style="color:{{ $isWaitlisted ? '#92400e' : '#065f46' }}">
                {{ $isWaitlisted ? "Ajouté(e) à la liste d'attente" : 'Inscription confirmée !' }}
            </h5>
            <p class="text-muted mb-0" style="font-size:.88rem">
                @if ($isWaitlisted)
                    Vous serez contacté(e) à <strong>{{ $email }}</strong> si une place se libère.
                @else
                    Un email de confirmation a été envoyé à <strong>{{ $email }}</strong>.
                @endif
            </p>
        </div>
    @else
        {{-- Formulaire --}}
        <form wire:submit="submit" enctype="multipart/form-data">

            {{-- Prénom / Nom --}}
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label small fw-medium mb-1">
                        Prénom <span class="text-danger">*</span>
                    </label>
                    <input type="text" wire:model="firstName"
                           class="form-control form-control-sm @error('firstName') is-invalid @enderror"
                           placeholder="Kouamé">
                    @error('firstName')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-6">
                    <label class="form-label small fw-medium mb-1">
                        Nom <span class="text-danger">*</span>
                    </label>
                    <input type="text" wire:model="lastName"
                           class="form-control form-control-sm @error('lastName') is-invalid @enderror"
                           placeholder="Koffi">
                    @error('lastName')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Email --}}
            <div class="mb-3">
                <label class="form-label small fw-medium mb-1">
                    Email <span class="text-danger">*</span>
                </label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fa-regular fa-envelope"></i></span>
                    <input type="email" wire:model="email"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="kouame@exemple.com">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- WhatsApp --}}
            <div class="mb-3">
                <label class="form-label small fw-medium mb-1">WhatsApp</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="fa-brands fa-whatsapp" style="color:#25d366"></i></span>
                    <input type="tel" wire:model="whatsapp"
                           class="form-control @error('whatsapp') is-invalid @enderror"
                           placeholder="+225 07 00 00 00 00">
                    @error('whatsapp')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Photo --}}
            <div class="mb-3">
                <label class="form-label small fw-medium mb-1">
                    Photo <span class="text-muted">(optionnel)</span>
                </label>
                <input type="file" wire:model="photo" accept="image/*"
                       class="form-control form-control-sm @error('photo') is-invalid @enderror">
                <div class="form-text">JPG, PNG ou WebP — max 2 Mo</div>
                @error('photo')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                @if ($photo)
                    <div class="mt-2">
                        <img src="{{ $photo->temporaryUrl() }}"
                             style="width:52px;height:52px;border-radius:50%;object-fit:cover;border:2px solid #fed7aa"
                             alt="Aperçu">
                    </div>
                @endif
            </div>

            {{-- Code promo --}}
            <div class="mb-3">
                <label class="form-label small fw-medium mb-1">
                    <i class="fa-solid fa-tag me-1" style="color:#f97316"></i>Code promo
                </label>
                <input type="text" wire:model="promoCode"
                       class="form-control form-control-sm font-monospace text-uppercase @error('promoCode') is-invalid @enderror"
                       placeholder="EARLYBIRD">
                @error('promoCode')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Submit --}}
            <button type="submit"
                    wire:loading.attr="disabled"
                    class="btn btn-brand w-100">
                <span wire:loading.remove>
                    <i class="fa-solid fa-ticket me-1"></i> S'inscrire à l'événement
                </span>
                <span wire:loading>
                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                    Inscription en cours…
                </span>
            </button>

        </form>
    @endif
</div>
