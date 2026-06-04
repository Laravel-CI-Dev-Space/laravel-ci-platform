<?php

declare(strict_types=1);

namespace App\Http\Requests\Jobs;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreJobApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && $user->isActive()
            && $user->hasAnyRole([
                UserRole::Member->value,
                UserRole::Admin->value,
                UserRole::SuperAdmin->value,
                UserRole::Moderator->value,
            ]);
    }

    public function rules(): array
    {
        return [
            'cover_letter'  => ['nullable', 'string', 'min:50', 'max:3000'],
            'cv'            => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
            'linkedin_url'  => ['nullable', 'url', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'cover_letter.min'  => 'La lettre de motivation doit contenir au moins :min caractères.',
            'cover_letter.max'  => 'La lettre de motivation ne peut pas dépasser :max caractères.',
            'cv.file'           => 'Le fichier CV est invalide.',
            'cv.mimes'          => 'Le CV doit être au format PDF, DOC ou DOCX.',
            'cv.max'            => 'Le CV ne peut pas dépasser 5 Mo.',
            'portfolio_url.url' => "L'URL du portfolio est invalide.",
            'linkedin_url.url'  => "L'URL LinkedIn est invalide.",
        ];
    }

    /** Au moins un CV ou une lettre de motivation doit être fourni. */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $hasCv     = $this->hasFile('cv');
            $hasLetter = filled($this->input('cover_letter'));

            if (! $hasCv && ! $hasLetter) {
                $v->errors()->add(
                    'cv',
                    'Veuillez fournir au moins un CV ou une lettre de motivation.'
                );
            }
        });
    }
}
