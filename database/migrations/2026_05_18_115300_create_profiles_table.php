<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Table profiles — profil complet du membre
     * Tous les champs sont nullable — sauvegarde progressive
     */
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Avatar uploadé
            $table->string('avatar')->nullable();

            // Localisation
            $table->string('pays')->nullable();
            $table->string('ville')->nullable();
            $table->string('commune')->nullable();

            // Biographie
            $table->text('biographie')->nullable();

            // Profil technique
            $table->enum('niveau_laravel', [
                'debutant',
                'intermediaire',
                'avance',
                'expert',
                'maitre',
            ])->nullable();

            $table->enum('annees_experience', [
                'moins_1_an',
                '1_3_ans',
                '3_5_ans',
                '5_10_ans',
                'plus_10_ans',
            ])->nullable();

            // Stack technique — stockée en JSON
            $table->json('stack_technique')->nullable();

            // Niveau académique
            $table->enum('niveau_academique', [
                'bts',
                'licence',
                'master_ingenieur',
                'doctorat',
            ])->nullable();

            // Situation professionnelle
            $table->enum('poste', [
                'en_fonction',
                'etudiant',
                'entrepreneur',
                'recherche_emploi',
                'freelance',
            ])->nullable();

            // Liens
            $table->string('lien_portfolio')->nullable();

            // CV — PDF, Word ou image
            $table->string('cv')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
