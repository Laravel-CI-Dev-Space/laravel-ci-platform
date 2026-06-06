<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AllEventRegistration;
use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RegistrationExportController extends Controller
{
    /**
     * Exports all registrations as a CSV file (opens in Excel).
     */
    public function excel(Request $request): StreamedResponse
    {
        $query = $this->buildQuery($request);

        $filename = 'inscriptions-' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM so Excel opens it correctly
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Type', 'Nom', 'Email', 'WhatsApp', 'Événement',
                'Statut', 'Paiement', 'Montant', 'Ticket', 'Inscrit le',
            ], ';');

            $query->chunk(500, function ($rows) use ($handle): void {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->isMember() ? 'Membre' : 'Invité',
                        $row->display_name,
                        $row->email,
                        $row->whatsapp      ?? '',
                        $row->event?->title ?? '',
                        match ($row->status) {
                            'confirmed'  => 'Confirmé',
                            'waitlisted' => 'En attente',
                            'cancelled'  => 'Annulé',
                            default      => $row->status,
                        },
                        match ($row->payment_status) {
                            'paid'    => 'Payé',
                            'pending' => 'En attente',
                            'free'    => 'Gratuit',
                            default   => $row->payment_status ?? '',
                        },
                        $row->amount_paid ? number_format((float) $row->amount_paid, 0, ',', ' ') . ' ' . ($row->event?->currency ?? 'XOF') : '',
                        $row->ticket_number                       ?? '',
                        $row->registered_at?->format('d/m/Y H:i') ?? '',
                    ], ';');
                }
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Exports all registrations as a PDF file.
     */
    public function pdf(Request $request): Response
    {
        $registrations = $this->buildQuery($request)->get();
        $event         = $request->filled('event_id')
            ? Event::find($request->integer('event_id'))
            : null;

        $pdf = Pdf::loadView('admin.exports.registrations-pdf', compact('registrations', 'event'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('inscriptions-' . now()->format('Y-m-d') . '.pdf');
    }

    private function buildQuery(Request $request): Builder
    {
        return AllEventRegistration::query()
            ->with('event')
            ->when($request->filled('event_id'), fn ($q) => $q->where('event_id', $request->integer('event_id')))
            ->when($request->filled('type'), fn ($q) => $q->where('participant_type', $request->input('type')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->orderBy('registered_at', 'desc');
    }
}
