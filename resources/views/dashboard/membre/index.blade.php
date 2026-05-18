<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Laravel CI</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: sans-serif; background: #f5f5f5; color: #1C1C2E; }

        .navbar {
            background: #1C1C2E;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar .brand { color: #FF6600; font-weight: bold; font-size: 1.2rem; }
        .navbar .user  { display: flex; align-items: center; gap: 1rem; color: #fff; }
        .navbar img    { width: 36px; height: 36px; border-radius: 50%; border: 2px solid #FF6600; }

        .container { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }

        .alert-suspension {
            background: #FFF3CD;
            border-left: 5px solid #FF6600;
            border-radius: 6px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .alert-suspension .icon  { font-size: 1.5rem; }
        .alert-suspension .text  { flex: 1; }
        .alert-suspension .title { font-weight: bold; color: #FF6600; margin-bottom: 0.25rem; }
        .alert-suspension .desc  { font-size: 0.9rem; color: #555; }

        .card {
            background: #fff;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }
        .card h2 {
            font-size: 1rem;
            color: #888;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .profile     { display: flex; align-items: center; gap: 1.5rem; }
        .profile img { width: 72px; height: 72px; border-radius: 50%; border: 3px solid #FF6600; }
        .profile .info h3 { font-size: 1.2rem; color: #1C1C2E; }
        .profile .info a  { color: #FF6600; font-size: 0.9rem; text-decoration: none; }

        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            margin-top: 0.5rem;
        }
        .badge.actif    { background: #D5F5E3; color: #1E8449; }
        .badge.suspendu { background: #FFF3CD; color: #B7770D; }

        .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 1.5rem; }
        .stat-card {
            background: #fff;
            border-radius: 10px;
            padding: 1.25rem;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            border-top: 3px solid #FF6600;
        }
        .stat-card .number { font-size: 2rem; font-weight: bold; color: #FF6600; }
        .stat-card .label  { font-size: 0.85rem; color: #888; margin-top: 0.25rem; }

        .actions { display: flex; gap: 1rem; flex-wrap: wrap; }
        .btn {
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            font-size: 0.9rem;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
            border: none;
            display: inline-block;
        }
        .btn-primary  { background: #FF6600; color: #fff; }
        .btn-disabled { background: #ddd; color: #999; cursor: not-allowed; pointer-events: none; }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; }
        .info-item label { font-size: 0.8rem; color: #888; display: block; margin-bottom: 0.2rem; }
        .info-item span  { font-size: 0.95rem; color: #1C1C2E; font-weight: 500; }
        .info-item a     { color: #FF6600; text-decoration: none; }

        form { display: inline; }
        .logout {
            background: none;
            border: 1px solid #ccc;
            color: #fff;
            padding: 0.4rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
        }
        .logout:hover { background: #fee; color: #c00; border-color: #c00; }
    </style>
</head>
<body>

{{-- NAVBAR --}}
<nav class="navbar">
    <span class="brand">🐘 Laravel CI</span>
    <div class="user">
        <img src="{{ auth()->user()->avatar }}" alt="avatar">
        <span>{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout">Déconnexion</button>
        </form>
    </div>
</nav>

<div class="container">

    {{-- BANNIÈRE SUSPENSION --}}
    @if(auth()->user()->isSuspended())
        <div class="alert-suspension">
            <span class="icon">⚠️</span>
            <div class="text">
                <div class="title">Compte suspendu temporairement</div>
                <div class="desc">
                    Votre compte est suspendu encore
                    <strong>{{ auth()->user()->suspensionDaysLeft() }} jour(s)</strong>
                    (jusqu'au {{ auth()->user()->suspended_until->format('d/m/Y à H:i') }}).
                    Vous pouvez consulter le contenu mais vous ne pouvez effectuer aucune action.
                </div>
            </div>
        </div>
    @endif

    {{-- PROFIL --}}
    <div class="card">
        <h2>Mon profil</h2>
        <div class="profile">
            <img src="{{ auth()->user()->avatar }}" alt="avatar">
            <div class="info">
                <h3>{{ auth()->user()->name }}</h3>
                <a href="{{ auth()->user()->githubUrl() }}" target="_blank">
                    {{ '@' . auth()->user()->github_username }}
                </a>
                <br>
                @if(auth()->user()->isSuspended())
                    <span class="badge suspendu">⚠️ Suspendu</span>
                @else
                    <span class="badge actif">✅ Membre actif</span>
                @endif
            </div>
        </div>
    </div>

    {{-- STATS --}}
    <div class="stats">
        <div class="stat-card">
            <div class="number">0</div>
            <div class="label">Questions posées</div>
        </div>
        <div class="stat-card">
            <div class="number">0</div>
            <div class="label">Réponses données</div>
        </div>
        <div class="stat-card">
            <div class="number">0</div>
            <div class="label">Articles publiés</div>
        </div>
    </div>

    {{-- ACTIONS --}}
    <div class="card">
        <h2>Actions rapides</h2>
        <div class="actions">
            @if(auth()->user()->isSuspended())
                <span class="btn btn-disabled">✏️ Poser une question</span>
                <span class="btn btn-disabled">📝 Écrire un article</span>
                <span class="btn btn-disabled">💼 Postuler à une offre</span>
            @else
                <a href="#" class="btn btn-primary">✏️ Poser une question</a>
                <a href="#" class="btn btn-primary">📝 Écrire un article</a>
                <a href="#" class="btn btn-primary">💼 Postuler à une offre</a>
            @endif
        </div>
    </div>

    {{-- INFOS COMPTE --}}
    <div class="card">
        <h2>Informations du compte</h2>
        <div class="info-grid">
            <div class="info-item">
                <label>Email</label>
                <span>{{ auth()->user()->email }}</span>
            </div>
            <div class="info-item">
                <label>Membre depuis</label>
                <span>{{ auth()->user()->created_at->format('d/m/Y') }}</span>
            </div>
            <div class="info-item">
                <label>Dernière connexion</label>
                <span>{{ auth()->user()->last_login_at?->format('d/m/Y à H:i') ?? '—' }}</span>
            </div>
            <div class="info-item">
                <label>GitHub</label>
                <span>
                    <a href="{{ auth()->user()->githubUrl() }}" target="_blank">
                        {{ '@' . auth()->user()->github_username }}
                    </a>
                </span>
            </div>
            <div class="info-item">
                <label>Rôle</label>
                <span>{{ auth()->user()->getRoleNames()->first() ?? '—' }}</span>
            </div>
            @if(auth()->user()->isSuspended())
                <div class="info-item">
                    <label>Suspension levée le</label>
                    <span style="color: #B7770D; font-weight: bold;">
                        {{ auth()->user()->suspended_until->format('d/m/Y à H:i') }}
                    </span>
                </div>
            @endif
        </div>
    </div>

</div>
</body>
</html>
