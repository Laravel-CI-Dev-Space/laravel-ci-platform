<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Chat\AiModel;
use App\Models\Chat\AiProvider;
use Illuminate\Database\Seeder;

class AiProviderSeeder extends Seeder
{
    public function run(): void
    {
        // ── Groq (inference rapide, compatible OpenAI) ───────────────────────
        $groq = AiProvider::updateOrCreate(
            ['name' => 'groq'],
            [
                'display_name' => 'Groq',
                'base_url'     => 'https://api.groq.com/openai/v1',
                'api_key'      => env('GROQ_API_KEY'),
                'priority'     => 1,
                'is_active'    => filled(env('GROQ_API_KEY')),
                'extra_config' => null,
            ]
        );

        AiModel::updateOrCreate(
            ['provider_id' => $groq->id, 'model_name' => 'openai/gpt-oss-20b'],
            [
                'display_name'      => 'GPT OSS 20B (Groq)',
                'max_tokens'        => 4096,
                'cost_input_per_1k' => 0.0,
                'cost_output_per_1k'=> 0.0,
                'supports_tools'    => true,
                'supports_streaming'=> true,
                'is_active'         => true,
                'is_default'        => true,
            ]
        );

        AiModel::updateOrCreate(
            ['provider_id' => $groq->id, 'model_name' => 'llama-3.3-70b-versatile'],
            [
                'display_name'      => 'Llama 3.3 70B (Groq)',
                'max_tokens'        => 8192,
                'cost_input_per_1k' => 0.0,
                'cost_output_per_1k'=> 0.0,
                'supports_tools'    => true,
                'supports_streaming'=> true,
                'is_active'         => true,
                'is_default'        => false,
            ]
        );

        // ── Claude (Anthropic) ───────────────────────────────────────────────
        $claude = AiProvider::updateOrCreate(
            ['name' => 'claude'],
            [
                'display_name' => 'Claude (Anthropic)',
                'base_url'     => 'https://api.anthropic.com',
                'api_key'      => env('ANTHROPIC_API_KEY'),
                'priority'     => 2,
                'is_active'    => filled(env('ANTHROPIC_API_KEY')),
                'extra_config' => null,
            ]
        );

        AiModel::updateOrCreate(
            ['provider_id' => $claude->id, 'model_name' => 'claude-sonnet-4-6'],
            [
                'display_name'      => 'Claude Sonnet 4.6',
                'max_tokens'        => 8096,
                'cost_input_per_1k' => 0.003,
                'cost_output_per_1k'=> 0.015,
                'supports_tools'    => true,
                'supports_streaming'=> true,
                'is_active'         => filled(env('ANTHROPIC_API_KEY')),
                'is_default'        => false,
            ]
        );

        // ── OpenAI ───────────────────────────────────────────────────────────
        $openai = AiProvider::updateOrCreate(
            ['name' => 'openai'],
            [
                'display_name' => 'OpenAI',
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
                'display_name'      => 'GPT-4o',
                'max_tokens'        => 4096,
                'cost_input_per_1k' => 0.005,
                'cost_output_per_1k'=> 0.015,
                'supports_tools'    => true,
                'supports_streaming'=> true,
                'is_active'         => filled(env('OPENAI_API_KEY')),
                'is_default'        => false,
            ]
        );

        // ── xAI Grok ─────────────────────────────────────────────────────────
        $xai = AiProvider::updateOrCreate(
            ['name' => 'grok'],
            [
                'display_name' => 'Grok (xAI)',
                'base_url'     => 'https://api.x.ai/v1',
                'api_key'      => env('XAI_API_KEY'),
                'priority'     => 4,
                'is_active'    => filled(env('XAI_API_KEY')),
                'extra_config' => null,
            ]
        );

        AiModel::updateOrCreate(
            ['provider_id' => $xai->id, 'model_name' => 'grok-3'],
            [
                'display_name'      => 'Grok 3',
                'max_tokens'        => 8192,
                'cost_input_per_1k' => 0.005,
                'cost_output_per_1k'=> 0.015,
                'supports_tools'    => true,
                'supports_streaming'=> true,
                'is_active'         => filled(env('XAI_API_KEY')),
                'is_default'        => false,
            ]
        );

        $this->command->info('AI providers seeded: Groq (actif), Claude, OpenAI, xAI Grok (inactifs jusqu\'à clé API)');
    }
}
