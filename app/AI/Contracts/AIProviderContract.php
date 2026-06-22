<?php

declare(strict_types=1);

namespace App\AI\Contracts;

use App\AI\DTOs\ChatPayload;
use App\AI\DTOs\ChatResponse;
use Generator;

interface AIProviderContract
{
    /**
     * Envoie une conversation et retourne la réponse complète.
     */
    public function chat(ChatPayload $payload): ChatResponse;

    /**
     * Envoie une conversation en streaming.
     * Yield string chunks pendant la génération, puis yield ChatResponse en dernier.
     */
    public function stream(ChatPayload $payload): Generator;

    /**
     * Convertit les messages (format universel DB) en format natif du provider.
     */
    public function normalizeMessages(array $messages): array;

    /**
     * Convertit les définitions de tools en format natif du provider.
     */
    public function normalizeTools(array $tools): array;

    /**
     * Vérifie si le provider supporte une fonctionnalité.
     * Fonctionnalités : 'tools', 'streaming', 'vision'
     */
    public function supports(string $feature): bool;

    /**
     * Retourne le nom du modèle utilisé (ex: claude-sonnet-4-6).
     */
    public function modelName(): string;

    /**
     * Vérifie que la clé API est configurée et valide (format).
     */
    public function isConfigured(): bool;
}
