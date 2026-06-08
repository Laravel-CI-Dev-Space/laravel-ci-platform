<footer class="site-footer">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4 col-md-6">
        <a class="brand-logo mb-3" href="{{ route('home') }}">
          <span class="brand-mark"><img src="{{ asset('assets/web/img/logo-mark.png') }}" alt="{{ $siteName }}" /></span> {{ $siteName }}
        </a>
        <p style="max-width:22rem">The first structured developer community for Laravel &amp; PHP in Côte d'Ivoire and the Ivorian diaspora. African tech excellence, together.</p>
        <div class="social-row">
          @foreach($social as $link)
            @if(!empty($link['url']))
              <a href="{{ $link['url'] }}" class="social-icon" aria-label="{{ ucfirst($link['platform']) }}" target="_blank" rel="noopener noreferrer">
                <i class="{{ $socialIconClasses[$link['platform']] ?? 'fa-solid fa-link' }}"></i>
              </a>
            @endif
          @endforeach
        </div>
      </div>
      <div class="col-lg-2 col-md-6 col-6">
        <h5>Quick Links</h5>
        <ul class="footer-links">
          @foreach($quickLinks as $link)
            <li><a href="{{ route($link['route']) }}">{{ $link['label'] }}</a></li>
          @endforeach
        </ul>
      </div>
      <div class="col-lg-3 col-md-6 col-6">
        <h5>Community</h5>
        <ul class="footer-links">
          @foreach($communityLinks as $link)
            <li>
              @if(!empty($link['external']))
                <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer">{{ $link['label'] }}</a>
              @else
                <a href="{{ route($link['route']) }}">{{ $link['label'] }}</a>
              @endif
            </li>
          @endforeach
        </ul>
      </div>
      <div class="col-lg-3 col-md-6">
        <h5>Contact</h5>
        <ul class="footer-links">
          <li><span><i class="fa-solid fa-location-dot me-2"></i>Abidjan, Côte d'Ivoire</span></li>
          <li><a href="mailto:hello@laravel.ci"><i class="fa-solid fa-envelope me-2"></i>hello@laravel.ci</a></li>
          @if($whatsappUrl)
            <li><a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-whatsapp me-2"></i>Join WhatsApp group</a></li>
          @endif
        </ul>
      </div>
    </div>

    <div class="footer-mascot-wrap" aria-hidden="true">
      <img class="footer-mascot" src="{{ asset('assets/web/img/mascot.png') }}" alt="" loading="lazy" />
    </div>

    <div class="footer-bottom">
      <span>© {{ date('Y') }} Laravel Côte d'Ivoire · <span class="mono">MIT License</span></span>
      <span>Built with <i class="fa-solid fa-heart heart"></i> in Côte d'Ivoire</span>
    </div>
  </div>
</footer>
