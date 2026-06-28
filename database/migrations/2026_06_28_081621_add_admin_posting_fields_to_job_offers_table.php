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
        Schema::table('job_offers', function (Blueprint $table) {
            // Entreprise libre (quand la société n'est pas sur la plateforme)
            $table->string('company_name', 200)->nullable()->after('company_id');

            // Sections riches supplémentaires
            $table->longText('profile_description')->nullable()->after('description');
            $table->longText('tech_stack')->nullable()->after('profile_description');
            $table->longText('benefits')->nullable()->after('tech_stack');

            // Candidature externe
            $table->string('apply_email', 200)->nullable()->after('benefits');
            $table->string('apply_url', 500)->nullable()->after('apply_email');

            // Niveau académique, domaine et expérience
            $table->json('education_levels')->nullable()->after('apply_url');
            $table->json('domains')->nullable()->after('education_levels');
            $table->unsignedTinyInteger('experience_years')->nullable()->after('domains');
        });
    }

    public function down(): void
    {
        Schema::table('job_offers', function (Blueprint $table) {
            $table->dropColumn([
                'company_name', 'profile_description', 'tech_stack', 'benefits',
                'apply_email', 'apply_url', 'education_levels', 'domains', 'experience_years',
            ]);
        });
    }
};
