<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{ $article->title }}</title>
  <style>
    body { margin:0; padding:0; background:#f4f6f8; font-family:'Segoe UI',Arial,sans-serif; color:#1a1a2e; }
    .wrapper { max-width:600px; margin:32px auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,.08); }
    .header { background:linear-gradient(135deg,#1a1a2e 0%,#e8590c 100%); padding:32px 40px; text-align:center; }
    .header img { height:48px; }
    .header h1 { color:#fff; margin:16px 0 0; font-size:22px; font-weight:700; }
    .body { padding:36px 40px; }
    .label { display:inline-block; background:#fff3ec; color:#e8590c; border-radius:20px; padding:4px 14px; font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.5px; margin-bottom:16px; }
    .title { font-size:24px; font-weight:700; line-height:1.35; margin:0 0 16px; color:#1a1a2e; }
    .excerpt { color:#555; line-height:1.7; margin:0 0 28px; }
    .btn { display:inline-block; background:#e8590c; color:#fff !important; padding:14px 32px; border-radius:8px; text-decoration:none; font-weight:600; font-size:15px; }
    .meta { margin-top:24px; padding-top:20px; border-top:1px solid #f0f0f0; font-size:13px; color:#888; }
    .footer { background:#f4f6f8; padding:24px 40px; text-align:center; font-size:12px; color:#aaa; }
    .footer a { color:#aaa; text-decoration:underline; }
  </style>
</head>
<body>
  <div class="wrapper">
    <div class="header">
      <img src="{{ asset('assets/web/img/logo-mark.png') }}" alt="Laravel CI" />
      <h1>Nouvel article publié</h1>
    </div>
    <div class="body">
      @if($subscriber->name)
        <p>Bonjour {{ $subscriber->name }},</p>
      @else
        <p>Bonjour,</p>
      @endif

      <span class="label">Article</span>
      <div class="title">{{ $article->title }}</div>
      <div class="excerpt">{{ Str::limit(strip_tags($article->body ?? ''), 200) }}</div>

      <a href="{{ route('blog.show', $article->slug) }}" class="btn">Lire l'article →</a>

      <div class="meta">
        Par <strong>{{ $article->author->name ?? 'La communauté' }}</strong>
        @if($article->published_at)
          · publié le {{ $article->published_at->format('d/m/Y') }}
        @endif
      </div>
    </div>
    <div class="footer">
      Tu reçois cet email car tu es abonné à Laravel Côte d'Ivoire.<br />
      <a href="{{ route('newsletter.unsubscribe', $subscriber->token) }}">Se désabonner</a>
    </div>
  </div>
</body>
</html>
