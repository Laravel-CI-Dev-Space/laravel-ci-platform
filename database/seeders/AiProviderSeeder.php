<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Chat\AiModel;
use App\Models\Chat\AiProvider;
use App\Models\Chat\AiUserAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;

class AiProviderSeeder extends Seeder
{
    public function run(): void
    {
        // ── Grok (xAI) ───────────────────────────────────────────────────────
        $grok = AiProvider::updateOrCreate(
            ['name' => 'grok'],
            [
                'display_name' => 'Grok (xAI)',
                'base_url'     => 'https://api.x.ai/v1',
                'api_key'      => env('XAI_API_KEY'),
                'priority'     => 2,
                'is_active'    => filled(env('XAI_API_KEY')),
                'extra_config' => null,
            ]
        );

        AiModel::updateOrCreate(
            ['provider_id' => $grok->id, 'model_name' => 'grok-3'],
            [
                'display_name'       => 'Grok 3',
                'max_tokens'         => 8192,
                'cost_input_per_1k'  => 0.005,
                'cost_output_per_1k' => 0.015,
                'supports_tools'     => true,
                'supports_streaming' => true,
                'is_active'          => filled(env('XAI_API_KEY')),
                'is_default'         => false,
            ]
        );

        AiModel::updateOrCreate(
            ['provider_id' => $grok->id, 'model_name' => 'grok-3-mini'],
            [
                'display_name'       => 'Grok 3 Mini',
                'max_tokens'         => 8192,
                'cost_input_per_1k'  => 0.0003,
                'cost_output_per_1k' => 0.0005,
                'supports_tools'     => true,
                'supports_streaming' => true,
                'is_active'          => filled(env('XAI_API_KEY')),
                'is_default'         => false,
            ]
        );

        // ── DeepSeek ─────────────────────────────────────────────────────────
        $deepseek = AiProvider::updateOrCreate(
            ['name' => 'deepseek'],
            [
                'display_name' => 'DeepSeek',
                'base_url'     => 'https://api.deepseek.com',
                'api_key'      => env('DEEPSEEK_API_KEY'),
                'priority'     => 3,
                'is_active'    => filled(env('DEEPSEEK_API_KEY')),
                'extra_config' => null,
            ]
        );

        AiModel::updateOrCreate(
            ['provider_id' => $deepseek->id, 'model_name' => 'deepseek-chat'],
            [
                'display_name'       => 'DeepSeek V3 (Chat)',
                'max_tokens'         => 8192,
                'cost_input_per_1k'  => 0.00014,
                'cost_output_per_1k' => 0.00028,
                'supports_tools'     => true,
                'supports_streaming' => true,
                'is_active'          => filled(env('DEEPSEEK_API_KEY')),
                'is_default'         => false,
            ]
        );

        AiModel::updateOrCreate(
            ['provider_id' => $deepseek->id, 'model_name' => 'deepseek-reasoner'],
            [
                'display_name'       => 'DeepSeek R1 (Reasoner)',
                'max_tokens'         => 8192,
                'cost_input_per_1k'  => 0.00055,
                'cost_output_per_1k' => 0.00219,
                'supports_tools'     => false,
                'supports_streaming' => true,
                'is_active'          => filled(env('DEEPSEEK_API_KEY')),
                'is_default'         => false,
            ]
        );

        // ── OpenAI (ChatGPT) ─────────────────────────────────────────────────
        $openai = AiProvider::updateOrCreate(
            ['name' => 'openai'],
            [
                'display_name' => 'OpenAI (ChatGPT)',
                'base_url'     => 'https://api.openai.com/v1',
                'api_key'      => env('OPENAI_API_KEY'),
                'priority'     => 4,
                'is_active'    => filled(env('OPENAI_API_KEY')),
                'extra_config' => null,
            ]
        );

        AiModel::updateOrCreate(
            ['provider_id' => $openai->id, 'model_name' => 'gpt-4o'],
            [
                'display_name'       => 'GPT-4o',
                'max_tokens'         => 4096,
                'cost_input_per_1k'  => 0.005,
                'cost_output_per_1k' => 0.015,
                'supports_tools'     => true,
                'supports_streaming' => true,
                'is_active'          => filled(env('OPENAI_API_KEY')),
                'is_default'         => false,
            ]
        );

        AiModel::updateOrCreate(
            ['provider_id' => $openai->id, 'model_name' => 'gpt-4o-mini'],
            [
                'display_name'       => 'GPT-4o Mini',
                'max_tokens'         => 4096,
                'cost_input_per_1k'  => 0.00015,
                'cost_output_per_1k' => 0.0006,
                'supports_tools'     => true,
                'supports_streaming' => true,
                'is_active'          => filled(env('OPENAI_API_KEY')),
                'is_default'         => false,
            ]
        );

        // ── OpenRouter (passerelle 400+ modèles) ─────────────────────────────
        $hasOrKey = filled(env('OPENROUTER_API_KEY'));

        $openrouter = AiProvider::updateOrCreate(
            ['name' => 'openrouter'],
            [
                'display_name' => 'OpenRouter',
                'base_url'     => 'https://openrouter.ai/api/v1',
                'api_key'      => env('OPENROUTER_API_KEY'),
                'priority'     => 1,
                'is_active'    => $hasOrKey,
                'extra_config' => [
                    'site_url'  => env('APP_URL', 'https://laravel.ci'),
                    'site_name' => env('APP_NAME', 'Laravel CI'),
                ],
            ]
        );

        // Modèles gratuits — disponibles sans solde
        $defaultModel = AiModel::updateOrCreate(
            ['provider_id' => $openrouter->id, 'model_name' => 'meta-llama/llama-3.3-70b-instruct:free'],
            [
                'display_name'       => 'Llama 3.3 70B (Gratuit)',
                'max_tokens'         => 8192,
                'cost_input_per_1k'  => 0.0,
                'cost_output_per_1k' => 0.0,
                'supports_tools'     => true,
                'supports_streaming' => true,
                'is_active'          => $hasOrKey,
                'is_default'         => true,
            ]
        );

        AiModel::updateOrCreate(
            ['provider_id' => $openrouter->id, 'model_name' => 'google/gemma-3-27b-it:free'],
            [
                'display_name'       => 'Gemma 3 27B (Gratuit)',
                'max_tokens'         => 8192,
                'cost_input_per_1k'  => 0.0,
                'cost_output_per_1k' => 0.0,
                'supports_tools'     => false,
                'supports_streaming' => true,
                'is_active'          => $hasOrKey,
                'is_default'         => false,
            ]
        );

        AiModel::updateOrCreate(
            ['provider_id' => $openrouter->id, 'model_name' => 'mistralai/mistral-7b-instruct:free'],
            [
                'display_name'       => 'Mistral 7B (Gratuit)',
                'max_tokens'         => 4096,
                'cost_input_per_1k'  => 0.0,
                'cost_output_per_1k' => 0.0,
                'supports_tools'     => false,
                'supports_streaming' => true,
                'is_active'          => $hasOrKey,
                'is_default'         => false,
            ]
        );

        // Modèles gratuits — raisonnement (reasoning tokens)
        AiModel::updateOrCreate(
            ['provider_id' => $openrouter->id, 'model_name' => 'tencent/hy3:free'],
            [
                'display_name'       => 'Hunyuan A13B Instruct (Gratuit)',
                'max_tokens'         => 8192,
                'cost_input_per_1k'  => 0.0,
                'cost_output_per_1k' => 0.0,
                'supports_tools'     => false,
                'supports_streaming' => true,
                'is_active'          => $hasOrKey,
                'is_default'         => false,
            ]
        );

        // Modèles payants — qualité supérieure
        AiModel::updateOrCreate(
            ['provider_id' => $openrouter->id, 'model_name' => 'deepseek/deepseek-chat-v3-0324'],
            [
                'display_name'       => 'DeepSeek V3 0324',
                'max_tokens'         => 8192,
                'cost_input_per_1k'  => 0.00028,
                'cost_output_per_1k' => 0.00088,
                'supports_tools'     => true,
                'supports_streaming' => true,
                'is_active'          => $hasOrKey,
                'is_default'         => false,
            ]
        );

        AiModel::updateOrCreate(
            ['provider_id' => $openrouter->id, 'model_name' => 'openai/gpt-4o'],
            [
                'display_name'       => 'GPT-4o via OpenRouter',
                'max_tokens'         => 4096,
                'cost_input_per_1k'  => 0.0055,
                'cost_output_per_1k' => 0.0165,
                'supports_tools'     => true,
                'supports_streaming' => true,
                'is_active'          => $hasOrKey,
                'is_default'         => false,
            ]
        );

        AiModel::updateOrCreate(
            ['provider_id' => $openrouter->id, 'model_name' => 'anthropic/claude-3.5-haiku'],
            [
                'display_name'       => 'Claude 3.5 Haiku via OpenRouter',
                'max_tokens'         => 4096,
                'cost_input_per_1k'  => 0.001,
                'cost_output_per_1k' => 0.005,
                'supports_tools'     => true,
                'supports_streaming' => true,
                'is_active'          => $hasOrKey,
                'is_default'         => false,
            ]
        );

        // ── Assignment global → OpenRouter DeepSeek V3 (gratuit) ─────────────
        $adminId = User::role('admin')->value('id') ?? User::first()?->id;

        if ($adminId) {
            AiUserAssignment::updateOrCreate(
                ['user_id' => null, 'role' => null],
                ['model_id' => $defaultModel->id, 'assigned_by' => $adminId]
            );
        } else {
            $this->command->warn('Aucun utilisateur trouvé — assignment global ignoré.');
        }

        $this->command->info('AI providers seeded: OpenRouter (défaut), Grok, DeepSeek, OpenAI');
        $this->command->info('Défaut global → DeepSeek V3 0324 via OpenRouter (gratuit, tools activés)');
    }
}
