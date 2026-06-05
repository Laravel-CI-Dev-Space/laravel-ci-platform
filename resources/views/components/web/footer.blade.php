<footer class="site-footer">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4 col-md-6">
        <a class="brand-logo mb-3" href="{{ route('home') }}">
          <span class="brand-mark"><img src="{{ asset('assets/web/img/logo-mark.png') }}" alt="Laravel CI" /></span> Laravel CI
        </a>
        <p style="max-width:22rem">The first structured developer community for Laravel &amp; PHP in Côte d'Ivoire and the Ivorian diaspora. African tech excellence, together.</p>
        <div class="social-row">
          @if($social['github'])
            <a href="{{ $social['github'] }}" class="social-icon" aria-label="GitHub" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-github"></i></a>
          @endif
          @if($social['linkedin'])
            <a href="{{ $social['linkedin'] }}" class="social-icon" aria-label="LinkedIn" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-linkedin-in"></i></a>
          @endif
          @if($social['whatsapp'])
            <a href="{{ $social['whatsapp'] }}" class="social-icon" aria-label="WhatsApp" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-whatsapp"></i></a>
          @endif
          @if($social['twitter'])
            <a href="{{ $social['twitter'] }}" class="social-icon" aria-label="X" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-x-twitter"></i></a>
          @endif
        </div>
      </div>
      <div class="col-lg-2 col-md-6 col-6">
        <h5>Quick Links</h5>
        <ul class="footer-links">
          <li><a href="{{ route('forum.index') }}">Forum</a></li>
          <li><a href="{{ route('blog.index') }}">Blog</a></li>
          <li><a href="{{ route('events.index') }}">Events</a></li>
          <li><a href="{{ route('jobs.index') }}">Jobs</a></li>
        </ul>
      </div>
      <div class="col-lg-3 col-md-6 col-6">
        <h5>Community</h5>
        <ul class="footer-links">
          <li><a href="{{ route('about') }}">About us</a></li>
          <li><a href="{{ route('join') }}">Join us</a></li>
          <li><a href="{{ route('forum.index') }}">Code of conduct</a></li>
          @if($social['github'])
            <li><a href="{{ $social['github'] }}" target="_blank" rel="noopener noreferrer">Contribute on GitHub</a></li>
          @endif
        </ul>
      </div>
      <div class="col-lg-3 col-md-6">
        <h5>Contact</h5>
        <ul class="footer-links">
          <li><span><i class="fa-solid fa-location-dot me-2"></i>Abidjan, Côte d'Ivoire</span></li>
          <li><a href="mailto:hello@laravel.ci"><i class="fa-solid fa-envelope me-2"></i>hello@laravel.ci</a></li>
          @if($social['whatsapp'])
            <li><a href="{{ $social['whatsapp'] }}" target="_blank" rel="noopener noreferrer"><i class="fa-brands fa-whatsapp me-2"></i>Join WhatsApp group</a></li>
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
