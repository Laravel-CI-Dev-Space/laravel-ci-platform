<?php

declare(strict_types=1);

namespace App\Http\Requests\Events;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\GuestRegistration;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreGuestRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $event = $this->route('event');

        return $event instanceof Event && $event->guest_registration_enabled;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', 'max:255'],
            'whatsapp'   => ['nullable', 'string', 'max:30'],
            'photo'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'promo_code' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Le prénom est obligatoire.',
            'last_name.required'  => 'Le nom de famille est obligatoire.',
            'email.required'      => "L'adresse email est obligatoire.",
            'email.email'         => "L'adresse email n'est pas valide.",
            'photo.image'         => 'Le fichier doit être une image.',
            'photo.max'           => 'La photo ne doit pas dépasser 2 Mo.',
        ];
    }

    /**
     * Business validations: event state + duplicate check.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            /** @var Event|null $event */
            $event = $this->route('event');

            if ($event === null) {
                $v->errors()->add('event', "L'événement est introuvable.");

                return;
            }

            if ($event->status !== EventStatus::Published) {
                $v->errors()->add('event', "Cet événement n'est pas ouvert aux inscriptions.");

                return;
            }

            if ($event->isPast()) {
                $v->errors()->add('event', 'Cet événement est déjà terminé.');

                return;
            }

            $alreadyRegistered = GuestRegistration::where('event_id', $event->id)
                ->where('email', $this->input('email'))
                ->whereNotIn('status', ['cancelled'])
                ->exists();

            if ($alreadyRegistered) {
                $v->errors()->add('email', 'Cette adresse email est déjà inscrite à cet événement.');

                return;
            }

            if ($event->isFull() && ! $event->waitlist_enabled) {
                $v->errors()->add('event', "Cet événement est complet et n'a pas de liste d'attente.");
            }
        });
    }
}
