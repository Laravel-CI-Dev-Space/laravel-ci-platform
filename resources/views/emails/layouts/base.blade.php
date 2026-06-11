<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('subject', 'Laravel CI')</title>
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
            background: #0F8A4F;
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

        .info-box {
            background: #f9f9f9;
            border-radius: 10px;
            padding: 1.25rem 1.5rem;
            margin: 1.5rem 0;
        }
        .info-box h3 {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #888;
            margin-bottom: 0.75rem;
        }
        .info-box .info-row {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            color: #333;
        }
        .info-box .info-row strong { color: #1C1C2E; min-width: 110px; display: inline-block; }

        .badge {
            display: inline-block;
            background: #FF6600;
            color: #fff;
            font-size: 0.78rem;
            font-weight: 700;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-green { background: #2ECC71; }
        .badge-navy { background: #0F8A4F; }

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

        .cta-secondary { text-align: center; margin: 1rem 0; }
        .cta-secondary a {
            display: inline-block;
            background: #ffffff;
            color: #0F8A4F;
            text-decoration: none;
            padding: 0.75rem 1.75rem;
            border-radius: 8px;
            font-weight: 700;
            font-size: 0.92rem;
            border: 2px solid #0F8A4F;
        }

        .credentials-box {
            background: linear-gradient(135deg, #0F8A4F 0%, #0B6B3D 100%);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border-left: 4px solid #FF6600;
            text-align: center;
        }
        .credentials-box .label { color: #aaa; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.4rem; }
        .credentials-box .value { color: #fff; font-size: 1.4rem; font-weight: 800; letter-spacing: 2px; font-family: 'Courier New', monospace; }

        .alert-box {
            background: #fff4e5;
            border-left: 4px solid #FF6600;
            border-radius: 6px;
            padding: 1rem 1.25rem;
            margin: 1.5rem 0;
            font-size: 0.88rem;
            color: #663c00;
            line-height: 1.6;
        }

        .offer-card {
            border: 1px solid #eee;
            border-radius: 10px;
            padding: 1rem 1.25rem;
            margin-bottom: 0.75rem;
        }
        .offer-card h4 { font-size: 1rem; color: #1C1C2E; margin-bottom: 0.35rem; }
        .offer-card .meta { font-size: 0.82rem; color: #888; margin-bottom: 0.5rem; }
        .offer-card a { color: #FF6600; text-decoration: none; font-weight: 700; font-size: 0.85rem; }

        .divider { border: none; border-top: 1px solid #eee; margin: 1.5rem 0; }

        .footer {
            background: #0F8A4F;
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
        <img src="{{ $message->embed(public_path('assets/logo.jpeg')) }}" alt="Laravel CI">
        <h1>Laravel Côte d'Ivoire</h1>
        <p>Hub communautaire des développeurs Laravel ivoiriens</p>
    </div>

    {{-- BODY --}}
    <div class="body">
        @yield('content')
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <p>
            © 2026 Laravel CI · MIT License · Built with ❤️ in Côte d'Ivoire
        </p>
        <p style="margin-top:0.5rem;">
            Des questions ? Contactez-nous sur
            <a href="mailto:contact@laravelci.com">contact@laravelci.com</a>
        </p>
        <p style="margin-top:0.5rem;">
            <a href="{!! $unsubscribeUrl ?? '#' !!}">Se désabonner de ces emails</a>
        </p>
    </div>

</div>
</body>
</html>
