<?php

declare(strict_types=1);

namespace App\Http\Requests\Events;

use App\Enums\UserPermission;
use App\Models\EventRegistration;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CancelEventRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var EventRegistration|null $registration */
        $registration = $this->route('registration');

        return $registration !== null
            && $this->user()?->id === $registration->user_id
            && $this->user()?->can(UserPermission::EventCancel->value);
    }

    public function rules(): array
    {
        return [
            'reason' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'reason.max' => "La raison d'annulation ne peut pas dépasser :max caractères.",
        ];
    }

    /**
     * Vérifie que l'annulation est encore possible (avant J-2).
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            /** @var EventRegistration|null $registration */
            $registration = $this->route('registration');

            if ($registration === null) {
                $v->errors()->add('registration', "L'inscription est introuvable.");

                return;
            }

            if (! $registration->canCancel()) {
                $v->errors()->add(
                    'registration',
                    "L'annulation n'est plus possible. Vous devez annuler au moins 2 jours avant l'événement."
                );
            }
        });
    }
}
