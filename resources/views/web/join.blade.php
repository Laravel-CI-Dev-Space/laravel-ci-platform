@extends('layouts.web')

@section('title', 'Join — Laravel CI')
@section('description', 'Join the Laravel Côte d\'Ivoire community. Free membership via GitHub — forum, events, jobs, and mentorship for Ivorian PHP developers.')

@section('content')

  <!-- HERO -->
  <section class="page-hero">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <span class="badge-pill badge-navy"><i class="fa-solid fa-user-plus text-orange"></i> Free · Open to all</span>
          <h1 class="my-3">Join the Ivorian Laravel community</h1>
          <p class="lead" style="max-width:42rem">Whether you are in Abidjan, Bouaké, or the diaspora — connect with developers who share your stack, your challenges, and your ambition.</p>
          <a href="{{ route('auth.github.redirect') }}" class="btn btn-brand btn-lg mt-2">
            <i class="fa-brands fa-github"></i> Continue with GitHub
          </a>
        </div>
        <div class="col-lg-5 d-none d-lg-block">
          <div class="mascot-art" style="width:clamp(200px,22vw,280px)">
            <span class="m-ring"></span><span class="m-blob"></span>
            <img src="{{ asset('assets/web/img/mascot.png') }}" alt="Laravel CI mascot" loading="lazy" />
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- BENEFITS -->
  <section class="section">
    <div class="container">
      <div class="text-center mb-5 reveal">
        <span class="section-eyebrow">Why join</span>
        <h2 class="section-heading">What you get as a member</h2>
      </div>
      <div class="row g-4">
        <div class="col-md-6 col-lg-4 reveal">
          <div class="card-soft h-100" style="padding:1.5rem">
            <div class="stat-icon mb-3"><i class="fa-solid fa-comments"></i></div>
            <h3 style="font-size:1.15rem">Forum &amp; peer support</h3>
            <p class="text-muted mb-0">Ask questions, share solutions, and get feedback from developers who understand the local context.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 reveal" data-delay="0.08">
          <div class="card-soft h-100" style="padding:1.5rem">
            <div class="stat-icon mb-3"><i class="fa-solid fa-calendar-check"></i></div>
            <h3 style="font-size:1.15rem">Events &amp; meetups</h3>
            <p class="text-muted mb-0">Attend workshops, webinars, and hackathons in Abidjan and online — grow your network IRL and remotely.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 reveal" data-delay="0.16">
          <div class="card-soft h-100" style="padding:1.5rem">
            <div class="stat-icon mb-3"><i class="fa-solid fa-briefcase"></i></div>
            <h3 style="font-size:1.15rem">Job opportunities</h3>
            <p class="text-muted mb-0">Discover Laravel &amp; PHP roles from Ivorian companies and international remote-friendly teams.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 reveal" data-delay="0.08">
          <div class="card-soft h-100" style="padding:1.5rem">
            <div class="stat-icon mb-3"><i class="fa-solid fa-book-open"></i></div>
            <h3 style="font-size:1.15rem">Blog &amp; resources</h3>
            <p class="text-muted mb-0">Read tutorials and guides written by community members — from first deploy to advanced architecture.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 reveal" data-delay="0.16">
          <div class="card-soft h-100" style="padding:1.5rem">
            <div class="stat-icon mb-3"><i class="fa-solid fa-handshake"></i></div>
            <h3 style="font-size:1.15rem">Mentorship &amp; visibility</h3>
            <p class="text-muted mb-0">Build your public profile, find mentors, and showcase your work to recruiters and collaborators.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 reveal" data-delay="0.24">
          <div class="card-soft h-100" style="padding:1.5rem">
            <div class="stat-icon mb-3"><i class="fa-solid fa-code-branch"></i></div>
            <h3 style="font-size:1.15rem">Open source together</h3>
            <p class="text-muted mb-0">Contribute to Laravel CI platform and community projects — learn by building real software.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- HOW TO JOIN -->
  <section class="section bg-light-2">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-6 reveal">
          <span class="section-eyebrow">Get started</span>
          <h2 class="section-heading">Sign up in under a minute</h2>
          <ol class="ps-3" style="color:var(--muted);line-height:2">
            <li>Click <strong>Continue with GitHub</strong> below</li>
            <li>Authorize Laravel CI (public profile + email)</li>
            <li>Complete your developer profile</li>
            <li>Start exploring the forum, events, and jobs</li>
          </ol>
          <p class="small text-muted mb-0"><i class="fa-solid fa-lock me-1"></i> We only request your public GitHub profile. No password to remember.</p>
        </div>
        <div class="col-lg-6 reveal" data-delay="0.1">
          <div class="card-soft" style="padding:2rem;text-align:center">
            <i class="fa-brands fa-github" style="font-size:3rem;color:var(--navy);margin-bottom:1rem"></i>
            <h3 style="font-size:1.25rem;margin-bottom:.5rem">Ready to join?</h3>
            <p class="text-muted mb-4">Free membership · No credit card · Instant access</p>
            <a href="{{ route('auth.github.redirect') }}" class="btn btn-brand btn-lg w-100">
              <i class="fa-brands fa-github"></i> Continue with GitHub
            </a>
            <p class="mt-3 mb-0 small text-muted">Already a member? <a href="{{ route('login') }}">Sign in</a></p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section class="section">
    <div class="container">
      <div class="text-center mb-5 reveal">
        <span class="section-eyebrow">FAQ</span>
        <h2 class="section-heading">Common questions</h2>
      </div>
      <div class="row justify-content-center">
        <div class="col-lg-8 reveal">
          <div class="accordion" id="joinFaq">
            <div class="accordion-item card-soft border-0 mb-3">
              <h3 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">
                  Is membership free?
                </button>
              </h3>
              <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#joinFaq">
                <div class="accordion-body text-muted">Yes. Laravel CI is a community-driven initiative. Membership is completely free — we only ask you to follow our code of conduct.</div>
              </div>
            </div>
            <div class="accordion-item card-soft border-0 mb-3">
              <h3 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
                  Who can join?
                </button>
              </h3>
              <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#joinFaq">
                <div class="accordion-body text-muted">Any developer interested in Laravel, PHP, or web development — students, juniors, seniors, freelancers, and entrepreneurs. You do not need to live in Côte d'Ivoire.</div>
              </div>
            </div>
            <div class="accordion-item card-soft border-0 mb-3">
              <h3 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
                  Why do I need a GitHub account?
                </button>
              </h3>
              <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#joinFaq">
                <div class="accordion-body text-muted">GitHub is our single sign-on provider. It lets us verify you are a real developer and sync your avatar and username automatically — no extra password to manage.</div>
              </div>
            </div>
            <div class="accordion-item card-soft border-0 mb-3">
              <h3 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">
                  What happens after I sign up?
                </button>
              </h3>
              <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#joinFaq">
                <div class="accordion-body text-muted">You will be asked to complete your developer profile (skills, experience, city). Then you can access the forum, register for events, apply to jobs, and write articles.</div>
              </div>
            </div>
            <div class="accordion-item card-soft border-0 mb-3">
              <h3 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false" aria-controls="faq5">
                  How can I contribute?
                </button>
              </h3>
              <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#joinFaq">
                <div class="accordion-body text-muted">Answer forum questions, write blog posts, volunteer at events, or contribute to our open-source platform on GitHub. Every skill level is welcome.</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="section pb-0">
    <div class="container">
      <div class="cta-banner reveal">
        <div class="row align-items-center position-relative" style="z-index:2">
          <div class="col-lg-8">
            <h2 style="color:#fff;font-size:var(--fs-h2)">Your seat at the table is waiting</h2>
            <p style="color:rgba(255,255,255,.88);margin-bottom:0">500+ developers are already here. Come build with us.</p>
          </div>
          <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
            <a href="{{ route('auth.github.redirect') }}" class="btn btn-light btn-lg"><i class="fa-brands fa-github"></i> Join now</a>
          </div>
        </div>
        <img class="cta-mascot" src="{{ asset('assets/web/img/mascot.png') }}" alt="" aria-hidden="true" loading="lazy" />
      </div>
    </div>
  </section>

@endsection
