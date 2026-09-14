<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #1e293b; }

        .header { background: #e7222c; color: #fff; padding: 14px 20px; margin-bottom: 16px; }
        .header h1 { font-size: 15px; font-weight: 700; }
        .header p  { font-size: 9px; opacity: .85; margin-top: 3px; }

        .stats { display: flex; gap: 12px; margin: 0 20px 14px; }
        .stat  { flex: 1; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 12px; }
        .stat .n { font-size: 18px; font-weight: 700; color: #e7222c; }
        .stat .l { font-size: 8px; color: #64748b; text-transform: uppercase; letter-spacing: .04em; }

        .event-meta { margin: 0 20px 14px; display: flex; gap: 20px; font-size: 9px; color: #475569; }
        .event-meta span { display: flex; align-items: center; gap: 4px; }

        table { width: calc(100% - 40px); margin: 0 20px; border-collapse: collapse; }
        thead tr { background: #1e293b; color: #fff; }
        thead th { padding: 7px 8px; text-align: left; font-size: 8px; text-transform: uppercase; letter-spacing: .04em; font-weight: 600; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 5px 8px; border-bottom: 1px solid #f1f5f9; font-size: 9px; }

        .badge { display: inline-block; padding: 1px 6px; border-radius: 9999px; font-size: 7.5px; font-weight: 600; }
        .badge-membre  { background: #dbeafe; color: #1d4ed8; }
        .badge-invite  { background: #dcfce7; color: #15803d; }
        .badge-confirmed { background: #dcfce7; color: #15803d; }

        .footer { margin-top: 16px; padding: 8px 20px; border-top: 1px solid #e2e8f0; font-size: 8px; color: #94a3b8; display: flex; justify-content: space-between; }
    </style>
</head>
<body>

    <div class="header">
        <h1>{{ $event->title }}</h1>
        <p>Liste des inscriptions — exportée le {{ now()->format('d/m/Y à H:i') }}</p>
    </div>

    <div class="event-meta">
        <span>📅 {{ $event->starts_at->translatedFormat('l d MMMM Y') }}</span>
        <span>🕐 {{ $event->starts_at->format('H:i') }} – {{ $event->ends_at->format('H:i') }}</span>
        @if($event->location)
            <span>📍 {{ $event->location }}</span>
        @endif
        <span>{{ ucfirst($event->type->label()) }} · {{ $event->status->label() }}</span>
    </div>

    <div class="stats">
        <div class="stat">
            <div class="n">{{ $stats['total_membres'] + $stats['total_invites'] }}</div>
            <div class="l">Total inscrits</div>
        </div>
        <div class="stat">
            <div class="n">{{ $stats['total_membres'] }}</div>
            <div class="l">Membres plateforme</div>
        </div>
        <div class="stat">
            <div class="n">{{ $stats['total_invites'] }}</div>
            <div class="l">Invités externes</div>
        </div>
        <div class="stat">
            <div class="n">{{ $stats['attended'] }}</div>
            <div class="l">Présents confirmés</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Type</th>
                <th>Prénom</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Statut</th>
                <th>Inscrit le</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <span class="badge {{ $row['Type'] === 'Membre' ? 'badge-membre' : 'badge-invite' }}">
                            {{ $row['Type'] }}
                        </span>
                    </td>
                    <td>{{ $row['Prénom'] }}</td>
                    <td>{{ $row['Nom'] }}</td>
                    <td>{{ $row['Email'] }}</td>
                    <td><span class="badge badge-confirmed">{{ $row['Statut'] }}</span></td>
                    <td>{{ $row['Inscrit le'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <span>Laravel Côte d'Ivoire — laravel-ci.com</span>
        <span>{{ $rows->count() }} inscription(s) au total</span>
    </div>

</body>
</html>
