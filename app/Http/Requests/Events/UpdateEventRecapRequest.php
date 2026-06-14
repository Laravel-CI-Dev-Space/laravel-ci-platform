<?php

declare(strict_types=1);

namespace App\Http\Requests\Events;

use App\Enums\UserPermission;
use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateEventRecapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(UserPermission::EventManage->value) ?? false;
    }

    public function rules(): array
    {
        return [
            'recap_summary'     => ['nullable', 'string', 'max:1000'],
            'recap_content'     => ['nullable', 'string'],
            'recap_video_url_1' => ['nullable', 'url', 'max:255'],
            'recap_video_url_2' => ['nullable', 'url', 'max:255'],
            'recap_video_url_3' => ['nullable', 'url', 'max:255'],
            'recap_document'    => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'photos'            => ['nullable', 'array', 'max:10'],
            'photos.*'          => ['image', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'recap_summary.max'     => 'Le résumé ne peut pas dépasser :max caractères.',
            'recap_video_url_1.url' => 'Le lien vidéo 1 doit être une URL valide.',
            'recap_video_url_2.url' => 'Le lien vidéo 2 doit être une URL valide.',
            'recap_video_url_3.url' => 'Le lien vidéo 3 doit être une URL valide.',
            'recap_document.mimes'  => 'Le document doit être au format PDF, DOC ou DOCX.',
            'recap_document.max'    => 'Le document ne doit pas dépasser 10 Mo.',
            'photos.max'            => 'Vous ne pouvez pas ajouter plus de :max photos.',
            'photos.*.image'        => 'Chaque fichier doit être une image.',
            'photos.*.max'          => 'Chaque photo ne doit pas dépasser 5 Mo.',
        ];
    }

    /**
     * Vérifie que le total de photos (existantes + nouvelles) ne dépasse pas 10.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            /** @var Event|null $event */
            $event = $this->route('event');

            if ($event === null) {
                return;
            }

            $existingCount = $event->photos()->count();
            $newCount      = count($this->file('photos') ?? []);

            if ($existingCount + $newCount > 10) {
                $v->errors()->add('photos', 'Le nombre total de photos ne peut pas dépasser 10.');
            }
        });
    }
}
