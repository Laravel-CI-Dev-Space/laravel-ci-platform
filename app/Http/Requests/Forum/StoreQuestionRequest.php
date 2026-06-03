<?php

declare(strict_types=1);

namespace App\Http\Requests\Forum;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'title'   => ['required', 'string', 'min:10', 'max:255'],
            'body' => ['required', 'string', 'min:30'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est obligatoire.',
            'title.min'      => 'Le titre doit contenir au moins :min caractères.',
            'title.max'      => 'Le titre ne peut pas dépasser :max caractères.',
            'body.required'  => 'Le contenu est obligatoire.',
            'body.min'       => 'Le contenu doit contenir au moins :min caractères.',
        ];
    }
}
