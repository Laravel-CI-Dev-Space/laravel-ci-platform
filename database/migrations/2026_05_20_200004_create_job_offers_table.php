<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Offres d'emploi — table `job_offers` (la table `jobs` est réservée à la queue Laravel).
     */
    public function up(): void
    {
        Schema::create('job_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('job_categories')->nullOnDelete();
            $table->string('title');
            $table->longText('description');
            $table->string('location')->nullable();
            $table->string('type', 20);
            $table->string('salary')->nullable();
            $table->date('deadline')->nullable();
            $table->string('status', 20)->default('draft');
            $table->timestamp('created_at')->useCurrent();

            $table->index('status');
            $table->index('type');
            $table->index('deadline');
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_offers');
    }
};
