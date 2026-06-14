<?php

declare(strict_types=1);

namespace App\Http\Requests\Events;

use App\Enums\EventStatus;
use App\Enums\UserPermission;
use App\Models\Event;
use App\Models\EventRegistration;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreEventRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && $user->isActive()
            && $user->can(UserPermission::EventRegister->value);
    }

    public function rules(): array
    {
        return [
            'event_id' => ['required', 'exists:events,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'event_id.required' => "L'identifiant de l'événement est obligatoire.",
            'event_id.exists'   => "L'événement sélectionné est introuvable.",
        ];
    }

    /**
     * Validations métier sur l'événement et l'utilisateur.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            /** @var Event|null $event */
            $event = $this->route('event');

            if ($event === null) {
                $v->errors()->add('event_id', "L'événement est introuvable.");

                return;
            }

            if ($event->status !== EventStatus::Published) {
                $v->errors()->add('event_id', "Cet événement n'est pas ouvert aux inscriptions.");

                return;
            }

            if ($event->isPast()) {
                $v->errors()->add('event_id', 'Cet événement est déjà terminé.');

                return;
            }

            $alreadyRegistered = EventRegistration::where('event_id', $event->id)
                ->where('user_id', $this->user()->id)
                ->whereNotIn('status', ['cancelled'])
                ->exists();

            if ($alreadyRegistered) {
                $v->errors()->add('event_id', 'Vous êtes déjà inscrit à cet événement.');

                return;
            }

            if ($event->isFull() && ! $event->waitlist_enabled) {
                $v->errors()->add('event_id', "Cet événement est complet et n'a pas de liste d'attente.");
            }
        });
    }
}
