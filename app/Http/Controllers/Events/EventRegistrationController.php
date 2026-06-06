<?php

declare(strict_types=1);

namespace App\Http\Controllers\Events;

use App\Http\Controllers\Controller;
use App\Http\Requests\Events\CancelEventRegistrationRequest;
use App\Http\Requests\Events\StoreEventRegistrationRequest;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Services\Events\EventRegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class EventRegistrationController extends Controller
{
    public function __construct(private readonly EventRegistrationService $registrationService) {}

    /**
     * Inscrit l'utilisateur connecté à un événement.
     */
    public function store(StoreEventRegistrationRequest $request, Event $event): RedirectResponse
    {
        $registration = $this->registrationService->register($request->user(), $event);

        $message = $registration->isWaitlisted()
            ? "Vous avez été ajouté à la liste d'attente pour cet événement."
            : 'Votre inscription a bien été enregistrée. À bientôt !';

        return redirect()
            ->route('events.show', $event->slug)
            ->with('success', $message);
    }

    /**
     * Annule l'inscription d'un utilisateur.
     */
    public function destroy(CancelEventRegistrationRequest $request, EventRegistration $registration): RedirectResponse
    {
        $this->registrationService->cancel(
            registration: $registration,
            reason: $request->input('reason'),
        );

        return redirect()
            ->back()
            ->with('success', 'Votre inscription a été annulée.');
    }

    /**
     * Génère et retourne le fichier iCal (.ics) pour une inscription.
     */
    public function downloadIcal(EventRegistration $registration): Response
    {
        abort_unless(
            auth()->id() === $registration->user_id,
            403,
            'Accès non autorisé.'
        );

        $content  = $this->registrationService->generateIcal($registration);
        $filename = "laravel-ci-event-{$registration->ical_token}.ics";

        return response($content, 200, [
            'Content-Type'        => 'text/calendar; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
