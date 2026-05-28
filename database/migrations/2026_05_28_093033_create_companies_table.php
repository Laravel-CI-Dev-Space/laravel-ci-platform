// database/migrations/2026_05_28_000012_create_companies_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name', 200);
            $table->string('slug', 250)->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->boolean('is_verified')->default(false)->comment('Verified by admin');
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_verified');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
