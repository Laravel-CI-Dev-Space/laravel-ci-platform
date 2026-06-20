<div>
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
    x-data
    @click.self="$wire.dismiss(); localStorage.setItem('newsletter_dismissed', '1')"
    style="position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1050;display:flex;align-items:center;justify-content:center;padding:1rem"
  >
    <div style="width:100%;max-width:420px;background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 16px 48px rgba(0,0,0,.22)">

      {{-- Header compact --}}
      <div style="background:linear-gradient(135deg,#1a1a2e 0%,#e8590c 100%);padding:14px 18px;position:relative;display:flex;align-items:center;gap:10px">
        <img src="{{ asset('assets/web/img/mascot.png') }}" alt="" style="height:40px;flex-shrink:0" />
        <div>
          <div style="color:rgba(255,255,255,.7);font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.5px">Laravel CI</div>
          <div style="color:#fff;font-size:17px;font-weight:700;line-height:1.2">Reste dans la boucle !</div>
        </div>
        <button
          wire:click="dismiss"
          x-on:click="localStorage.setItem('newsletter_dismissed','1')"
          style="position:absolute;top:10px;right:12px;background:rgba(255,255,255,.15);border:none;color:#fff;width:26px;height:26px;border-radius:50%;font-size:15px;line-height:1;cursor:pointer"
          aria-label="Fermer"
        >×</button>
      </div>

      {{-- Corps compact --}}
      <div style="padding:18px 20px">
        @if($subscribed)
          <div style="text-align:center;padding:4px 0 8px">
            <div style="font-size:36px;margin-bottom:8px">🎉</div>
            <h3 style="margin:0 0 6px;color:#1a1a2e;font-size:17px">C'est dans la boîte !</h3>
            <p style="color:#666;margin:0 0 14px;font-size:13px">Tu recevras les prochains articles et événements par email.</p>
            <button wire:click="dismiss" x-on:click="localStorage.setItem('newsletter_dismissed','1')" class="btn btn-brand w-100">Parfait, merci !</button>
          </div>
        @else
          <p style="color:#555;margin:0 0 14px;font-size:13px;line-height:1.5">
            Reçois les <strong>nouveaux articles</strong> et <strong>événements</strong> directement par email.
          </p>
          <form wire:submit="subscribe">
            <div style="display:flex;flex-direction:column;gap:8px">
              <input
                wire:model="name"
                type="text"
                class="form-control form-control-sm"
                placeholder="Ton prénom (optionnel)"
                style="border-radius:7px;border:1.5px solid #e0e0e0"
              />
              <div>
                <input
                  wire:model="email"
                  type="email"
                  class="form-control form-control-sm @error('email') is-invalid @enderror"
                  placeholder="Ton adresse email *"
                  style="border-radius:7px;border:1.5px solid #e0e0e0"
                  required
                />
                @error('email')
                  <div class="invalid-feedback" style="font-size:11px">{{ $message }}</div>
                @enderror
              </div>
              <button type="submit" class="btn btn-brand btn-sm w-100" wire:loading.attr="disabled">
                <span wire:loading.remove>S'abonner gratuitement</span>
                <span wire:loading><i class="fa-solid fa-spinner fa-spin"></i> Inscription…</span>
              </button>
            </div>
          </form>
          <p style="font-size:11px;color:#bbb;margin:10px 0 0;text-align:center">Pas de spam · Désabonnement en un clic</p>
        @endif
      </div>
    </div>
  </div>
  @endif
</div>
