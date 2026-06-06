<?php

declare(strict_types=1);

namespace App\Http\Controllers\Events;

use App\Http\Controllers\Controller;
use App\Http\Requests\Events\StoreGuestRegistrationRequest;
use App\Models\Event;
use App\Services\Events\GuestRegistrationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class GuestRegistrationController extends Controller
{
    public function __construct(private readonly GuestRegistrationService $guestService) {}

    /**
     * Stores a guest (non-member) event registration.
     */
    public function store(StoreGuestRegistrationRequest $request, Event $event): RedirectResponse
    {
        $photoPath = null;

        if ($request->hasFile('photo')) {
            $photoPath = $this->savePhoto($request->file('photo'), $event->id);
        }

        $registration = $this->guestService->register(
            event: $event,
            data: [
                'first_name' => $request->input('first_name'),
                'last_name'  => $request->input('last_name'),
                'email'      => $request->input('email'),
                'whatsapp'   => $request->input('whatsapp'),
                'photo'      => $photoPath,
            ],
            promoCode: $request->input('promo_code'),
        );

        $message = $registration->isWaitlisted()
            ? "Vous avez été ajouté(e) à la liste d'attente. Nous vous contacterons si une place se libère."
            : 'Votre inscription a bien été enregistrée. À bientôt !';

        return redirect()
            ->route('events.show', $event->slug)
            ->with('success', $message);
    }

    /**
     * Saves the uploaded photo to public/assets/web/img/guests/{eventId}/.
     */
    private function savePhoto(UploadedFile $file, int $eventId): string
    {
        $dir = public_path("assets/web/img/guests/{$eventId}");
        File::ensureDirectoryExists($dir);

        $filename = uniqid('guest_', true) . '.' . $file->getClientOriginalExtension();
        $file->move($dir, $filename);

        return "assets/web/img/guests/{$eventId}/{$filename}";
    }
}
