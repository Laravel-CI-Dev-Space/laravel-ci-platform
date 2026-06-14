<?php

declare(strict_types=1);

namespace App\Http\Requests\Blog;

use App\Enums\UserPermission;
use Illuminate\Foundation\Http\FormRequest;

class StoreResourceRequest extends FormRequest
{
    /**
     * Vérifie que l'utilisateur est un membre actif disposant de la permission de téléverser une ressource.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && $user->isActive()
            && $user->can(UserPermission::BlogResourceUpload->value);
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'min:5', 'max:200'],
            'description' => ['nullable', 'string', 'max:1000'],
            'type'        => ['required', 'in:boilerplate,cheatsheet,guide,pdf,other'],
            'file'        => ['required', 'file', 'mimes:pdf,doc,docx,zip,txt,md', 'max:10240'],
            'is_public'   => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'  => 'Le titre est obligatoire.',
            'title.min'       => 'Le titre doit contenir au moins :min caractères.',
            'title.max'       => 'Le titre ne peut pas dépasser :max caractères.',
            'description.max' => 'La description ne peut pas dépasser :max caractères.',
            'type.required'   => 'Le type de ressource est obligatoire.',
            'type.in'         => 'Le type doit être : boilerplate, cheatsheet, guide, pdf ou other.',
            'file.required'   => 'Le fichier est obligatoire.',
            'file.file'       => 'Le fichier téléversé est invalide.',
            'file.mimes'      => 'Le fichier doit être de type : pdf, doc, docx, zip, txt ou md.',
            'file.max'        => 'Le fichier ne peut pas dépasser 10 Mo.',
        ];
    }
}
