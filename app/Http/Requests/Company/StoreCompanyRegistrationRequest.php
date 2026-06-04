<?php

declare(strict_types=1);

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'      => ['required', 'string', 'max:100'],
            'last_name'       => ['required', 'string', 'max:100'],
            'company_name'    => ['required', 'string', 'max:200'],
            'email'           => ['required', 'email', 'max:255', 'unique:company_registration_requests,email'],
            'phone'           => ['nullable', 'string', 'max:30'],
            'position'        => ['required', 'string', 'max:100'],
            'city'            => ['nullable', 'string', 'max:100'],
            'country'         => ['required', 'string', 'max:100'],
            'business_domain' => ['required', 'string', 'max:150'],
            'website'         => ['nullable', 'url', 'max:255'],
            'motivation'      => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required'      => 'Le prénom est obligatoire.',
            'first_name.max'           => 'Le prénom ne peut pas dépasser :max caractères.',
            'last_name.required'       => 'Le nom est obligatoire.',
            'last_name.max'            => 'Le nom ne peut pas dépasser :max caractères.',
            'company_name.required'    => "Le nom de l'entreprise est obligatoire.",
            'company_name.max'         => "Le nom de l'entreprise ne peut pas dépasser :max caractères.",
            'email.required'           => "L'adresse email est obligatoire.",
            'email.email'              => "L'adresse email est invalide.",
            'email.unique'             => 'Cette adresse email a déjà soumis une demande.',
            'position.required'        => 'Le poste occupé est obligatoire.',
            'country.required'         => 'Le pays est obligatoire.',
            'business_domain.required' => "Le domaine d'activité est obligatoire.",
            'website.url'              => "L'URL du site web est invalide.",
            'motivation.max'           => 'Le message de présentation ne peut pas dépasser :max caractères.',
        ];
    }
}
