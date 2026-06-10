<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annulation événement</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f5f5; color: #1C1C2E; padding: 2rem 1rem; margin: 0; }
        .wrapper { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden; }
        .header { background: #1C1C2E; padding: 1.5rem 2rem; text-align: center; }
        .header h1 { color: #FF6600; font-size: 1.25rem; margin: 0; }
        .body { padding: 2rem; line-height: 1.6; }
        .event-title { font-size: 1.1rem; font-weight: 700; margin: 1rem 0 0.5rem; }
        .meta { color: #666; font-size: 0.9rem; margin-bottom: 1rem; }
        .btn { display: inline-block; background: #FF6602; color: #fff !important; text-decoration: none; padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 700; margin-top: 1rem; }
        .footer { padding: 1rem 2rem; background: #f9f9f9; font-size: 0.8rem; color: #888; text-align: center; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Laravel CI</h1>
        </div>
        <div class="body">
            <p>Bonjour <strong>{{ $user->name }}</strong>,</p>
            <p>Votre inscription à l'événement suivant a bien été annulée :</p>
            <p class="event-title">{{ $event->title }}</p>
            <p class="meta">
                {{ $event->start_date->translatedFormat('l j F Y · H:i') }}
                @if($event->location)
                    <br>{{ $event->location }}
                @elseif($event->meeting_link)
                    <br>En ligne
                @endif
            </p>
            <p>Vous pouvez vous réinscrire à tout moment si des places sont encore disponibles.</p>
            <a href="{{ route('events.index') }}" class="btn">
                Parcourir les événements
            </a>
        </div>
        <div class="footer">
            Laravel Côte d'Ivoire — Communauté open source
        </div>
    </div>
</body>
</html>
