<?php

declare(strict_types=1);

namespace App\Http\Requests\Forum;

use App\Enums\UserPermission;
use Illuminate\Foundation\Http\FormRequest;

class StoreAnswerRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && $user->isActive()
            && $user->can(UserPermission::ForumAnswerCreate->value);
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'min:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'La réponse est obligatoire.',
            'body.min'      => 'La réponse doit contenir au moins :min caractères.',
        ];
    }
}
