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
        Schema::create('ai_user_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('model_id')->constrained('ai_models')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('role')->nullable();     // membre | moderator | null = tous
            $table->foreignId('assigned_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            // Un user ou un rôle ne peut avoir qu'un seul modèle assigné
            $table->unique(['user_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_user_assignments');
    }
};
