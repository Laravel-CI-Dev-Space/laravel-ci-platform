<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    @font-face {
        font-family: 'DejaVu Sans';
        font-style: normal;
        font-weight: normal;
        src: url('{{ storage_path("fonts/dejavu-sans/DejaVuSans.ttf") }}') format('truetype');
    }
    * { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; }
    body { margin: 15px; color: #1e293b; }
    h1 { font-size: 16px; color: #f97316; margin-bottom: 4px; }
    .meta { color: #64748b; font-size: 9px; margin-bottom: 12px; }
    table { width: 100%; border-collapse: collapse; }
    thead tr { background-color: #f97316; color: #fff; }
    thead th { padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: .04em; }
    tbody tr:nth-child(even) { background-color: #f8fafc; }
    tbody tr:nth-child(odd)  { background-color: #ffffff; }
    tbody td { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; }
    .badge { padding: 2px 6px; border-radius: 4px; font-size: 8px; font-weight: bold; }
    .badge-active    { background: #dcfce7; color: #166534; }
    .badge-suspended { background: #fef9c3; color: #854d0e; }
    .badge-banned    { background: #fee2e2; color: #991b1b; }
    .badge-admin     { background: #ffedd5; color: #9a3412; }
    .badge-member    { background: #eff6ff; color: #1e40af; }
    .badge-moderator { background: #f0fdf4; color: #166534; }
    .badge-super     { background: #fef2f2; color: #7f1d1d; }
    .footer { margin-top: 10px; font-size: 8px; color: #94a3b8; text-align: right; }
</style>
</head>
<body>

<h1>Liste des membres — Laravel CI Platform</h1>
<p class="meta">
    Généré le {{ now()->format('d/m/Y à H:i') }} · {{ $members->count() }} membre(s){{ $truncated ? ' sur ' . $total . ' (export limité, utilisez le CSV pour la liste complète)' : '' }}
</p>

<table>
    <thead>
        <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>GitHub</th>
            <th>Rôle</th>
            <th>Statut</th>
            <th>Dernière connexion</th>
            <th>Inscrit le</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($members as $user)
            @php
                $role   = $user->roles->first()?->name ?? 'member';
                $status = match (true) {
                    !$user->is_active                                                         => 'banned',
                    $user->suspended_until !== null && $user->suspended_until->isFuture()    => 'suspended',
                    default                                                                   => 'active',
                };
                $statusLabel = match ($status) {
                    'banned'    => 'Banni',
                    'suspended' => 'Suspendu',
                    default     => 'Actif',
                };
                $roleLabel = match ($role) {
                    'super-admin' => 'Super Admin',
                    'admin'       => 'Admin',
                    'moderator'   => 'Modérateur',
                    default       => 'Membre',
                };
                $roleClass = match ($role) {
                    'super-admin' => 'badge-super',
                    'admin'       => 'badge-admin',
                    'moderator'   => 'badge-moderator',
                    default       => 'badge-member',
                };
            @endphp
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->github_username ? '@' . $user->github_username : '—' }}</td>
                <td><span class="badge {{ $roleClass }}">{{ $roleLabel }}</span></td>
                <td><span class="badge badge-{{ $status }}">{{ $statusLabel }}</span></td>
                <td>{{ $user->last_login_at?->format('d/m/Y H:i') ?? 'Jamais' }}</td>
                <td>{{ $user->created_at->format('d/m/Y') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p class="footer">Laravel CI Platform · Export membres · {{ now()->format('d/m/Y') }}</p>

</body>
</html>
