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
        Schema::create('platform_daily_stats', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            // Utilisateurs
            $table->unsignedInteger('new_users')->default(0);
            $table->unsignedInteger('active_users')->default(0);
            $table->unsignedInteger('total_users')->default(0);
            // Contenu
            $table->unsignedInteger('new_questions')->default(0);
            $table->unsignedInteger('new_answers')->default(0);
            $table->unsignedInteger('new_articles')->default(0);
            $table->unsignedInteger('new_comments')->default(0);
            // Emploi
            $table->unsignedInteger('new_job_offers')->default(0);
            $table->unsignedInteger('new_applications')->default(0);
            // Événements
            $table->unsignedInteger('new_registrations')->default(0);
            // Chatbot
            $table->unsignedInteger('total_chat_tokens')->default(0);
            $table->unsignedInteger('total_chat_sessions')->default(0);
            $table->timestamps();

            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_daily_stats');
    }
};
