// database/migrations/2026_05_28_000015_create_job_skills_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_skills', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->unsignedInteger('offers_count')->default(0);
            $table->timestamps();
        });

        Schema::create('job_offer_skill', function (Blueprint $table) {
            $table->foreignId('job_offer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_skill_id')->constrained()->cascadeOnDelete();
            $table->primary(['job_offer_id', 'job_skill_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_offer_skill');
        Schema::dropIfExists('job_skills');
    }
};
