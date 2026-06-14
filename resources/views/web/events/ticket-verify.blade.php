@extends('layouts.web')
@section('title', 'Vérification de ticket — Laravel CI')

@section('content')
@php
    $memberReg = \App\Models\EventRegistration::where('ticket_qr_token', $token)
        ->with('event', 'user')->first();
    $guestReg  = \App\Models\GuestRegistration::where('ticket_qr_token', $token)
        ->with('event')->first();
    $registration = $memberReg ?? $guestReg;
    $statusValue = $registration?->status instanceof \BackedEnum ? $registration->status->value : $registration?->status;
    $isValid = $registration && in_array($statusValue, ['confirmed', 'attended'], true);
    $name = $memberReg ? $memberReg->user->name : ($guestReg ? $guestReg->fullName() : '—');
@endphp

<section class="section-padded">
    <div class="container" style="max-width:480px">
        <div class="text-center mb-5">
            <h1 class="section-title" style="font-size:1.6rem">Vérification de ticket</h1>
        </div>

        @if ($registration === null)
            <div class="p-5 text-center rounded-2xl"
                 style="border:2px solid #fecaca;background:#fef2f2">
                <i class="fa-solid fa-circle-xmark" style="font-size:3rem;color:#dc2626"></i>
                <h2 class="mt-3 mb-1" style="color:#991b1b">Ticket invalide</h2>
                <p class="text-muted-2">Ce code QR ne correspond à aucun ticket enregistré.</p>
            </div>
        @elseif (! $isValid)
            <div class="p-5 text-center rounded-2xl"
                 style="border:2px solid #fde68a;background:#fffbeb">
                <i class="fa-solid fa-circle-exclamation" style="font-size:3rem;color:#d97706"></i>
                <h2 class="mt-3 mb-1" style="color:#92400e">Ticket
                    {{ $statusValue === 'cancelled' ? 'annulé' : 'en attente' }}
                </h2>
                <p class="text-muted-2">Ce ticket ne donne pas accès à l'événement.</p>
            </div>
        @else
            <div class="p-5 rounded-2xl"
                 style="border:2px solid #bbf7d0;background:#f0fdf4">
                <div class="text-center mb-4">
                    <i class="fa-solid fa-circle-check" style="font-size:3rem;color:#16a34a"></i>
                    <h2 class="mt-3 mb-0" style="color:#166534">Ticket valide ✓</h2>
                </div>
                <table class="w-100" style="border-collapse:collapse">
                    <tr style="border-bottom:1px solid #d1fae5">
                        <td class="py-2" style="color:#64748b;font-size:.9rem;width:40%">Participant</td>
                        <td class="py-2 fw-semibold">{{ $name }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid #d1fae5">
                        <td class="py-2" style="color:#64748b;font-size:.9rem">Événement</td>
                        <td class="py-2 fw-semibold">{{ $registration->event->title }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid #d1fae5">
                        <td class="py-2" style="color:#64748b;font-size:.9rem">Date</td>
                        <td class="py-2">{{ $registration->event->starts_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr style="border-bottom:1px solid #d1fae5">
                        <td class="py-2" style="color:#64748b;font-size:.9rem">Numéro</td>
                        <td class="py-2" style="font-family:monospace;font-weight:700;letter-spacing:.05em">
                            {{ $registration->ticket_number }}
                        </td>
                    </tr>
                    <tr>
                        <td class="py-2" style="color:#64748b;font-size:.9rem">Type</td>
                        <td class="py-2">{{ $memberReg ? 'Membre' : 'Invité' }}</td>
                    </tr>
                </table>
            </div>
        @endif

        <div class="text-center mt-4">
            <a href="{{ route('events.index') }}" class="btn btn-ghost btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Retour aux événements
            </a>
        </div>
    </div>
</section>
@endsection
