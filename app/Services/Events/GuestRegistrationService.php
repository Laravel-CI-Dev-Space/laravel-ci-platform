<?php

declare(strict_types=1);

namespace App\Services\Events;

use App\Models\Event;
use App\Models\GuestRegistration;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class GuestRegistrationService
{
    public function __construct(
        private readonly TicketService $ticketService,
        private readonly NotificationService $notificationService,
    ) {}

    /**
     * Registers a non-member guest for an event.
     *
     * Handles waitlisting, pricing, promo codes and ticket generation.
     *
     * @param  array{first_name: string, last_name: string, email: string, whatsapp: string|null, photo: string|null}  $data
     *
     * @throws \Exception if the event is full without a waitlist
     */
    public function register(Event $event, array $data, ?string $promoCode = null): GuestRegistration
    {
        if ($event->isFull() && ! $event->waitlist_enabled) {
            throw new \Exception("L'événement est complet et n'a pas de liste d'attente.");
        }

        return DB::transaction(function () use ($event, $data, $promoCode): GuestRegistration {
            $isWaitlisted = $event->isFull() && $event->waitlist_enabled;
            $paymentData  = $this->resolvePaymentData($event, $promoCode);

            $ticketNumber  = null;
            $ticketQrToken = null;

            if ($event->ticketing_enabled && ! $isWaitlisted) {
                $ticketNumber  = $this->ticketService->generateNumber($event);
                $ticketQrToken = $this->ticketService->generateQrToken();
            }

            $registration = GuestRegistration::create([
                'event_id'         => $event->id,
                'first_name'       => $data['first_name'],
                'last_name'        => $data['last_name'],
                'email'            => $data['email'],
                'whatsapp'         => $data['whatsapp'] ?? null,
                'photo'            => $data['photo']    ?? null,
                'status'           => $isWaitlisted ? 'waitlisted' : 'confirmed',
                'amount_paid'      => $paymentData['amount_paid'],
                'promo_code_used'  => $paymentData['promo_code_used'],
                'discount_applied' => $paymentData['discount_applied'],
                'payment_status'   => $paymentData['payment_status'],
                'ticket_number'    => $ticketNumber,
                'ticket_qr_token'  => $ticketQrToken,
                'registered_at'    => now(),
            ]);

            if (! $isWaitlisted) {
                $event->increment('registrations_count');

                if ($paymentData['promo_code_used'] !== null) {
                    $event->increment('promo_uses_count');
                }
            }

            return $registration;
        });
    }

    /**
     * Cancels a guest registration and promotes from waitlist if needed.
     *
     * @throws \Exception if the registration cannot be cancelled
     */
    public function cancel(GuestRegistration $registration, ?string $reason = null): void
    {
        if (! $registration->canCancel()) {
            throw new \Exception("L'annulation n'est plus possible.");
        }

        DB::transaction(function () use ($registration, $reason): void {
            $wasConfirmed = $registration->isConfirmed();

            $registration->update([
                'status'              => 'cancelled',
                'cancelled_at'        => now(),
                'cancellation_reason' => $reason,
            ]);

            if ($wasConfirmed) {
                $registration->event->decrement('registrations_count');
            }
        });
    }

    /**
     * Resolves payment data (price, discount, promo) for a guest registration.
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
}
