<?php

declare(strict_types=1);

namespace App\Http\Requests\Forum;

use App\Enums\UserPermission;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && $user->isActive()
            && $user->can(UserPermission::ForumCommentCreate->value);
    }

    public function rules(): array
    {
        return [
            'body'      => ['required', 'string', 'min:5', 'max:500'],
            'parent_id' => ['nullable', 'exists:comments,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required'    => 'Le commentaire est obligatoire.',
            'body.min'         => 'Le commentaire doit contenir au moins :min caractères.',
            'body.max'         => 'Le commentaire ne peut pas dépasser :max caractères.',
            'parent_id.exists' => 'Le commentaire parent est invalide.',
        ];
    }
}
