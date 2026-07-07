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
                'priority'     => 1,
                'is_active'    => filled(env('XAI_API_KEY')),
                'extra_config' => null,
            ]
        );

        $grok3 = AiModel::updateOrCreate(
            ['provider_id' => $grok->id, 'model_name' => 'grok-3'],
            [
                'display_name'       => 'Grok 3',
                'max_tokens'         => 8192,
                'cost_input_per_1k'  => 0.005,
                'cost_output_per_1k' => 0.015,
                'supports_tools'     => true,
                'supports_streaming' => true,
                'is_active'          => filled(env('XAI_API_KEY')),
                'is_default'         => true,
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
                'priority'     => 2,
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
                'supports_tools'     => false, // R1 ne supporte pas les function calls
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
                'priority'     => 3,
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

        // ── Assignment global par défaut → Grok 3 ────────────────────────────
        $adminId = User::role('admin')->value('id') ?? User::first()?->id;

        if ($adminId) {
            AiUserAssignment::updateOrCreate(
                ['user_id' => null, 'role' => null],
                ['model_id' => $grok3->id, 'assigned_by' => $adminId]
            );
        } else {
            $this->command->warn('Aucun utilisateur trouvé — assignment global ignoré.');
        }

        $this->command->info('AI providers seeded: Grok (xAI), DeepSeek, OpenAI — défaut global: Grok 3');
    }
}
