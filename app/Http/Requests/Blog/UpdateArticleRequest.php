<?php

declare(strict_types=1);

namespace App\Http\Requests\Blog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateArticleRequest extends FormRequest
{
    /**
     * L'auteur de l'article ou un admin/modérateur peuvent modifier.
     */
    public function authorize(): bool
    {
        $user    = $this->user();
        $article = $this->route('article');

        if ($user === null || $article === null) {
            return false;
        }

        return $article->isOwnedBy($user)
            || $user->hasAnyRole(['admin', 'moderator', 'super-admin']);
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'min:10', 'max:300'],
            'excerpt'     => ['nullable', 'string', 'max:500'],
            'body'        => ['required', 'string', 'min:100'],
            'level'       => ['required', 'in:beginner,intermediate,advanced'],
            'tags'        => ['required', 'array', 'min:1', 'max:5'],
            'tags.*'      => ['integer', Rule::exists('tags', 'id')->whereIn('scope', ['blog', 'both'])],
            'cover_image' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'    => 'Le titre est obligatoire.',
            'title.min'         => 'Le titre doit contenir au moins :min caractères.',
            'title.max'         => 'Le titre ne peut pas dépasser :max caractères.',
            'excerpt.max'       => "L'extrait ne peut pas dépasser :max caractères.",
            'body.required'     => "Le corps de l'article est obligatoire.",
            'body.min'          => "L'article doit contenir au moins :min caractères.",
            'level.required'    => 'Le niveau est obligatoire.',
            'level.in'          => 'Le niveau doit être : débutant, intermédiaire ou avancé.',
            'tags.required'     => 'Veuillez sélectionner au moins un tag.',
            'tags.min'          => 'Veuillez sélectionner au moins :min tag.',
            'tags.max'          => 'Vous ne pouvez pas sélectionner plus de :max tags.',
            'tags.*.exists'     => 'Un des tags sélectionnés est invalide ou non autorisé pour le blog.',
            'cover_image.image' => "L'image de couverture doit être une image valide.",
            'cover_image.max'   => "L'image de couverture ne peut pas dépasser 2 Mo.",
        ];
    }
}
