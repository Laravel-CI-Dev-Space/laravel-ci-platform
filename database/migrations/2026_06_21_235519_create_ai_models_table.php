<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ai_models', function (Blueprint $table) {
            $table->id();
            $table->foreignId('provider_id')->constrained('ai_providers')->cascadeOnDelete();
            $table->string('model_name');                   // ex: claude-sonnet-4-6, gpt-4o
            $table->string('display_name');
            $table->unsignedInteger('max_tokens')->default(4096);
            $table->decimal('cost_input_per_1k', 10, 6)->default(0);
            $table->decimal('cost_output_per_1k', 10, 6)->default(0);
            $table->boolean('supports_tools')->default(true);
            $table->boolean('supports_streaming')->default(true);
            $table->boolean('is_active')->default(false);
            $table->boolean('is_default')->default(false);  // modèle par défaut global
            $table->timestamps();

            $table->unique(['provider_id', 'model_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_models');
    }
};
