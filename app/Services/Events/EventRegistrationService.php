<?php

declare(strict_types=1);

namespace App\Services\Events;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use App\Services\Analytics\AnalyticsService;
use App\Services\NotificationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EventRegistrationService
{
    public function __construct(
        private readonly NotificationService $notificationService,
        private readonly TicketService $ticketService,
        private readonly AnalyticsService $analytics,
    ) {}

    /**
     * Inscrit un utilisateur à un événement.
     *
     * Si l'événement est complet et la liste d'attente est activée, le statut est "waitlisted".
     * Pour les événements payants, calcule le prix final après application du code promo.
     *
     * @throws \Exception si l'événement est complet et la liste d'attente est désactivée
     */
    public function register(User $user, Event $event, ?string $promoCode = null): EventRegistration
    {
        if ($event->isFull() && ! $event->waitlist_enabled) {
            throw new \Exception("L'événement est complet et n'a pas de liste d'attente.");
        }

        $registration = DB::transaction(function () use ($user, $event, $promoCode): EventRegistration {
            $isWaitlisted = $event->isFull() && $event->waitlist_enabled;

            $paymentData = $this->resolvePaymentData($event, $promoCode);

            $ticketNumber  = null;
            $ticketQrToken = null;

            if ($event->ticketing_enabled && ! $isWaitlisted) {
                $ticketNumber  = $this->ticketService->generateNumber($event);
                $ticketQrToken = $this->ticketService->generateQrToken();
            }

            $registration = EventRegistration::create([
                'event_id'         => $event->id,
                'user_id'          => $user->id,
                'status'           => $isWaitlisted ? 'waitlisted' : 'confirmed',
                'ical_token'       => Str::random(64),
                'registered_at'    => now(),
                'amount_paid'      => $paymentData['amount_paid'],
                'promo_code_used'  => $paymentData['promo_code_used'],
                'discount_applied' => $paymentData['discount_applied'],
                'payment_status'   => $paymentData['payment_status'],
                'ticket_number'    => $ticketNumber,
                'ticket_qr_token'  => $ticketQrToken,
            ]);

            if (! $isWaitlisted) {
                $event->increment('registrations_count');

                if ($paymentData['promo_code_used'] !== null) {
                    $event->increment('promo_uses_count');
                }

                $this->notificationService->sendEventConfirmation($user, $registration);
            }

            return $registration;
        });

        $this->analytics->trackEvent(
            type: 'event_registration',
            userId: $user->id,
            entityType: 'event',
            entityId: $event->id,
            metadata: [
                'event_title' => $event->title,
                'status'      => $registration->status,
                'is_paid'     => $event->is_paid,
            ],
        );

        return $registration;
    }

    /**
     * Annule l'inscription d'un utilisateur.
     *
     * Décrémente le compteur d'inscriptions et promeut le premier sur la liste d'attente.
     *
     * @throws \Exception si l'annulation est impossible
     */
    public function cancel(EventRegistration $registration, ?string $reason = null): void
    {
        if (! $registration->canCancel()) {
            throw new \Exception("L'annulation n'est plus possible.");
        }

        $eventId    = $registration->event_id;
        $eventTitle = $registration->event?->title;
        $userId     = $registration->user_id;

        DB::transaction(function () use ($registration, $reason): void {
            $wasConfirmed = $registration->isConfirmed();

            $registration->update([
                'status'              => 'cancelled',
                'cancelled_at'        => now(),
                'cancellation_reason' => $reason,
            ]);

            if ($wasConfirmed) {
                // Guard against UNSIGNED underflow if count was already 0
                $registration->event->newQuery()
                    ->where('id', $registration->event_id)
                    ->where('registrations_count', '>', 0)
                    ->decrement('registrations_count');

                $this->promoteFromWaitlist($registration->event);
            }
        });

        $this->analytics->trackEvent(
            type: 'event_cancellation',
            userId: $userId,
            entityType: 'event',
            entityId: $eventId,
            metadata: ['event_title' => $eventTitle],
        );
    }

    /**
     * Génère le contenu iCal (.ics) pour une inscription.
     */
    public function generateIcal(EventRegistration $registration): string
    {
        return $this->buildIcalContent($registration);
    }

    /**
     * Récupère toutes les inscriptions d'un utilisateur avec eager loading.
     */
    public function getUserRegistrations(User $user): Collection
    {
        return EventRegistration::where('user_id', $user->id)
            ->with(['event'])
            ->orderByDesc('registered_at')
            ->get();
    }

    /**
     * Valide un code promo et retourne les données de paiement calculées.
     *
     * @return array{amount_paid: float|null, promo_code_used: string|null, discount_applied: float|null, payment_status: string}
     */
    public function validatePromo(Event $event, string $code): ?array
    {
        $promo = $event->resolvePromo($code);

        if ($promo === null) {
            return null;
        }

        $base     = (float) $event->price;
        $discount = $promo['type'] === 'percent'
            ? round($base * ($promo['discount'] / 100), 2)
            : min($promo['discount'], $base);

        return [
            'discount'    => $discount,
            'type'        => $promo['type'],
            'final_price' => max(0.0, round($base - $discount, 2)),
        ];
    }

    /**
     * Promeut le premier inscrit en liste d'attente quand une place se libère.
     */
    private function promoteFromWaitlist(Event $event): void
    {
        $next = EventRegistration::where('event_id', $event->id)
            ->where('status', 'waitlisted')
            ->orderBy('registered_at')
            ->first();

        if ($next === null) {
            return;
        }

        $next->update(['status' => 'confirmed']);
        $event->increment('registrations_count');

        $this->notificationService->sendEventConfirmation($next->user, $next);
    }

    /**
     * Calcule les données de paiement pour une inscription.
     *
     * @return array{amount_paid: float|null, promo_code_used: string|null, discount_applied: float|null, payment_status: string}
     */
    private function resolvePaymentData(Event $event, ?string $promoCode): array
    {
        if (! $event->is_paid) {
            return [
                'amount_paid'      => null,
                'promo_code_used'  => null,
                'discount_applied' => null,
                'payment_status'   => 'free',
            ];
        }

        $base            = (float) $event->price;
        $discountApplied = null;
        $promoUsed       = null;
        $finalPrice      = $base;

        if ($promoCode !== null) {
            $promo = $event->resolvePromo($promoCode);

            if ($promo !== null) {
                $discountApplied = $promo['type'] === 'percent'
                    ? round($base * ($promo['discount'] / 100), 2)
                    : min((float) $promo['discount'], $base);

                $finalPrice = max(0.0, round($base - $discountApplied, 2));
                $promoUsed  = strtoupper($promoCode);
            }
        }

        return [
            'amount_paid'      => $finalPrice,
            'promo_code_used'  => $promoUsed,
            'discount_applied' => $discountApplied,
            'payment_status'   => 'pending',
        ];
    }

    /**
     * Construit le contenu d'un fichier iCal RFC 5545 pour une inscription.
     */
    private function buildIcalContent(EventRegistration $registration): string
    {
        $event   = $registration->event;
        $uid     = $registration->ical_token . '@laravelci.com';
        $dtStart = $event->starts_at->utc()->format('Ymd\THis\Z');
        $dtEnd   = $event->ends_at->utc()->format('Ymd\THis\Z');
        $dtStamp = now()->utc()->format('Ymd\THis\Z');

        $location = $event->location ?? $event->online_url ?? 'Online';
        $title    = addslashes($event->title);
        $desc     = addslashes(strip_tags($event->description ?? ''));

        return "BEGIN:VCALENDAR\r\n"
            . "VERSION:2.0\r\n"
            . "PRODID:-//Laravel CI//Events//FR\r\n"
            . "CALSCALE:GREGORIAN\r\n"
            . "METHOD:PUBLISH\r\n"
            . "BEGIN:VEVENT\r\n"
            . "UID:{$uid}\r\n"
            . "DTSTAMP:{$dtStamp}\r\n"
            . "DTSTART:{$dtStart}\r\n"
            . "DTEND:{$dtEnd}\r\n"
            . "SUMMARY:{$title}\r\n"
            . "DESCRIPTION:{$desc}\r\n"
            . "LOCATION:{$location}\r\n"
            . "STATUS:CONFIRMED\r\n"
            . "END:VEVENT\r\n"
            . "END:VCALENDAR\r\n";
    }
}
