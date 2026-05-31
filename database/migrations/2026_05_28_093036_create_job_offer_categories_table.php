// database/migrations/2026_05_28_000014_create_job_offer_categories_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_offer_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('icon', 50)->nullable();
            $table->unsignedInteger('offers_count')->default(0);
            $table->timestamps();
        });

        Schema::create('job_offer_category', function (Blueprint $table) {
            $table->foreignId('job_offer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_offer_category_id')->constrained()->cascadeOnDelete();
            $table->primary(['job_offer_id', 'job_offer_category_id'], 'job_offer_category_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_offer_category');
        Schema::dropIfExists('job_offer_categories');
    }
};
