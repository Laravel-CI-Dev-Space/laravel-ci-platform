<?php

declare(strict_types=1);

namespace App\Services\Events;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\GuestRegistration;
use Illuminate\Support\Str;

class TicketService
{
    /**
     * Generates a unique ticket number for an event.
     * Format: {PREFIX}-{EVENT_ID}-{RANDOM6}  e.g. LCI-5-K3M9P2
     */
    public function generateNumber(Event $event): string
    {
        $prefix = strtoupper($event->ticket_prefix ?? 'LCI');

        do {
            $number = $prefix . '-' . $event->id . '-' . strtoupper(Str::random(6));
        } while (
            EventRegistration::where('ticket_number', $number)->exists()
            || GuestRegistration::where('ticket_number', $number)->exists()
        );

        return $number;
    }

    /**
     * Generates a unique QR token used to identify and verify a ticket.
     */
    public function generateQrToken(): string
    {
        do {
            $token = Str::random(64);
        } while (
            EventRegistration::where('ticket_qr_token', $token)->exists()
            || GuestRegistration::where('ticket_qr_token', $token)->exists()
        );

        return $token;
    }

    /**
     * Returns the QR code image URL for a given token using an external API.
     * No server-side image generation needed.
     */
    public function qrImageUrl(string $token, int $size = 200): string
    {
        $data = urlencode(route('events.ticket.verify', ['token' => $token]));

        return "https://api.qrserver.com/v1/create-qr-code/?size={$size}x{$size}&data={$data}";
    }
}
