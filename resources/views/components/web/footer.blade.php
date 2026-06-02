<footer class="site-footer">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4 col-md-6">
        <a class="brand-logo mb-3" href="{{ route('home') }}">
          <span class="brand-mark"><img src="{{ asset('assets/web/img/logo-mark.png') }}" alt="Laravel CI" /></span> Laravel CI
        </a>
        <p style="max-width:22rem">The first structured developer community for Laravel &amp; PHP in Côte d'Ivoire and the Ivorian diaspora. African tech excellence, together.</p>
        <div class="social-row">
          <a href="{{ route('login') }}" class="social-icon" aria-label="GitHub"><i class="fa-brands fa-github"></i></a>
          <a href="{{ route('login') }}" class="social-icon" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
          <a href="{{ route('login') }}" class="social-icon" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
          <a href="{{ route('login') }}" class="social-icon" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
        </div>
      </div>
      <div class="col-lg-2 col-md-6 col-6">
        <h5>Quick Links</h5>
        <ul class="footer-links">
          <li><a href="{{ route('forum.index') }}">Forum</a></li>
          <li><a href="{{ route('blog.index') }}">Blog</a></li>
          <li><a href="#">Events</a></li>
          <li><a href="#">Jobs</a></li>
        </ul>
      </div>
      <div class="col-lg-3 col-md-6 col-6">
        <h5>Community</h5>
        <ul class="footer-links">
          <li><a href="{{ route('about') }}">About us</a></li>
          <li><a href="{{ route('login') }}">Become a member</a></li>
          <li><a href="{{ route('forum.index') }}">Code of conduct</a></li>
          <li><a href="https://github.com" target="_blank" rel="noopener">Contribute on GitHub</a></li>
        </ul>
      </div>
      <div class="col-lg-3 col-md-6">
        <h5>Contact</h5>
        <ul class="footer-links">
          <li><a href="#"><i class="fa-solid fa-location-dot me-2"></i>Abidjan, Côte d'Ivoire</a></li>
          <li><a href="mailto:hello@laravel.ci"><i class="fa-solid fa-envelope me-2"></i>hello@laravel.ci</a></li>
          <li><a href="{{ route('login') }}"><i class="fa-brands fa-whatsapp me-2"></i>Join WhatsApp group</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© {{ date('Y') }} Laravel Côte d'Ivoire · <span class="mono">MIT License</span></span>
      <span>Built with <i class="fa-solid fa-heart heart"></i> in Côte d'Ivoire</span>
    </div>
  </div>
</footer>
