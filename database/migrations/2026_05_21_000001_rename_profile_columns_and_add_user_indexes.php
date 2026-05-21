<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Renommer les colonnes FR → EN sur profiles
        Schema::table('profiles', function (Blueprint $table) {
            $table->renameColumn('pays', 'country');
            $table->renameColumn('ville', 'city');
            $table->renameColumn('commune', 'district');
            $table->renameColumn('biographie', 'bio');
            $table->renameColumn('niveau_laravel', 'laravel_level');
            $table->renameColumn('annees_experience', 'years_experience');
            $table->renameColumn('stack_technique', 'tech_stack');
            $table->renameColumn('niveau_academique', 'academic_level');
            $table->renameColumn('poste', 'job_status');
            $table->renameColumn('lien_portfolio', 'portfolio_url');
        });

        // Index sur les colonnes de statut fréquemment filtrées
        Schema::table('users', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('suspended_until');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->renameColumn('country', 'pays');
            $table->renameColumn('city', 'ville');
            $table->renameColumn('district', 'commune');
            $table->renameColumn('bio', 'biographie');
            $table->renameColumn('laravel_level', 'niveau_laravel');
            $table->renameColumn('years_experience', 'annees_experience');
            $table->renameColumn('tech_stack', 'stack_technique');
            $table->renameColumn('academic_level', 'niveau_academique');
            $table->renameColumn('job_status', 'poste');
            $table->renameColumn('portfolio_url', 'lien_portfolio');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['suspended_until']);
        });
    }
};
