// database/migrations/2026_05_28_000013_create_job_offers_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('posted_by')->constrained('users')->cascadeOnDelete();
            $table->string('title', 200);
            $table->string('slug', 250)->unique();
            $table->longText('description');
            $table->enum('contract_type', ['cdi', 'cdd', 'freelance', 'internship', 'apprenticeship']);
            $table->enum('level', ['junior', 'intermediate', 'senior', 'lead', 'any']);
            $table->string('location', 150)->nullable();
            $table->string('country', 100)->nullable();
            $table->boolean('is_remote')->default(false);
            $table->boolean('is_hybrid')->default(false);
            $table->unsignedInteger('salary_min')->nullable()->comment('In local currency');
            $table->unsignedInteger('salary_max')->nullable();
            $table->string('currency', 10)->default('XOF');
            $table->boolean('salary_visible')->default(true);
            $table->enum('status', ['draft', 'pending', 'active', 'expired', 'rejected', 'filled'])->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_urgent')->default(false);
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('applications_count')->default(0)->comment('Cached count');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('contract_type');
            $table->index('level');
            $table->index('is_remote');
            $table->index('is_urgent');
            $table->index('expires_at');
            $table->index(['status', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_offers');
    }
};
