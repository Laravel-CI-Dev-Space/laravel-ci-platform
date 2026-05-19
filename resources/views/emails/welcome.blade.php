<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue sur Laravel CI</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: #f5f5f5;
            color: #1C1C2E;
            padding: 2rem 1rem;
        }

        .wrapper { max-width: 600px; margin: 0 auto; }

        .header {
            background: #1C1C2E;
            border-radius: 12px 12px 0 0;
            padding: 2rem;
            text-align: center;
        }
        .header img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 3px solid #FF6600;
            margin-bottom: 1rem;
        }
        .header h1 { color: #FF6600; font-size: 1.5rem; font-weight: 800; }
        .header p { color: #aaa; font-size: 0.85rem; margin-top: 0.25rem; }

        .body { background: #ffffff; padding: 2rem; }

        .greeting { font-size: 1.2rem; font-weight: 700; color: #1C1C2E; margin-bottom: 1rem; }
        .greeting span { color: #FF6600; }

        .text { font-size: 0.95rem; color: #444; line-height: 1.7; margin-bottom: 1rem; }

        .benefits {
            background: #f9f9f9;
            border-radius: 10px;
            padding: 1.25rem 1.5rem;
            margin: 1.5rem 0;
        }
        .benefits h3 {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #888;
            margin-bottom: 0.75rem;
        }
        .benefit-item {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 0.6rem;
            font-size: 0.9rem;
            color: #333;
        }
        .benefit-dot {
            width: 8px;
            height: 8px;
            background: #FF6600;
            border-radius: 50%;
            flex-shrink: 0;
            margin-top: 6px;
        }

        .cta-primary { text-align: center; margin: 1.5rem 0; }
        .cta-primary a {
            display: inline-block;
            background: #FF6600;
            color: #ffffff;
            text-decoration: none;
            padding: 0.85rem 2rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 1rem;
        }

        .profile-block {
            background: linear-gradient(135deg, #1C1C2E 0%, #2d2d45 100%);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-left: 4px solid #FF6600;
        }
        .profile-block h3 { color: #FF6600; font-size: 1rem; font-weight: 700; margin-bottom: 0.5rem; }
        .profile-block p { color: #ccc; font-size: 0.88rem; line-height: 1.6; margin-bottom: 1rem; }
        .profile-block .progress-hint {
            background: rgba(255,255,255,0.08);
            border-radius: 6px;
            padding: 0.75rem 1rem;
            font-size: 0.82rem;
            color: #aaa;
            margin-bottom: 1rem;
        }
        .profile-block .progress-hint strong { color: #FF6600; }
        .profile-block a {
            display: inline-block;
            background: #FF6600;
            color: #fff;
            text-decoration: none;
            padding: 0.65rem 1.5rem;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.9rem;
        }

        .divider { border: none; border-top: 1px solid #eee; margin: 1.5rem 0; }

        .footer {
            background: #1C1C2E;
            border-radius: 0 0 12px 12px;
            padding: 1.5rem 2rem;
            text-align: center;
        }
        .footer p { color: #666; font-size: 0.78rem; line-height: 1.6; }
        .footer a { color: #FF6600; text-decoration: none; }
    </style>
</head>
<body>
<div class="wrapper">

    {{-- HEADER --}}
    <div class="header">
        <img src="{{ asset('assets/logo.jpeg') }}" alt="Laravel CI">
        <h1>Laravel Côte d'Ivoire</h1>
        <p>Hub communautaire des développeurs Laravel ivoiriens</p>
    </div>

    {{-- BODY --}}
    <div class="body">

        <p class="greeting">
            Bienvenue, <span>{{ $user->name }}</span> !
        </p>

        <p class="text">
            Nous sommes ravis de vous accueillir dans la communauté <strong>Laravel Côte d'Ivoire</strong> —
            la première communauté structurée dédiée aux développeurs Laravel en Côte d'Ivoire et dans la diaspora ivoirienne.
        </p>

        <p class="text">
            Votre compte GitHub <strong>@{{ $user->github_username }}</strong> a été connecté avec succès.
            Vous faites désormais partie d'une communauté de plus de 500 développeurs passionnés.
        </p>

        {{-- AVANTAGES --}}
        <div class="benefits">
            <h3>Ce qui vous attend</h3>
            <div class="benefit-item">
                <span class="benefit-dot"></span>
                <span><strong>Forum technique</strong> — Posez vos questions Laravel, répondez à celles des autres</span>
            </div>
            <div class="benefit-item">
                <span class="benefit-dot"></span>
                <span><strong>Blog &amp; ressources</strong> — Tutoriels, boilerplates et guides PDF</span>
            </div>
            <div class="benefit-item">
                <span class="benefit-dot"></span>
                <span><strong>Événements</strong> — Meetups, hackathons et talks en ligne</span>
            </div>
            <div class="benefit-item">
                <span class="benefit-dot"></span>
                <span><strong>Job Board</strong> — Offres Laravel en Côte d'Ivoire et en remote</span>
            </div>
        </div>

        {{-- CTA --}}
        <div class="cta-primary">
            <a href="{{ url('/dashboard') }}">Accéder à la plateforme &rarr;</a>
        </div>

        <hr class="divider">

        {{-- BLOC PROFIL --}}
        <div class="profile-block">
            <h3>Une étape importante vous attend</h3>
            <p>
                Votre profil est actuellement <strong style="color:#FF6600;">incomplet</strong>.
                Un profil complet vous permet d'être mieux visible dans la communauté,
                de postuler aux offres d'emploi et de participer pleinement aux échanges.
            </p>
            <div class="progress-hint">
                Taux de complétion actuel : <strong>0%</strong> —
                Complétez votre profil pour atteindre <strong>100%</strong> et débloquer toutes les fonctionnalités.
            </div>
            <p>
                Cela ne prend que <strong style="color:#FF6600;">5 minutes</strong>.
                Ajoutez votre pays, votre niveau Laravel, votre stack technique et votre situation professionnelle.
            </p>
            <a href="{{ url('/profil/completer') }}">Compléter mon profil maintenant &rarr;</a>
        </div>

    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <p>
            <strong style="color:#FF6600;">Laravel Côte d'Ivoire</strong> — 2026 — Open Source MIT
        </p>
        <p style="margin-top:0.5rem;">
            Vous recevez cet email car vous venez de vous inscrire sur
            <a href="{{ url('/') }}">laravelci.com</a>.
        </p>
        <p style="margin-top:0.5rem;">
            Des questions ? Contactez-nous sur
            <a href="mailto:contact@laravelci.com">contact@laravelci.com</a>
        </p>
    </div>

</div>
</body>
</html>
