<?php

declare(strict_types=1);

namespace App\AI;

use App\AI\Contracts\AIProviderContract;
use App\AI\Providers\DeepSeekProvider;
use App\AI\Providers\GrokProvider;
use App\AI\Providers\OpenAIProvider;
use App\Models\Chat\AiModel;
use App\Models\Chat\AiUserAssignment;
use App\Models\User;
use RuntimeException;

class AIRouter
{
    /**
     * Résout le provider à utiliser pour un utilisateur donné.
     *
     * Priorité :
     *   1. Assignment individuel (user_id)
     *   2. Assignment par rôle (role)
     *   3. Assignment global par défaut (user_id null + role null)
     *   4. Modèle is_default = true
     */
    public function resolveForUser(User $user): AIProviderContract
    {
        $model = $this->resolveModelForUser($user);

        if (! $model) {
            throw new RuntimeException(
                "Aucun modèle AI actif configuré. Veuillez en activer un depuis le panneau d'administration."
            );
        }

        if (! $model->provider->is_active || ! $model->provider->hasKey()) {
            throw new RuntimeException(
                "Le provider «{$model->provider->display_name}» est inactif ou sans clé API."
            );
        }

        return $this->makeProvider($model);
    }

    /**
     * Résout le modèle sans lancer d'exception (retourne null si aucun).
     */
    public function resolveModelForUser(User $user): ?AiModel
    {
        // 1. Assignment individuel
        $assignment = AiUserAssignment::forUser($user->id)
            ->with(['model.provider'])
            ->first();

        if ($assignment?->model?->is_active) {
            return $assignment->model;
        }

        // 2. Assignment par rôle (premier rôle de l'user qui a un assignment)
        $userRoles = $user->getRoleNames();

        foreach ($userRoles as $role) {
            $assignment = AiUserAssignment::forRole($role)
                ->with(['model.provider'])
                ->first();

            if ($assignment?->model?->is_active) {
                return $assignment->model;
            }
        }

        // 3. Assignment global (user_id = null, role = null)
        $assignment = AiUserAssignment::globalDefault()
            ->with(['model.provider'])
            ->first();

        if ($assignment?->model?->is_active) {
            return $assignment->model;
        }

        // 4. Modèle par défaut global
        return AiModel::with('provider')
            ->where('is_active', true)
            ->where('is_default', true)
            ->first();
    }

    /**
     * Instancie le bon provider selon le nom du provider en base.
     */
    public function makeProvider(AiModel $model): AIProviderContract
    {
        return match ($model->provider->name) {
            'grok'      => new GrokProvider($model),
            'deepseek'  => new DeepSeekProvider($model),
            default     => new OpenAIProvider($model),
        };
    }
}
