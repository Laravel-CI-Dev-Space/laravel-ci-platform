<?php

declare(strict_types=1);

namespace App\AI\Providers;

use App\Models\Chat\AiModel;

/**
 * DeepSeek utilise une API compatible OpenAI.
 * Le base_url (https://api.deepseek.com) est stocké dans ai_providers.base_url.
 */
class DeepSeekProvider extends OpenAIProvider
{
    public function __construct(AiModel $model)
    {
        parent::__construct($model);
    }

    public function supports(string $feature): bool
    {
        return match ($feature) {
            'tools'     => ! str_contains($this->model->model_name, 'reasoner'), // R1 ne supporte pas les function calls
            'streaming' => true,
            'vision'    => str_contains($this->model->model_name, 'chat'), // deepseek-chat supporte la vision
            default     => false,
        };
    }
}
