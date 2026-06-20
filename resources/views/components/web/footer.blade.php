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
          <li><a href="{{ route('events.index') }}">Events</a></li>
          <li><a href="{{ route('jobs.index') }}">Jobs</a></li>
        </ul>
      </div>

      {{-- Colonne 3 : Communauté --}}
      <div class="col-lg-3 col-md-6 col-6">
        <h5>{{ $col2Title }}</h5>
        <ul class="footer-links">
          <li><a href="{{ route('about') }}">About us</a></li>
          <li><a href="{{ route('join') }}">Join us</a></li>
          <li><a href="{{ $cocUrl }}">Code of conduct</a></li>
          @if($github)
            <li><a href="{{ $github }}" target="_blank" rel="noopener noreferrer">{{ $githubLabel }}</a></li>
          @endif
        </ul>
      </div>

      {{-- Colonne 4 : Contact --}}
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
