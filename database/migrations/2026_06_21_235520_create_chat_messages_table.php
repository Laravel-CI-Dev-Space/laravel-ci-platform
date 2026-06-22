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
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('chat_sessions')->cascadeOnDelete();
            // user | assistant | tool_call | tool_result
            $table->string('role');
            $table->longText('content')->nullable();
            // Pour les tool_call / tool_result
            $table->string('tool_name')->nullable();
            $table->json('tool_input')->nullable();
            $table->json('tool_result')->nullable();
            $table->string('tool_call_id')->nullable();     // id de corrélation provider
            // Tokens & coût
            $table->unsignedInteger('input_tokens')->default(0);
            $table->unsignedInteger('output_tokens')->default(0);
            $table->decimal('estimated_cost', 10, 6)->default(0);
            $table->timestamps();

            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
