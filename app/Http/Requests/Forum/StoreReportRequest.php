<?php

declare(strict_types=1);

namespace App\Http\Requests\Forum;

use App\Enums\UserPermission;
use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null
            && $user->isActive()
            && $user->can(UserPermission::ForumReport->value);
    }

    public function rules(): array
    {
        return [
            'reason'      => ['required', 'in:spam,inappropriate,harassment,misinformation,copyright,other'],
            'description' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'reason.required' => 'La raison du signalement est obligatoire.',
            'reason.in'       => 'La raison sélectionnée est invalide.',
            'description.max' => 'La description ne peut pas dépasser :max caractères.',
        ];
    }
}
