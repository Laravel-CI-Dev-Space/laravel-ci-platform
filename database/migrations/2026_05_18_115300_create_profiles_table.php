<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Uploaded profile picture (filename only, served from public/assets/avatars)
            $table->string('avatar')->nullable();

            // Location
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('district')->nullable();

            // Biography
            $table->text('bio')->nullable();

            // Technical profile
            $table->enum('laravel_level', [
                'debutant',
                'intermediaire',
                'avance',
                'expert',
                'maitre',
            ])->nullable();

            $table->enum('years_experience', [
                'moins_1_an',
                '1_3_ans',
                '3_5_ans',
                '5_10_ans',
                'plus_10_ans',
            ])->nullable();

            // Tech stack stored as JSON array
            $table->json('tech_stack')->nullable();

            // Academic background
            $table->enum('academic_level', [
                'bts',
                'licence',
                'master_ingenieur',
                'doctorat',
            ])->nullable();

            // Professional situation
            $table->enum('job_status', [
                'en_fonction',
                'etudiant',
                'entrepreneur',
                'recherche_emploi',
                'freelance',
            ])->nullable();

            // Links
            $table->string('portfolio_url')->nullable();

            // CV — PDF, Word or image (filename only, served from public/assets/cv)
            $table->string('cv')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
