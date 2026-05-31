// database/migrations/2026_05_28_000016_create_job_applications_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_offer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('cv_path')->nullable()->comment('Uploaded CV path in public/assets/cv/');
            $table->text('cover_letter')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->enum('status', ['pending', 'viewed', 'shortlisted', 'accepted', 'rejected'])->default('pending');
            $table->text('employer_note')->nullable()->comment('Private note from employer');
            $table->timestamp('viewed_at')->nullable();
            $table->timestamps();

            $table->unique(['job_offer_id', 'user_id']);
            $table->index('status');
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
