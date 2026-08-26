@extends('layouts.web')

@section('title', 'Rejoindre · Laravel CI')
@section('description', "Rejoins la communauté Laravel Côte d'Ivoire. Adhésion gratuite via GitHub. Forum, événements, emplois et mentorat pour les développeurs PHP ivoiriens et de la diaspora.")

@section('content')

  <!-- HERO -->
  <section class="page-hero">
    <div class="container">
      <div class="row align-items-center g-4">
        <div class="col-lg-7">
          <span class="badge-pill badge-navy"><i class="fa-solid fa-user-plus text-orange"></i> Gratuit · Ouvert à tous</span>
          <h1 class="my-3">Rejoins la communauté Laravel ivoirienne</h1>
          <p class="lead" style="max-width:42rem">Que tu sois à Abidjan, à Bouaké ou dans la diaspora : connecte-toi avec des développeurs qui partagent ta stack, tes défis et tes ambitions.</p>
          <a href="{{ route('auth.github.redirect') }}" class="btn btn-brand btn-lg mt-2">
            <i class="fa-brands fa-github"></i> Continuer avec GitHub
          </a>
        </div>
        <div class="col-lg-5 d-none d-lg-block">
          <div class="mascot-art" style="width:clamp(200px,22vw,280px)">
            <span class="m-ring"></span><span class="m-blob"></span>
            <img src="{{ asset('assets/web/img/mascot.png') }}" alt="Mascotte Laravel CI" loading="lazy" />
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CHIFFRES CLÉS -->
  <section class="stats-strip">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-6 col-md-3 reveal">
          <div class="stat-item">
            <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
            <div class="stat-num"><span>1 100</span><span class="plus">+</span></div>
            <div class="stat-label">Membres</div>
          </div>
        </div>
        <div class="col-6 col-md-3 reveal" data-delay="0.08">
          <div class="stat-item">
            <div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
            <div class="stat-num"><span>12</span><span class="plus">+</span></div>
            <div class="stat-label">Événements organisés</div>
          </div>
        </div>
        <div class="col-6 col-md-3 reveal" data-delay="0.16">
          <div class="stat-item">
            <div class="stat-icon"><i class="fa-solid fa-code-branch"></i></div>
            <div class="stat-num"><span>3</span><span class="plus"></span></div>
            <div class="stat-label">Années d'existence</div>
          </div>
        </div>
        <div class="col-6 col-md-3 reveal" data-delay="0.24">
          <div class="stat-item">
            <div class="stat-icon"><i class="fa-solid fa-globe"></i></div>
            <div class="stat-num"><span>5</span><span class="plus"></span></div>
            <div class="stat-label">Communautés partenaires</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- POUR QUI -->
  <section class="section">
    <div class="container">
      <div class="text-center mb-5 reveal">
        <span class="section-eyebrow">Notre public</span>
        <h2 class="section-heading">Pour qui sommes-nous faits ?</h2>
        <p class="text-muted" style="max-width:42rem;margin-inline:auto">Laravel CI accueille tous les développeurs qui construisent avec Laravel et PHP, quel que soit ton niveau ou ton parcours.</p>
      </div>
      <div class="row g-4">
        <div class="col-md-6 col-lg-3 reveal">
          <div class="card-soft h-100" style="padding:1.75rem;text-align:center">
            <div class="value-icon mb-3"><i class="fa-solid fa-seedling"></i></div>
            <h3 style="font-size:1.1rem">Développeurs juniors</h3>
            <p class="text-muted mb-0" style="font-size:.9rem">0 à 3 ans d'expérience. Tu apprends, tu poses des questions, tu cherches des mentors. Tu es exactement là où tu dois être.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3 reveal" data-delay="0.08">
          <div class="card-soft h-100" style="padding:1.75rem;text-align:center">
            <div class="value-icon mb-3"><i class="fa-solid fa-code"></i></div>
            <h3 style="font-size:1.1rem">Développeurs expérimentés</h3>
            <p class="text-muted mb-0" style="font-size:.9rem">3+ ans de pratique. Tu partages ton expertise, tu guides les juniors et tu renforces ton réseau professionnel local et international.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3 reveal" data-delay="0.16">
          <div class="card-soft h-100" style="padding:1.75rem;text-align:center">
            <div class="value-icon mb-3"><i class="fa-solid fa-chalkboard-user"></i></div>
            <h3 style="font-size:1.1rem">Formateurs et enseignants</h3>
            <p class="text-muted mb-0" style="font-size:.9rem">Professeurs, instructeurs de bootcamp ou mentors. Tu trouves une communauté de pairs et un écosystème pour amplifier ton impact pédagogique.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3 reveal" data-delay="0.24">
          <div class="card-soft h-100" style="padding:1.75rem;text-align:center">
            <div class="value-icon mb-3"><i class="fa-solid fa-building"></i></div>
            <h3 style="font-size:1.1rem">Entreprises et recruteurs</h3>
            <p class="text-muted mb-0" style="font-size:.9rem">Accède à un vivier de développeurs Laravel qualifiés. Publie tes offres d'emploi et connecte-toi directement avec les talents ivoiriens.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- AVANTAGES MEMBRES -->
  <section class="section">
    <div class="container">
      <div class="text-center mb-5 reveal">
        <span class="section-eyebrow">Pourquoi rejoindre</span>
        <h2 class="section-heading">Ce que tu obtiens en tant que membre</h2>
      </div>
      <div class="row g-4">
        <div class="col-md-6 col-lg-4 reveal">
          <div class="card-soft h-100" style="padding:1.5rem">
            <div class="stat-icon mb-3"><i class="fa-solid fa-comments"></i></div>
            <h3 style="font-size:1.15rem">Forum et entraide</h3>
            <p class="text-muted mb-0">Pose tes questions, partage tes solutions et reçois des retours de développeurs qui comprennent le contexte local.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 reveal" data-delay="0.08">
          <div class="card-soft h-100" style="padding:1.5rem">
            <div class="stat-icon mb-3"><i class="fa-solid fa-calendar-check"></i></div>
            <h3 style="font-size:1.15rem">Événements et meetups</h3>
            <p class="text-muted mb-0">Participe à des workshops, webinaires et hackathons à Abidjan et en ligne. Développe ton réseau en présentiel et à distance.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 reveal" data-delay="0.16">
          <div class="card-soft h-100" style="padding:1.5rem">
            <div class="stat-icon mb-3"><i class="fa-solid fa-briefcase"></i></div>
            <h3 style="font-size:1.15rem">Opportunités d'emploi</h3>
            <p class="text-muted mb-0">Découvre des offres Laravel et PHP dans des entreprises ivoiriennes et des équipes internationales en remote.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 reveal" data-delay="0.08">
          <div class="card-soft h-100" style="padding:1.5rem">
            <div class="stat-icon mb-3"><i class="fa-solid fa-book-open"></i></div>
            <h3 style="font-size:1.15rem">Blog et ressources</h3>
            <p class="text-muted mb-0">Lis des tutoriels et guides écrits par des membres de la communauté, du premier déploiement à l'architecture avancée.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 reveal" data-delay="0.16">
          <div class="card-soft h-100" style="padding:1.5rem">
            <div class="stat-icon mb-3"><i class="fa-solid fa-handshake"></i></div>
            <h3 style="font-size:1.15rem">Mentorat et visibilité</h3>
            <p class="text-muted mb-0">Construis ton profil public, trouve des mentors et montre ton travail aux recruteurs et collaborateurs.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-4 reveal" data-delay="0.24">
          <div class="card-soft h-100" style="padding:1.5rem">
            <div class="stat-icon mb-3"><i class="fa-solid fa-code-branch"></i></div>
            <h3 style="font-size:1.15rem">Open source ensemble</h3>
            <p class="text-muted mb-0">Contribue à la plateforme Laravel CI et aux projets communautaires. Apprends en construisant de vrais logiciels.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- COMMENT REJOINDRE -->
  <section class="section bg-light-2">
    <div class="container">
      <div class="row align-items-center g-5">
        <div class="col-lg-6 reveal">
          <span class="section-eyebrow">Comment ça marche</span>
          <h2 class="section-heading">Rejoins en moins d'une minute</h2>
          <ol class="ps-3" style="color:var(--muted);line-height:2.2">
            <li>Clique sur <strong>Continuer avec GitHub</strong> ci-contre</li>
            <li>Autorise Laravel CI (profil public et email)</li>
            <li>Complète ton profil de développeur</li>
            <li>Accède au forum, aux événements et aux offres d'emploi</li>
          </ol>
          <p class="small text-muted mb-0"><i class="fa-solid fa-lock me-1"></i> Seul ton profil public GitHub est utilisé. Aucun mot de passe supplémentaire.</p>
        </div>
        <div class="col-lg-6 reveal" data-delay="0.1">
          <div class="card-soft" style="padding:2rem;text-align:center">
            <i class="fa-brands fa-github" style="font-size:3rem;color:var(--navy);margin-bottom:1rem"></i>
            <h3 style="font-size:1.25rem;margin-bottom:.5rem">Prêt à rejoindre ?</h3>
            <p class="text-muted mb-4">Adhésion gratuite · Sans carte bancaire · Accès immédiat</p>
            <a href="{{ route('auth.github.redirect') }}" class="btn btn-brand btn-lg w-100">
              <i class="fa-brands fa-github"></i> Continuer avec GitHub
            </a>
            <p class="mt-3 mb-0 small text-muted">Déjà membre ? <a href="{{ route('login') }}">Se connecter</a></p>
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
        <h2 class="section-heading">Questions fréquentes</h2>
      </div>
      <div class="row justify-content-center">
        <div class="col-lg-8 reveal">
          <div class="accordion" id="joinFaq">
            <div class="accordion-item card-soft border-0 mb-3">
              <h3 class="accordion-header">
                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">
                  L'adhésion est-elle gratuite ?
                </button>
              </h3>
              <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#joinFaq">
                <div class="accordion-body text-muted">Oui. Laravel CI est une initiative communautaire. L'adhésion est entièrement gratuite. Nous te demandons simplement de respecter notre code de conduite.</div>
              </div>
            </div>
            <div class="accordion-item card-soft border-0 mb-3">
              <h3 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
                  Qui peut rejoindre la communauté ?
                </button>
              </h3>
              <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#joinFaq">
                <div class="accordion-body text-muted">Tout développeur intéressé par Laravel, PHP ou le développement web : étudiants, juniors, seniors, freelances et entrepreneurs. Tu n'as pas besoin de vivre en Côte d'Ivoire.</div>
              </div>
            </div>
            <div class="accordion-item card-soft border-0 mb-3">
              <h3 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
                  Pourquoi utiliser GitHub pour se connecter ?
                </button>
              </h3>
              <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#joinFaq">
                <div class="accordion-body text-muted">GitHub est notre fournisseur d'authentification unique. Il nous permet de vérifier que tu es un vrai développeur et de synchroniser ton avatar et ton nom d'utilisateur automatiquement. Aucun mot de passe supplémentaire à gérer.</div>
              </div>
            </div>
            <div class="accordion-item card-soft border-0 mb-3">
              <h3 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" aria-expanded="false" aria-controls="faq4">
                  Que se passe-t-il après l'inscription ?
                </button>
              </h3>
              <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#joinFaq">
                <div class="accordion-body text-muted">Il te sera demandé de compléter ton profil de développeur (compétences, expérience, ville). Tu pourras ensuite accéder au forum, t'inscrire à des événements, postuler à des offres d'emploi et écrire des articles.</div>
              </div>
            </div>
            <div class="accordion-item card-soft border-0 mb-3">
              <h3 class="accordion-header">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" aria-expanded="false" aria-controls="faq5">
                  Comment puis-je contribuer ?
                </button>
              </h3>
              <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#joinFaq">
                <div class="accordion-body text-muted">Réponds aux questions du forum, rédige des articles, interviens bénévolement lors d'événements ou contribue à notre plateforme open source sur GitHub. Tous les niveaux sont les bienvenus.</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA FINAL -->
  <section class="section pb-5">
    <div class="container">
      <div class="cta-banner reveal">
        <div class="row align-items-center position-relative" style="z-index:2">
          <div class="col-lg-8">
            <h2 style="color:#fff;font-size:var(--fs-h2)">Ta place dans la communauté t'attend</h2>
            <p style="color:rgba(255,255,255,.88);margin-bottom:0">1 100+ développeurs sont déjà là. Viens construire avec nous.</p>
          </div>
          <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
            <a href="{{ route('auth.github.redirect') }}" class="btn btn-light btn-lg"><i class="fa-brands fa-github"></i> Rejoindre maintenant</a>
          </div>
        </div>
        <img class="cta-mascot" src="{{ asset('assets/web/img/mascot.png') }}" alt="" aria-hidden="true" loading="lazy" />
      </div>
    </div>
  </section>

@endsection
