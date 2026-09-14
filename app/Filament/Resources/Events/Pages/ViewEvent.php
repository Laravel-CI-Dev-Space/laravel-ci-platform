<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events\Pages;

use App\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Events\Widgets\EventRegistrationStatsWidget;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\GuestRegistration;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Collection;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ViewEvent extends ViewRecord
{
    protected static string $resource = EventResource::class;

    // ── Actions d'en-tête ─────────────────────────────────────────────────

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Modifier')
                ->icon('heroicon-o-pencil'),

            Action::make('manage_media')
                ->label('Médias')
                ->icon('heroicon-o-photo')
                ->color('gray')
                ->url(fn (): string => EventResource::getUrl('media', ['record' => $this->record])),

            Action::make('export_excel')
                ->label('Export Excel')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->action(fn () => $this->exportExcel()),

            Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action(fn () => $this->exportPdf()),

            Action::make('write_report')
                ->label('Rédiger rapport post-événement')
                ->icon('heroicon-o-document-text')
                ->color('warning')
                ->visible(fn (): bool => $this->record->status->value === 'completed')
                ->url(fn (): string => EventResource::getUrl('report', ['record' => $this->record])),
        ];
    }

    // ── Widgets : stats inscriptions ──────────────────────────────────────

    protected function getHeaderWidgets(): array
    {
        return [EventRegistrationStatsWidget::class];
    }

    public function getWidgetData(): array
    {
        return ['record' => $this->record];
    }

    // ── Export Excel ──────────────────────────────────────────────────────

    public function exportExcel(): StreamedResponse
    {
        $rows = $this->buildRegistrationRows();
        $event = $this->record;

        return response()->streamDownload(function () use ($rows): void {
            (new FastExcel($rows))->export('php://output');
        }, 'inscriptions-' . $event->slug . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    // ── Export PDF ────────────────────────────────────────────────────────

    public function exportPdf(): StreamedResponse
    {
        $event = $this->record;
        $rows  = $this->buildRegistrationRows();
        $stats = $this->buildStats();

        $pdf = Pdf::loadView('admin.exports.event-registrations-pdf', compact('event', 'rows', 'stats'))
            ->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'inscriptions-' . $event->slug . '.pdf'
        );
    }

    // ── Données partagées ─────────────────────────────────────────────────

    /**
     * Liste unifiée membres + invités pour exports.
     *
     * @return Collection<int, array<string, mixed>>
     */
    private function buildRegistrationRows(): Collection
    {
        $event = $this->record;

        // Membres (event_registrations)
        $members = EventRegistration::where('event_id', $event->id)
            ->with('user')
            ->get()
            ->map(fn (EventRegistration $r): array => [
                'Type'          => 'Membre',
                'Prénom'        => $r->user?->name ?? '—',
                'Nom'           => '',
                'Email'         => $r->user?->email ?? '—',
                'WhatsApp'      => '—',
                'Statut'        => $r->status->label(),
                'Inscrit le'    => $r->registered_at?->format('d/m/Y H:i') ?? '—',
                'Ticket'        => $r->ticket_number ?? '—',
            ]);

        // Invités externes (guest_registrations)
        $guests = GuestRegistration::where('event_id', $event->id)
            ->get()
            ->map(fn (GuestRegistration $r): array => [
                'Type'          => 'Invité',
                'Prénom'        => $r->first_name,
                'Nom'           => $r->last_name,
                'Email'         => $r->email,
                'WhatsApp'      => $r->whatsapp ?? '—',
                'Statut'        => ucfirst($r->status),
                'Inscrit le'    => $r->registered_at?->format('d/m/Y H:i') ?? '—',
                'Ticket'        => $r->ticket_number ?? '—',
            ]);

        return $members->concat($guests)->sortBy('Prénom')->values();
    }

    private function buildStats(): array
    {
        $event = $this->record;

        return [
            'total_membres'  => EventRegistration::where('event_id', $event->id)->count(),
            'total_invites'  => GuestRegistration::where('event_id', $event->id)->count(),
            'confirmed'      => EventRegistration::where('event_id', $event->id)->where('status', 'confirmed')->count()
                + GuestRegistration::where('event_id', $event->id)->where('status', 'confirmed')->count(),
            'attended'       => EventRegistration::where('event_id', $event->id)->where('status', 'attended')->count(),
        ];
    }
}
