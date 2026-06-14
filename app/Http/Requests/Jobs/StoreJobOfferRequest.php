<?php

declare(strict_types=1);

namespace App\Http\Requests\Jobs;

use App\Enums\UserPermission;
use Illuminate\Foundation\Http\FormRequest;

class StoreJobOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        // Autorisé : membre avec la permission appropriée OU compte entreprise
        return ($user !== null && $user->can(UserPermission::JobOfferCreate->value))
            || auth('company')->check();
    }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'min:5', 'max:200'],
            'description'    => ['required', 'string', 'min:100'],
            'contract_type'  => ['required', 'in:cdi,cdd,freelance,internship,apprenticeship'],
            'level'          => ['required', 'in:junior,intermediate,senior,lead,any'],
            'location'       => ['nullable', 'string', 'max:150'],
            'country'        => ['nullable', 'string', 'max:100'],
            'is_remote'      => ['boolean'],
            'is_hybrid'      => ['boolean'],
            'salary_min'     => ['nullable', 'integer', 'min:0'],
            'salary_max'     => ['nullable', 'integer', 'min:0', 'gte:salary_min'],
            'currency'       => ['nullable', 'string', 'max:10'],
            'salary_visible' => ['boolean'],
            'is_urgent'      => ['boolean'],
            'categories'     => ['required', 'array', 'min:1'],
            'categories.*'   => ['exists:job_offer_categories,id'],
            'skills'         => ['required', 'array', 'min:1', 'max:10'],
            'skills.*'       => ['exists:job_skills,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'         => "L'intitulé du poste est obligatoire.",
            'title.min'              => "L'intitulé doit contenir au moins :min caractères.",
            'description.required'   => 'La description est obligatoire.',
            'description.min'        => 'La description doit contenir au moins :min caractères.',
            'contract_type.required' => 'Le type de contrat est obligatoire.',
            'contract_type.in'       => 'Le type de contrat est invalide.',
            'level.required'         => 'Le niveau requis est obligatoire.',
            'level.in'               => 'Le niveau est invalide.',
            'salary_max.gte'         => 'Le salaire maximum doit être supérieur au salaire minimum.',
            'categories.required'    => 'Sélectionnez au moins une catégorie.',
            'categories.min'         => 'Sélectionnez au moins :min catégorie.',
            'skills.required'        => 'Sélectionnez au moins une compétence.',
            'skills.min'             => 'Sélectionnez au moins :min compétence.',
            'skills.max'             => 'Vous ne pouvez pas sélectionner plus de :max compétences.',
        ];
    }
}
