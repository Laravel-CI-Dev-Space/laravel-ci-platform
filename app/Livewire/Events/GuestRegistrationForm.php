<?php

declare(strict_types=1);

namespace App\Livewire\Events;

use App\Livewire\Concerns\RateLimited;
use App\Models\Event;
use App\Models\GuestRegistration;
use App\Services\Events\GuestRegistrationService;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class GuestRegistrationForm extends Component
{
    use RateLimited, WithFileUploads;

    public int $eventId;

    public string $firstName = '';

    public string $lastName = '';

    public string $email = '';

    public string $whatsapp = '';

    /** @var TemporaryUploadedFile|null */
    public $photo = null;

    public string $promoCode = '';

    public bool $submitted = false;

    public bool $isWaitlisted = false;

    protected function rules(): array
    {
        return [
            'firstName' => ['required', 'string', 'max:100'],
            'lastName'  => ['required', 'string', 'max:100'],
            'email'     => ['required', 'email', 'max:255'],
            'whatsapp'  => ['nullable', 'string', 'max:30'],
            'photo'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'promoCode' => ['nullable', 'string', 'max:50'],
        ];
    }

    protected function messages(): array
    {
        return [
            'firstName.required' => 'Le prénom est obligatoire.',
            'lastName.required'  => 'Le nom de famille est obligatoire.',
            'email.required'     => "L'adresse email est obligatoire.",
            'email.email'        => "L'adresse email n'est pas valide.",
            'photo.image'        => 'Le fichier doit être une image.',
            'photo.max'          => 'La photo ne doit pas dépasser 2 Mo.',
        ];
    }

    /**
     * Submits the guest registration form.
     */
    public function submit(GuestRegistrationService $guestService): void
    {
        if ($this->tooManyAttempts('events.guest-register', 5)) {
            $this->addError('email', $this->rateLimitMessage('events.guest-register'));

            return;
        }

        $this->validate();

        $event = Event::find($this->eventId);

        if ($event === null || ! $event->guest_registration_enabled || ! $event->isUpcoming()) {
            $this->addError('email', "Cet événement n'accepte pas d'inscriptions extérieures.");

            return;
        }

        $alreadyRegistered = GuestRegistration::where('event_id', $event->id)
            ->where('email', $this->email)
            ->whereNotIn('status', ['cancelled'])
            ->exists();

        if ($alreadyRegistered) {
            $this->addError('email', 'Cette adresse email est déjà inscrite à cet événement.');

            return;
        }

        $photoPath = null;

        if ($this->photo !== null) {
            $photoPath = $this->savePhoto($event->id);
        }

        try {
            $registration = $guestService->register(
                event: $event,
                data: [
                    'first_name' => $this->firstName,
                    'last_name'  => $this->lastName,
                    'email'      => $this->email,
                    'whatsapp'   => $this->whatsapp ?: null,
                    'photo'      => $photoPath,
                ],
                promoCode: $this->promoCode ?: null,
            );

            $this->isWaitlisted = $registration->isWaitlisted();
            $this->submitted    = true;

        } catch (\Exception $e) {
            $this->addError('email', $e->getMessage());
        }
    }

    public function render(): View
    {
        return view('livewire.events.guest-registration-form');
    }

    /**
     * Saves the uploaded photo to public/assets/web/img/guests/{eventId}/.
     */
    private function savePhoto(int $eventId): string
    {
        $dir = public_path("assets/web/img/guests/{$eventId}");
        File::ensureDirectoryExists($dir);

        // Extension dérivée du MIME réel du fichier, jamais du nom fourni par le client.
        $filename = uniqid('guest_', true) . '.' . $this->photo->extension();
        $this->photo->move($dir, $filename);

        return "assets/web/img/guests/{$eventId}/{$filename}";
    }
}
