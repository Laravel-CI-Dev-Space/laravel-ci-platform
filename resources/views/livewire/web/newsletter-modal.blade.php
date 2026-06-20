<div>
  {{-- Déclencheur : affiche le modal après 4s si pas encore vu --}}
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (!localStorage.getItem('newsletter_dismissed')) {
        setTimeout(function () {
          @this.set('open', true);
        }, 4000);
      }
    });
  </script>

  @if($open)
  <div
    class="newsletter-backdrop"
    x-data
    @click.self="$wire.dismiss(); localStorage.setItem('newsletter_dismissed', '1')"
    style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1050;display:flex;align-items:center;justify-content:center;padding:1rem"
  >
    <div class="newsletter-modal card-soft" style="width:100%;max-width:480px;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 20px 60px rgba(0,0,0,.2)">

      {{-- Header --}}
      <div style="background:linear-gradient(135deg,#1a1a2e 0%,#e8590c 100%);padding:28px 28px 24px;position:relative">
        <button
          wire:click="dismiss"
          x-on:click="localStorage.setItem('newsletter_dismissed','1')"
          style="position:absolute;top:12px;right:14px;background:rgba(255,255,255,.15);border:none;color:#fff;width:28px;height:28px;border-radius:50%;font-size:16px;line-height:1;cursor:pointer"
          aria-label="Fermer"
        >×</button>
        <div style="display:flex;align-items:center;gap:12px">
          <img src="{{ asset('assets/web/img/mascot.png') }}" alt="" style="height:56px" />
          <div>
            <div style="color:rgba(255,255,255,.75);font-size:12px;font-weight:600;text-transform:uppercase;letter-spacing:.5px">Laravel CI</div>
            <div style="color:#fff;font-size:20px;font-weight:700;line-height:1.2">Reste dans la boucle !</div>
          </div>
        </div>
      </div>

      {{-- Corps --}}
      <div style="padding:28px">
        @if($subscribed)
          <div style="text-align:center;padding:12px 0">
            <div style="font-size:48px;margin-bottom:12px">🎉</div>
            <h3 style="margin:0 0 8px;color:#1a1a2e">C'est dans la boîte !</h3>
            <p style="color:#666;margin:0 0 20px">Tu recevras les prochains articles et événements directement dans ta boîte mail.</p>
            <button
              wire:click="dismiss"
              x-on:click="localStorage.setItem('newsletter_dismissed','1')"
              class="btn btn-brand w-100"
            >Parfait, merci !</button>
          </div>
        @else
          <p style="color:#555;margin:0 0 20px;line-height:1.6">
            Reçois les <strong>nouveaux articles</strong> et <strong>événements</strong> de la communauté Laravel CI directement par email.
          </p>

          <form wire:submit="subscribe">
            <div class="mb-3">
              <input
                wire:model="name"
                type="text"
                class="form-control"
                placeholder="Ton prénom (optionnel)"
                style="border-radius:8px;border:1.5px solid #e0e0e0;padding:10px 14px"
              />
            </div>
            <div class="mb-3">
              <input
                wire:model="email"
                type="email"
                class="form-control @error('email') is-invalid @enderror"
                placeholder="Ton adresse email *"
                style="border-radius:8px;border:1.5px solid #e0e0e0;padding:10px 14px"
                required
              />
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <button type="submit" class="btn btn-brand w-100" wire:loading.attr="disabled">
              <span wire:loading.remove>S'abonner gratuitement</span>
              <span wire:loading><i class="fa-solid fa-spinner fa-spin"></i> Inscription…</span>
            </button>
          </form>
          <p style="font-size:11px;color:#aaa;margin:14px 0 0;text-align:center">
            Pas de spam. Désabonnement en un clic.
          </p>
        @endif
      </div>
    </div>
  </div>
  @endif
</div>
