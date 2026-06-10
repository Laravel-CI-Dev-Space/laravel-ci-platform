<?php

namespace App\Http\Requests\Jobs;

use App\Enums\Jobs\JobOfferType;
use App\Models\JobOffer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SubmitJobOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', JobOffer::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'company_name'        => ['required', 'string', 'max:255'],
            'company_description' => ['nullable', 'string', 'max:2000'],
            'title'               => ['required', 'string', 'max:255'],
            'description'         => ['required', 'string', 'min:50'],
            'location'            => ['required', 'string', 'max:255'],
            'type'                => ['required', Rule::enum(JobOfferType::class)],
        ];
    }
}
