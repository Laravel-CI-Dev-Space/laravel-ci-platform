<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1e293b; }
  h1   { font-size: 14px; margin: 0 0 4px; color: #1e293b; }
  .sub { font-size: 8px; color: #64748b; margin-bottom: 12px; }
  table { width: 100%; border-collapse: collapse; }
  th {
    background: #f97316; color: #fff; text-align: left;
    padding: 5px 6px; font-size: 8px; font-weight: 600; text-transform: uppercase; letter-spacing: .04em;
  }
  td { padding: 4px 6px; border-bottom: 1px solid #f1f5f9; }
  tr:nth-child(even) td { background: #f8fafc; }
  .badge-member  { background: #dbeafe; color: #1d4ed8; border-radius: 3px; padding: 1px 5px; }
  .badge-guest   { background: #fef3c7; color: #92400e; border-radius: 3px; padding: 1px 5px; }
  .badge-ok      { background: #d1fae5; color: #065f46; border-radius: 3px; padding: 1px 5px; }
  .badge-wait    { background: #fef3c7; color: #92400e; border-radius: 3px; padding: 1px 5px; }
  .badge-cancel  { background: #fee2e2; color: #991b1b; border-radius: 3px; padding: 1px 5px; }
  .footer        { margin-top: 10px; font-size: 7px; color: #94a3b8; text-align: right; }
</style>
</head>
<body>
  <h1>Liste des inscriptions — Laravel CI</h1>
  <div class="sub">
    {{ $event ? 'Événement : ' . $event->title . ' · ' : '' }}Exporté le {{ now()->format('d/m/Y à H:i') }}
    · {{ $registrations->count() }} inscription(s){{ $truncated ? ' sur ' . $total . ' (export limité, utilisez le CSV pour la liste complète)' : '' }}
  </div>

  <table>
    <thead>
      <tr>
        <th>Type</th>
        <th>Nom</th>
        <th>Email</th>
        <th>WhatsApp</th>
        <th>Événement</th>
        <th>Statut</th>
        <th>Paiement</th>
        <th>Montant</th>
        <th>Ticket</th>
        <th>Inscrit le</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($registrations as $r)
      <tr>
        <td>
          @if ($r->isMember())
            <span class="badge-member">Membre</span>
          @else
            <span class="badge-guest">Invité</span>
          @endif
        </td>
        <td>{{ $r->display_name }}</td>
        <td>{{ $r->email }}</td>
        <td>{{ $r->whatsapp ?? '—' }}</td>
        <td>{{ $r->event?->title ?? '—' }}</td>
        <td>
          @php
            $cls = match($r->status) { 'confirmed' => 'badge-ok', 'waitlisted' => 'badge-wait', default => 'badge-cancel' };
            $lbl = match($r->status) { 'confirmed' => 'Confirmé', 'waitlisted' => 'En attente', default => 'Annulé' };
          @endphp
          <span class="{{ $cls }}">{{ $lbl }}</span>
        </td>
        <td>
          @php
            $lbl = match($r->payment_status ?? '') { 'paid' => 'Payé', 'pending' => 'En attente', default => 'Gratuit' };
          @endphp
          {{ $lbl }}
        </td>
        <td>
          {{ $r->amount_paid && $r->amount_paid > 0 ? number_format((float)$r->amount_paid, 0, ',', ' ') . ' ' . ($r->event?->currency ?? 'XOF') : '—' }}
        </td>
        <td style="font-family:monospace">{{ $r->ticket_number ?? '—' }}</td>
        <td>{{ $r->registered_at?->format('d/m/Y H:i') ?? '—' }}</td>
      </tr>
      @empty
      <tr><td colspan="10" style="text-align:center;color:#94a3b8;padding:12px">Aucune inscription</td></tr>
      @endforelse
    </tbody>
  </table>

  <div class="footer">Laravel CI Platform · Généré automatiquement</div>
</body>
</html>
