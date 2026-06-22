<?php

declare(strict_types=1);

namespace App\AI\Providers;

use App\Models\Chat\AiModel;

/**
 * Grok (xAI) utilise une API compatible OpenAI.
 * On hérite d'OpenAIProvider et on surcharge uniquement le base_url.
 */
class GrokProvider extends OpenAIProvider
{
    public function __construct(AiModel $model)
    {
        parent::__construct($model);
        // Le base_url est configuré dans ai_providers.base_url = https://api.x.ai/v1
    }

    public function supports(string $feature): bool
    {
        return match ($feature) {
            'tools', 'streaming' => true,
            'vision'             => str_contains($this->model->model_name, 'vision'),
            default              => false,
        };
    }
}
