<?php

declare(strict_types=1);

namespace App\AI;

use App\Models\Chat\AiKnowledgeFile;
use App\Models\Chat\ChatSession;
use App\Models\User;
use Illuminate\Support\Carbon;

class SystemPromptBuilder
{
    // Limite par section knowledge pour ne pas dépasser les quotas TPM Groq free tier
    private const KNOWLEDGE_MAX_CHARS = 6000;

    public function build(User $user, string $context): string
    {
        $sections = [];

        // 1. Comportement de base (fichiers type "behavior" uploadés par l'admin)
        $behavior = AiKnowledgeFile::activeContentByType(AiKnowledgeFile::TYPE_BEHAVIOR);
        if ($behavior) {
            $sections[] = $this->cap($behavior);
        } else {
            $sections[] = $this->defaultBehavior();
        }

        // 2. Connaissance de la plateforme (fichiers type "platform")
        $platform = AiKnowledgeFile::activeContentByType(AiKnowledgeFile::TYPE_PLATFORM);
        if ($platform) {
            $sections[] = "## Connaissance de la plateforme\n\n" . $this->cap($platform);
        }

        // 3. Connaissance Laravel/PHP (fichiers type "laravel")
        $laravel = AiKnowledgeFile::activeContentByType(AiKnowledgeFile::TYPE_LARAVEL);
        if ($laravel) {
            $sections[] = "## Base de connaissance Laravel & PHP\n\n" . $this->cap($laravel);
        }

        // 4. Contexte utilisateur
        $sections[] = $this->buildUserContext($user, $context);

        // 5. Date actuelle (évite les hallucinations temporelles)
        $sections[] = "Date actuelle : " . Carbon::now()->translatedFormat('d F Y');

        return implode("\n\n---\n\n", array_filter($sections));
    }

    private function buildUserContext(User $user, string $context): string
    {
        $isAdmin = $user->hasAnyRole(['super-admin', 'admin']);
        $isMod   = $user->hasRole('moderator');
        $role    = $isAdmin ? 'Administrateur' : ($isMod ? 'Modérateur' : 'Membre');

        $base = <<<TEXT
## Contexte de l'utilisateur connecté

- Nom : {$user->name}
- Identifiant (user_id) : {$user->id}
- Rôle : {$role}
- GitHub : @{$user->github_username}
TEXT;

        if ($context === ChatSession::CONTEXT_DASHBOARD) {
            $base .= $this->buildDashboardContext($user, $isAdmin);
        }

        if ($isAdmin) {
            $base .= "\n\n**Tu as accès à toutes les données de la plateforme.** Tu peux répondre aux questions globales sur tous les utilisateurs, articles, questions, statistiques, etc.";
        } else {
            $base .= "\n\n**IMPORTANT :** Dans le contexte dashboard, réponds UNIQUEMENT aux questions concernant CET utilisateur (user_id = {$user->id}). Ne divulgue jamais les données d'autres membres.";
        }

        return $base;
    }

    private function buildDashboardContext(User $user, bool $isAdmin): string
    {
        if ($isAdmin) {
            return "\n- Contexte : Dashboard administrateur (accès global)";
        }

        // Résumé rapide de l'activité du membre (requêtes légères)
        $questionCount   = $user->questions()->count();
        $answerCount     = \DB::table('answers')->where('user_id', $user->id)->count();
        $articleCount    = $user->articles()->count();
        $applicationCount = $user->jobApplications()->count();

        return <<<TEXT

- Contexte : Dashboard personnel

Activité résumée de l'utilisateur :
- Questions posées : {$questionCount}
- Réponses données : {$answerCount}
- Articles soumis : {$articleCount}
- Candidatures envoyées : {$applicationCount}

En mode dashboard, tu peux interroger la base de données pour donner à cet utilisateur des détails précis sur son activité (ses questions, articles, candidatures, événements inscrits, etc.).
TEXT;
    }

    private function cap(string $content): string
    {
        if (mb_strlen($content) <= self::KNOWLEDGE_MAX_CHARS) {
            return $content;
        }

        return mb_substr($content, 0, self::KNOWLEDGE_MAX_CHARS)
            . "\n\n[contenu tronqué — limite système]";
    }

    private function defaultBehavior(): string
    {
        return <<<TEXT
## Comportement et limites

Tu es l'assistant IA de **Laravel CI** (Laravel Côte d'Ivoire), une plateforme communautaire pour les développeurs Laravel et PHP en Côte d'Ivoire.

**Tu réponds uniquement aux sujets suivants :**
- Questions techniques sur Laravel, PHP, Livewire, Filament, Eloquent, et l'écosystème Laravel
- Questions sur la plateforme Laravel CI (fonctionnement, contenu, événements, offres d'emploi)
- Questions sur la communauté Laravel CI

**Tu refuses poliment tout autre sujet** (politique, religion, sujets non techniques, autres langages non liés, etc.) en expliquant que tu es spécialisé Laravel CI.

**Ton style :**
- Précis, concis, et professionnel
- Tu utilises du code quand c'est utile
- Tu cites les sources (articles ou questions de la plateforme) quand tu peux les retrouver en base
- Tu réponds en français par défaut, en anglais si l'utilisateur écrit en anglais
TEXT;
    }
}
