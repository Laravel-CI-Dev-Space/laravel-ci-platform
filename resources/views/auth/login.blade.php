<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Laravel CI</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: sans-serif;
            background: #1C1C2E;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 1rem;
        }

        .login-card {
            background: #fff;
            border-radius: 16px;
            padding: 2.5rem 2rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            text-align: center;
        }

        /* Logo */
        .logo-wrapper {
            margin-bottom: 1.5rem;
        }
        .logo-wrapper img {
            width: 90px;
            height: 90px;
            object-fit: contain;
            border-radius: 50%;
            border: 3px solid #FF6600;
            padding: 4px;
            background: #fff;
        }

        /* Titre */
        .login-card h1 {
            font-size: 1.6rem;
            color: #1C1C2E;
            font-weight: 800;
            margin-bottom: 0.25rem;
        }
        .login-card h1 span { color: #FF6600; }

        .login-card .subtitle {
            font-size: 0.9rem;
            color: #888;
            margin-bottom: 2rem;
        }

        /* Séparateur */
        .divider {
            border: none;
            border-top: 1px solid #eee;
            margin: 1.5rem 0;
        }

        /* Message d'erreur */
        .alert-error {
            background: #FDECEA;
            border-left: 4px solid #C0392B;
            border-radius: 6px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #C0392B;
        }

        /* Message succès */
        .alert-success {
            background: #D5F5E3;
            border-left: 4px solid #2ECC71;
            border-radius: 6px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #1E8449;
        }

        /* Bouton GitHub */
        .btn-github {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            width: 100%;
            padding: 0.85rem 1.5rem;
            background: #1C1C2E;
            color: #fff;
            border-radius: 8px;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 700;
            transition: background 0.2s, transform 0.1s;
            border: 2px solid transparent;
        }
        .btn-github:hover {
            background: #FF6600;
            transform: translateY(-1px);
        }
        .btn-github svg {
            width: 22px;
            height: 22px;
            fill: #fff;
            flex-shrink: 0;
        }

        /* Note bas de page */
        .login-note {
            font-size: 0.8rem;
            color: #aaa;
            margin-top: 1.5rem;
            line-height: 1.5;
        }
        .login-note a {
            color: #FF6600;
            text-decoration: none;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.78rem;
            color: #555;
        }
        .footer span { color: #FF6600; }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-card">

        {{-- LOGO --}}
        <div class="logo-wrapper">
            <img src="{{ asset('assets/logo.jpeg') }}" alt="Laravel CI Logo">
        </div>

        {{-- TITRE --}}
        <h1>Laravel <span>CI</span></h1>
        <p class="subtitle">Hub communautaire des développeurs Laravel ivoiriens</p>

        <hr class="divider">

        {{-- MESSAGES --}}
        @if(session('error'))
            <div class="alert-error">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert-success">
                ✅ {{ session('success') }}
            </div>
        @endif

        {{-- BOUTON GITHUB --}}
        <a href="{{ route('auth.github.redirect') }}" class="btn-github">
            {{-- Icône GitHub SVG --}}
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303
                3.438 9.8 8.205 11.385.6.113.82-.258.82-.577
                0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422
                18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729
                1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305
                3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93
                0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176
                0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405
                1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23
                3.285-1.23.645 1.653.24 2.873.12 3.176.765.84
                1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475
                5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015
                3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592
                24 12.297c0-6.627-5.373-12-12-12"/>
            </svg>
            Se connecter avec GitHub
        </a>

        {{-- NOTE --}}
        <p class="login-note">
            En vous connectant, vous acceptez les
            <a href="#">conditions d'utilisation</a>
            de la plateforme Laravel CI.
        </p>

    </div>

    {{-- FOOTER --}}
    <div class="footer">
        🇨🇮 <span>Laravel Côte d'Ivoire</span> 2026 Open Source MIT
    </div>
</div>

</body>
</html>
