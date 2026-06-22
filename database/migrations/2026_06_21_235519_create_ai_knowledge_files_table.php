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
        Schema::create('ai_knowledge_files', function (Blueprint $table) {
            $table->id();
            $table->string('type');             // platform | laravel | behavior
            $table->string('label');            // nom affiché en admin
            $table->string('filename');         // nom original du fichier
            $table->string('disk_path');        // chemin storage
            $table->longText('content');        // contenu texte extrait
            $table->boolean('is_active')->default(true);
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_knowledge_files');
    }
};
