<?php

declare(strict_types=1);

namespace App\AI\Providers;

use App\Models\Chat\AiModel;

/**
 * OpenRouter — passerelle unifiée vers 400+ modèles IA.
 * Entièrement compatible OpenAI. Le base_url est https://openrouter.ai/api/v1.
 * Les headers HTTP-Referer et X-Title sont optionnels (attribution uniquement).
 */
class OpenRouterProvider extends OpenAIProvider
{
    public function __construct(AiModel $model)
    {
        parent::__construct($model);

        // Reconstruire le client avec les headers spécifiques à OpenRouter
        $extra = $model->provider->extra_config ?? [];

        $this->client = \OpenAI::factory()
            ->withApiKey($model->provider->api_key)
            ->withBaseUri($this->resolveBaseUrl())
            ->withHttpHeader('HTTP-Referer', $extra['site_url'] ?? config('app.url', 'https://laravel.ci'))
            ->withHttpHeader('X-Title', $extra['site_name'] ?? config('app.name', 'Laravel CI'))
            ->make();
    }

    public function supports(string $feature): bool
    {
        return match ($feature) {
            'tools'     => $this->model->supports_tools,
            'streaming' => true,
            'vision'    => str_contains($this->model->model_name, 'vision')
                        || str_contains($this->model->model_name, 'gpt-4o')
                        || str_contains($this->model->model_name, 'claude-3'),
            default     => false,
        };
    }
}
