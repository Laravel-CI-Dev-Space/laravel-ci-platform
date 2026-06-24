@php
  $brandName   = $globalSettings->get('identity_brand_name')?->value ?? 'Laravel CI';
  $logoMark    = $globalSettings->get('identity_logo_mark')?->value;
  $logoMarkUrl = $logoMark ? asset('assets/' . $logoMark) : asset('assets/web/img/logo-mark.png');

  $tagline      = $globalSettings->get('footer_tagline')?->value
                  ?? "The first structured developer community for Laravel & PHP in Côte d'Ivoire.";
  $col1Title    = $globalSettings->get('footer_col1_title')?->value ?? 'Quick Links';
  $col2Title    = $globalSettings->get('footer_col2_title')?->value ?? 'Community';
  $col3Title    = $globalSettings->get('footer_col3_title')?->value ?? 'Contact';
  $location     = $globalSettings->get('footer_contact_location')?->value ?? "Abidjan, Côte d'Ivoire";
  $email        = $globalSettings->get('footer_contact_email')?->value ?? 'hello@laravel.ci';
  $whatsappLabel = $globalSettings->get('footer_whatsapp_label')?->value ?? 'Join WhatsApp group';
  $githubLabel  = $globalSettings->get('footer_github_label')?->value ?? 'Contribute on GitHub';
  $cocUrl       = $globalSettings->get('footer_code_of_conduct_url')?->value ?: route('forum.index');
  $copyright    = $globalSettings->get('footer_copyright')?->value ?? "Laravel Côte d'Ivoire · MIT License";
  $builtWith    = $globalSettings->get('footer_built_with')?->value ?? "Built with ♥ in Côte d'Ivoire";

  $github    = $globalSettings->get('social_github')?->value;
  $linkedin  = $globalSettings->get('social_linkedin')?->value;
  $twitter   = $globalSettings->get('social_twitter')?->value;
  $whatsapp  = $globalSettings->get('social_whatsapp')?->value;
@endphp

<footer class="site-footer">
  <div class="container">

    <div class="row g-4">

      {{-- Colonne 1 : Brand + tagline + réseaux sociaux --}}
      <div class="col-lg-4 col-md-6">
        <a class="brand-logo mb-3" href="{{ route('home') }}">
          <span class="brand-mark"><img src="{{ $logoMarkUrl }}" alt="{{ $brandName }}" /></span>
          {{ $brandName }}
        </a>
        <p style="max-width:22rem">{{ $tagline }}</p>
        <div class="social-row">
          @if($github)
            <a href="{{ $github }}" class="social-icon" target="_blank" rel="noopener noreferrer" aria-label="GitHub">
              <i class="fa-brands fa-github"></i>
            </a>
          @endif
          @if($linkedin)
            <a href="{{ $linkedin }}" class="social-icon" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn">
              <i class="fa-brands fa-linkedin-in"></i>
            </a>
          @endif
          @if($whatsapp)
            <a href="{{ $whatsapp }}" class="social-icon" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">
              <i class="fa-brands fa-whatsapp"></i>
            </a>
          @endif
          @if($twitter)
            <a href="{{ $twitter }}" class="social-icon" target="_blank" rel="noopener noreferrer" aria-label="X / Twitter">
              <i class="fa-brands fa-x-twitter"></i>
            </a>
          @endif
          {{-- Fallback si aucun réseau renseigné --}}
          @if(!$github && !$linkedin && !$whatsapp && !$twitter)
            <a href="{{ route('login') }}" class="social-icon" aria-label="GitHub"><i class="fa-brands fa-github"></i></a>
            <a href="{{ route('login') }}" class="social-icon" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
            <a href="{{ route('login') }}" class="social-icon" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
            <a href="{{ route('login') }}" class="social-icon" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
          @endif
        </div>
      </div>

      {{-- Colonne 2 : Liens rapides --}}
      <div class="col-lg-2 col-md-6 col-6">
        <h5>{{ $col1Title }}</h5>
        <ul class="footer-links">
          <li><a href="{{ route('forum.index') }}">Forum</a></li>
          <li><a href="{{ route('blog.index') }}">Blog</a></li>
          <li><a href="{{ route('events.index') }}">Événements</a></li>
          <li><a href="{{ route('jobs.index') }}">Emplois</a></li>
        </ul>
      </div>

      {{-- Colonne 3 : Communauté --}}
      <div class="col-lg-3 col-md-6 col-6">
        <h5>{{ $col2Title }}</h5>
        <ul class="footer-links">
          <li><a href="{{ route('about') }}">À propos</a></li>
          <li><a href="{{ route('join') }}">Rejoindre</a></li>
          <li><a href="{{ $cocUrl }}">Code de conduite</a></li>
          @if($github)
            <li><a href="{{ $github }}" target="_blank" rel="noopener noreferrer">{{ $githubLabel }}</a></li>
          @endif
        </ul>
      </div>

      {{-- Colonne 4 : Contact + Newsletter --}}
      <div class="col-lg-3 col-md-6">
        <h5>{{ $col3Title }}</h5>
        <ul class="footer-links">
          @if($location)
            <li>
              <a href="#"><i class="fa-solid fa-location-dot me-2"></i>{{ $location }}</a>
            </li>
          @endif
          @if($email)
            <li>
              <a href="mailto:{{ $email }}"><i class="fa-solid fa-envelope me-2"></i>{{ $email }}</a>
            </li>
          @endif
          @if($whatsapp)
            <li>
              <a href="{{ $whatsapp }}" target="_blank" rel="noopener noreferrer">
                <i class="fa-brands fa-whatsapp me-2"></i>{{ $whatsappLabel }}
              </a>
            </li>
          @endif
        </ul>

        {{-- Newsletter compact (AJAX) --}}
        <div style="margin-top:1.5rem">
          <p style="font-size:.78rem;font-weight:700;color:var(--orange);letter-spacing:.08em;text-transform:uppercase;margin-bottom:.6rem">Newsletter</p>
          <p id="nl-ok" style="display:none;color:#4ade80;font-size:.82rem;margin:0"><i class="fa-solid fa-circle-check me-1"></i>Inscrit !</p>
          <form id="nl-form" style="display:flex;gap:.4rem">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="email" name="email" required placeholder="ton@email.com"
                   style="flex:1;min-width:0;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.15);border-radius:.4rem;padding:.4rem .7rem;color:#fff;font-size:.82rem;outline:none"
                   onfocus="this.style.borderColor='var(--orange)'" onblur="this.style.borderColor='rgba(255,255,255,.15)'">
            <button type="submit" id="nl-btn" style="background:var(--orange);border:none;border-radius:.4rem;padding:.4rem .75rem;color:#fff;font-size:.82rem;cursor:pointer;transition:opacity .2s">
              <i class="fa-solid fa-paper-plane"></i>
            </button>
          </form>
          <p id="nl-err" style="display:none;color:#f87171;font-size:.75rem;margin:.35rem 0 0">Adresse invalide ou déjà inscrite.</p>
        </div>
        <script>
        (function () {
          var form = document.getElementById('nl-form');
          if (!form) return;
          form.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn  = document.getElementById('nl-btn');
            var ok   = document.getElementById('nl-ok');
            var err  = document.getElementById('nl-err');
            var email = form.querySelector('[name=email]').value;
            var token = form.querySelector('[name=_token]').value;
            btn.style.opacity = '.5';
            err.style.display = 'none';
            fetch('{{ route('newsletter.subscribe') }}', {
              method : 'POST',
              headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
              body   : JSON.stringify({ email: email }),
            })
            .then(function (r) {
              btn.style.opacity = '1';
              if (r.ok) { form.style.display = 'none'; ok.style.display = 'block'; }
              else { err.style.display = 'block'; }
            })
            .catch(function () { btn.style.opacity = '1'; err.style.display = 'block'; });
          });
        })();
        </script>
      </div>

    </div>

    <div class="footer-mascot-wrap" aria-hidden="true">
      <img class="footer-mascot" src="{{ asset('assets/web/img/mascot.png') }}" alt="" loading="lazy" />
    </div>

    <div class="footer-bottom">
      <span>© {{ date('Y') }} {{ $copyright }}</span>
      <span>{{ $builtWith }}</span>
    </div>
  </div>
</footer>
