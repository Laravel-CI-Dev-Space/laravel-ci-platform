<?php

declare(strict_types=1);

namespace App\Http\Requests\Forum;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->hasRole('member') && $user->isActive();
    }

    public function rules(): array
    {
        return [
            'title'  => ['required', 'string', 'min:10', 'max:300'],
            'body'   => ['required', 'string', 'min:30'],
            'tags'   => ['required', 'array', 'min:1', 'max:5'],
            'tags.*' => ['exists:tags,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est obligatoire.',
            'title.min'      => 'Le titre doit contenir au moins :min caractères.',
            'title.max'      => 'Le titre ne peut pas dépasser :max caractères.',
            'body.required'  => 'Le corps de la question est obligatoire.',
            'body.min'       => 'La question doit contenir au moins :min caractères.',
            'tags.required'  => 'Veuillez sélectionner au moins un tag.',
            'tags.min'       => 'Veuillez sélectionner au moins :min tag.',
            'tags.max'       => 'Vous ne pouvez pas sélectionner plus de :max tags.',
            'tags.*.exists'  => 'Un des tags sélectionnés est invalide.',
        ];
    }
}
