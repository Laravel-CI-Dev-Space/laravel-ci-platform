<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MemberExportController extends Controller
{
    /**
     * Nombre maximum de lignes rendues dans le PDF (DomPDF charge tout en
     * mémoire pour générer le rendu : au-delà, l'export Excel/CSV doit être
     * utilisé).
     */
    private const MAX_PDF_ROWS = 1000;

    /**
     * Exports all members as a CSV file (opens in Excel).
     */
    public function excel(Request $request): StreamedResponse
    {
        $query    = $this->buildQuery($request);
        $filename = 'membres-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Nom', 'Email', 'GitHub', 'Rôle', 'Statut',
                'Dernière connexion', 'Inscrit le',
            ], ';');

            $query->chunk(500, function ($rows) use ($handle): void {
                foreach ($rows as $user) {
                    $role   = $user->roles->first()?->name ?? 'member';
                    $status = match (true) {
                        ! $user->is_active                                                    => 'Banni',
                        $user->suspended_until !== null && $user->suspended_until->isFuture() => 'Suspendu',
                        default                                                               => 'Actif',
                    };

                    fputcsv($handle, [
                        $user->name,
                        $user->email,
                        $user->github_username ? '@' . $user->github_username : '',
                        $role,
                        $status,
                        $user->last_login_at?->format('d/m/Y H:i') ?? 'Jamais',
                        $user->created_at->format('d/m/Y'),
                    ], ';');
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Exports all members as a PDF file.
     */
    public function pdf(Request $request): Response
    {
        $query = $this->buildQuery($request);

        $total     = (clone $query)->count();
        $members   = $query->limit(self::MAX_PDF_ROWS)->get();
        $truncated = $total > self::MAX_PDF_ROWS;

        $pdf = Pdf::loadView('admin.exports.members-pdf', compact('members', 'truncated', 'total'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('membres-' . now()->format('Y-m-d') . '.pdf');
    }

    private function buildQuery(Request $request): Builder
    {
        return User::query()
            ->with('roles')
            ->when($request->filled('role'), function (Builder $q) use ($request): void {
                $q->whereHas('roles', fn (Builder $r) => $r->where('name', $request->input('role')));
            })
            ->when($request->input('status') === 'banned', fn (Builder $q) => $q->where('is_active', false))
            ->when($request->input('status') === 'suspended', fn (Builder $q) => $q
                ->where('is_active', true)
                ->whereNotNull('suspended_until')
                ->where('suspended_until', '>', now()))
            ->when($request->input('status') === 'active', fn (Builder $q) => $q
                ->where('is_active', true)
                ->where(fn (Builder $q2) => $q2->whereNull('suspended_until')->orWhere('suspended_until', '<=', now())))
            ->orderBy('created_at', 'desc');
    }
}
